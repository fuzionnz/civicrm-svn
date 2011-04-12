<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Grant/Form/Task.php';

/**
 * This class provides the functionality to update a group of
 * grants. This class provides functionality for the actual
 * update.
 */
class CRM_Grant_Form_Task_Update extends CRM_Grant_Form_Task 
{
    /**
     * Are we operating in "single mode", i.e. updating one
     * specific grant?
     *
     * @var boolean
     */
    protected $_single = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        parent::preProcess( );

        //check permission for update.
        if ( !CRM_Core_Permission::checkActionPermission( 'CiviGrant', CRM_Core_Action::UPDATE ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );  
        }
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        require_once 'CRM/Grant/PseudoConstant.php';
        $grantStatus = CRM_Grant_PseudoConstant::grantStatus();
        $this->addElement('select', 'status_id', ts('Grant Status'), array( '' => '' ) + $grantStatus);

        $this->addElement('text', 'amount_granted', ts('Amount Granted') );
        $this->addRule('amount_granted', ts('Please enter a valid amount.'), 'money'); 

        $this->addDate( 'decision_date', ts('Grant Decision'), false, array( 'formatType' => 'custom') );

        $this->assign( 'elements', array( 'status_id', 'amount_granted', 'decision_date' ) );
        $this->assign( 'totalSelectedGrants', count($this->_grantIds) );

        $this->addDefaultButtons( ts( 'Update Grants' ), 'done' );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $updatedGrants = 0;

        // get the submitted form values.  
        $params = $this->controller->exportValues( $this->_name );
        $qfKey = $params['qfKey'];
        foreach ( $params as $key => $value ) {
            if ( $value == '' || $key == 'qfKey' ) unset( $params[$key] );
        }
        if ( ! empty($params) ) {
            foreach ( $this->_grantIds as $grantId ) {
                require_once 'CRM/Grant/DAO/Grant.php';
                $grant  = new CRM_Grant_DAO_Grant( );
                $grant->id = $grantId;
                if ( $grant->find( true ) ) {
    
                    // get existing grant and update fields from the form
                    $values = array();
                    CRM_Core_DAO::storeValues( $grant, $values );
                    foreach ( $params as $key => $value ) {
                        $values[$key] = $value;
                    }
                    // convert dates to mysql format
                    $dates = array( 'application_received_date',
                                    'decision_date',
                                    'money_transfer_date',
                                    'grant_due_date' );
                    foreach ( $dates as $d ) {
                        if ( $values[$d] ) {
                            $values[$d] = CRM_Utils_Date::processDate( $values[$d], null, true );
                        }
                    }

                    require_once 'CRM/Grant/BAO/Grant.php';
                    $ids['grant'] = $grant->id ;
                    CRM_Grant_BAO_Grant::add($values, $ids);
                    $updatedGrants++;
                }
            }
        }

        $status = array(
                        ts( 'Updated Grant(s): %1',        array( 1 => $updatedGrants ) ),
                        ts( 'Total Selected Grant(s): %1', array( 1 => count($this->_grantIds ) ) ),
                        );
        CRM_Core_Session::setStatus( $status );
        CRM_Utils_System::redirect( CRM_Utils_System::url( 'civicrm/grant/search', 'force=1&qfKey=' . $qfKey ) );
    }
}
