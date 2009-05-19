<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Upgrade/Form.php';
require_once 'CRM/Core/BAO/Domain.php';
require_once 'CRM/Utils/System.php';

class CRM_Upgrade_Page_Upgrade extends CRM_Core_Page {
    function preProcess( ) {
        parent::preProcess( );
    }

    function run( ) {
        $latestVer  = CRM_Utils_System::version();
        $currentVer = CRM_Core_BAO_Domain::version();

        // hack to make past ver compatible /w new incremental upgrade process
        $convertVer = array( '2.1'      => '2.1.0',
                             '2.2'      => '2.2.alpha1',
                             '2.2.alph' => '2.2.alpha3',
                             );
        if ( isset($convertVer[$currentVer]) ) {
            $currentVer = $convertVer[$currentVer];
        }
        
        // This could be removed in later rev
        if ( $currentVer == '2.1.6' ) {
            $config =& CRM_Core_Config::singleton( );
            // also cleanup the templates_c directory
            $config->cleanup( 1 );
            
            if ( $config->userFramework !== 'Standalone' ) {
                // clean the session
                $session =& CRM_Core_Session::singleton( );
                $session->reset( 2 );
            }
        }
        // end of hack
        
        CRM_Utils_System::setTitle(ts('Upgrade CiviCRM to Version %1', 
                                      array( 1 => $latestVer )));
        
        $upgrade  =& new CRM_Upgrade_Form( );

        $template =& CRM_Core_Smarty::singleton( );
        $template->assign( 'pageTitle', ts('Upgrade CiviCRM to Version %1', 
                                           array( 1 => $latestVer )));
        $template->assign( 'menuRebuildURL', 
                           CRM_Utils_System::url( 'civicrm/menu/rebuild', 'reset=1' ) );
        $template->assign( 'cancelURL', 
                          CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' ) );

        if ( version_compare($currentVer, $latestVer) >= 0 ) {
            $message = ts( 'Your database has already been upgraded to CiviCRM %1',
                           array( 1 => $latestVer ) );
            $template->assign( 'upgraded', true );
        } else {
            $message   = ts('CiviCRM upgrade was successful.');
            $template->assign( 'currentVersion',  $currentVer);
            $template->assign( 'newVersion',      $latestVer );
            $template->assign( 'upgradeTitle',   ts('Upgrade CiviCRM from v %1 To v %2', 
                                                    array( 1=> $currentVer, 2=> $latestVer ) ) );
            $template->assign( 'upgraded', false );

            if ( CRM_Utils_Array::value('upgrade', $_POST) ) {
                $revisions = $upgrade->getRevisionSequence();
                foreach ( $revisions as $rev ) {
                    // proceed only if $currentVer < $rev
                    if ( version_compare($currentVer, $rev) < 0 ) {
                        
                        $phpFunctionName = 'upgrade_' . str_replace( '.', '_', $rev );
                        if ( is_callable(array($this, $phpFunctionName)) ) {
                            eval("\$this->{$phpFunctionName}('$rev');");
                        } else {
                            $upgrade->processSQL( $rev );
                        }
                        $upgrade->setVersion( $rev );
                    }
                }
                $upgrade->setVersion( $latestVer );
                $template->assign( 'upgraded', true );
                
                // also cleanup the templates_c directory
                $config =& CRM_Core_Config::singleton( );
                $config->cleanup( 1 );
                
                // clean the session. Note: In case of standalone this makes the user logout. 
                // So skip this step for standalone. 
                if ( $config->userFramework !== 'Standalone' ) {
                    $session =& CRM_Core_Session::singleton( );
                    $session->reset( 2 );
                }
            }
        }
        
        $template->assign( 'message', $message );
        $content = $template->fetch( 'CRM/common/success.tpl' );
        echo CRM_Utils_System::theme( 'page', $content, true, $this->_print, false, true );
    }

    function upgrade_2_2_alpha1( $rev ) {
        for ( $stepID = 1; $stepID <= 4; $stepID++ ) {
            require_once "CRM/Upgrade/TwoTwo/Form/Step{$stepID}.php";
            $formName = "CRM_Upgrade_TwoTwo_Form_Step{$stepID}";
            eval( "\$form = new $formName( );" );
            
            $error = null;
            if ( ! $form->verifyPreDBState( $error ) ) {
                if ( ! isset( $error ) ) {
                    $error = "pre-condition failed for current upgrade step $stepID, rev $rev";
                }
                CRM_Core_Error::fatal( $error );
            }
            
            if ( $stepID == 4 ) {
                return;
            }

            $template =& CRM_Core_Smarty::singleton( );

            $eventFees = array( );
            $query = "SELECT og.id ogid FROM civicrm_option_group og WHERE og.name LIKE  %1";
            $params = array( 1 => array(  'civicrm_event_page.amount%', 'String' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
            while ( $dao->fetch( ) ) { 
                $eventFees[$dao->ogid] = $dao->ogid;  
            }
            $template->assign( 'eventFees', $eventFees );    
            
            $form->upgrade( );
            
            if ( ! $form->verifyPostDBState( $error ) ) {
                if ( ! isset( $error ) ) {
                    $error = "post-condition failed for current upgrade step $stepID, rev $rev";
                }
                CRM_Core_Error::fatal( $error );
            }
        }
    }

    function upgrade_2_1_2( $rev ) {
        require_once "CRM/Upgrade/TwoOne/Form/TwoOneTwo.php";
        $formName = "CRM_Upgrade_TwoOne_Form_TwoOneTwo";
        eval( "\$form = new $formName( '$rev' );" );
        
        $error = null;
        if ( ! $form->verifyPreDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = "pre-condition failed for current upgrade for $rev";
            }
            CRM_Core_Error::fatal( $error );
        }

        $form->upgrade( );

        if ( ! $form->verifyPostDBState( $error ) ) {
            if ( ! isset( $error ) ) {
                $error = "post-condition failed for current upgrade for $rev";
            }
            CRM_Core_Error::fatal( $error );
        }
    }

    /**
     * This function should check if if need to skip current sql file
     * Name of this function will change according to the latest release 
     *   
     */
    function upgrade_2_2_alpha3( $rev ) {
        // skip processing sql file, if fresh install -
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup','mail_protocol','id','name' ) ) {
            $upgrade  =& new CRM_Upgrade_Form( );
            $upgrade->processSQL( $rev );
        }
        return true;
    }

    function upgrade_2_2_beta1( $rev ) {
        if ( ! CRM_Core_DAO::checkFieldExists( 'civicrm_pcp_block', 'notify_email' ) ) {
            $template =& CRM_Core_Smarty::singleton( );
            $template->assign( 'notifyAbsent', true );
        }
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }

    function upgrade_2_2_beta2( $rev ) {
        $template =& CRM_Core_Smarty::singleton( );
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 
                                            'CRM_Contact_Form_Search_Custom_ZipCodeRange','id','name' ) ) {
            $template->assign( 'customSearchAbsentAll', true );
        } else if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', 
                                                   'CRM_Contact_Form_Search_Custom_MultipleValues','id','name' ) ) {
            $template->assign( 'customSearchAbsent', true );
        }
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }
    
    function upgrade_2_2_beta3( $rev ) {
        $template =& CRM_Core_Smarty::singleton( );
        if ( ! CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup','custom_data_type','id','name' ) ) {
            $template->assign( 'customDataType', true );
        }
        
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }
    
    function upgrade_2_3_alpha1( $rev ) {
        
        $template = & CRM_Core_Smarty::singleton( );

        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
        
        //delete unnecessary activities 
        require_once 'CRM/Core/OptionGroup.php';
        $bulkEmailID = CRM_Core_OptionGroup::getValue('activity_type', 'Bulk Email', 'name' );
 
        if ( $bulkEmailID ) {

            $mailingActivityIds = array( );
            $query = " 
SELECT max( ca.id ) as aid , ca.source_record_id sid
FROM civicrm_activity ca
WHERE ca.activity_type_id = %1 
GROUP BY ca.source_record_id";
            
            $params = array( 1 => array(  $bulkEmailID, 'Integer' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );

            while ( $dao->fetch( ) ) { 
                
                $updateQuery = "
UPDATE civicrm_activity_target cat, civicrm_activity ca 
SET cat.activity_id = {$dao->aid}  
WHERE ca.source_record_id IS NOT NULL 
AND ca.activity_type_id = %1 AND ca.id <> {$dao->aid} 
AND ca.source_record_id = {$dao->sid} AND ca.id = cat.activity_id";
                
                $updateParams = array( 1 => array(  $bulkEmailID, 'Integer' ) );    
                CRM_Core_DAO::executeQuery( $updateQuery,  $updateParams );
                
                $deleteQuery = " 
DELETE ca.* FROM civicrm_activity ca 
WHERE ca.source_record_id IS NOT NULL 
AND ca.activity_type_id = %1
AND ca.id <> {$dao->aid} AND ca.source_record_id = {$dao->sid}";
                
                $deleteParams = array( 1 => array(  $bulkEmailID, 'Integer' ) );    
                CRM_Core_DAO::executeQuery( $deleteQuery,  $deleteParams );
            }
        }
        
        //CRM-4453
        //lets insert column in civicrm_aprticipant table
        $query  = "
ALTER TABLE `civicrm_participant` ADD `fee_currency` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '3 character string, value derived from config setting.' AFTER `discount_id`";
        CRM_Core_DAO::executeQuery( $query );
        
        //get currency from contribution table if exists/default
        //insert currency when fee_amount != NULL or event is paid.
        $query = "
   SELECT  civicrm_participant.id 
     FROM  civicrm_participant
LEFT JOIN  civicrm_event ON ( civicrm_participant.event_id = civicrm_event.id )
    WHERE  civicrm_participant.fee_amount IS NOT NULL OR civicrm_event.is_monetary = 1";
        
        $participant = CRM_Core_DAO::executeQuery( $query );
        while ( $participant->fetch( ) ) {
            $query = "
SELECT  civicrm_contribution.currency 
  FROM  civicrm_contribution, civicrm_participant_payment
 WHERE  civicrm_contribution.id = civicrm_participant_payment.contribution_id
   AND  civicrm_participant_payment.participant_id = {$participant->id}";
            $currencyID = CRM_Core_DAO::singleValueQuery( $query );
            if ( !$currencyID ) {
                $config =& CRM_Core_Config::singleton( ); 
                $currencyID = $config->defaultCurrency;
            }
            
            //finally update participant record.
            CRM_Core_DAO::setFieldValue( 'CRM_Event_DAO_Participant', $participant->id, 'fee_currency', $currencyID );
        }
    }
    
}

