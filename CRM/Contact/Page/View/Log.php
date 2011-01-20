<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';

class CRM_Contact_Page_View_Log extends CRM_Core_Page {

   /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) {
        
        $useLogging = false;

        require_once 'CRM/Logging/Schema.php';
        $loggingSchema = new CRM_Logging_Schema( );
        if ( $loggingSchema->isEnabled() ) {
            require_once 'CRM/Report/BAO/Instance.php';
            $params   = array( 'report_id' => 'logging/contact/summary' );
            $instance = array( );
            CRM_Report_BAO_Instance::retrieve($params, $instance);
            
            if ( !empty($instance) &&
                 ( !CRM_Utils_Array::value('permission', $instance) ||
                   ( CRM_Utils_Array::value('permission', $instance) && CRM_Core_Permission::check( $instance['permission'] ) ) ) ) {
                $this->assign( 'instanceUrl',  CRM_Utils_System::url( "civicrm/report/instance/{$instance['id']}", "reset=1&force=1&snippet=4&section=2&id_op=eq&id_value={$this->_contactId}&cid={$this->_contactId}", false, null, false ) );
                $useLogging = true;
            }
            
        }
        $this->assign( 'useLogging', $useLogging );
        
        if ( $useLogging ) {
            return;
        }
        
        require_once 'CRM/Core/DAO/Log.php';

        $log = new CRM_Core_DAO_Log( );
        
        $log->entity_table = 'civicrm_contact';
        $log->entity_id    = $this->_contactId;
        $log->orderBy( 'modified_date desc' );
        $log->find( );

        $logEntries = array( );
        while ( $log->fetch( ) ) {
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $log->modified_id );
            $logEntries[] = array( 'id'    => $log->modified_id,
                                   'name'  => $displayName,
                                   'image' => $contactImage,
                                   'date'  => $log->modified_date );
        }

        $this->assign( 'logCount', count( $logEntries ) );
        $this->assign_by_ref( 'log', $logEntries );
    }

    function preProcess() {
        $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        $this->assign( 'contactId', $this->_contactId );

        // check logged in url permission
        require_once 'CRM/Contact/Page/View.php';
        CRM_Contact_Page_View::checkUserPermission( $this );
        
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, false, 'browse');
        $this->assign( 'action', $this->_action);
    }

   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) {
        $this->preProcess( );

        $this->browse( );

        return parent::run( );
    }

}


