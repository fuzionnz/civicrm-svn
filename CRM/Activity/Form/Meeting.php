<?php
/**
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Activity/Form.php';

/**
 * This class generates form components for Meeting
 * 
 */
class CRM_Activity_Form_Meeting extends CRM_Activity_Form
{

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $this->applyFilter('__ALL__', 'trim');
       
        $this->add('text', 'subject', ts('Subject') , CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'subject' ) );
        $this->addRule( 'subject', ts('Please enter a valid subject.'), 'required' );

        $this->addElement('date', 'scheduled_date_time', ts('Schedule Date'), CRM_Core_SelectValues::date());
        $this->addRule('scheduled_date_time', ts('Select a valid date.'), 'qfDate');
        
        $this->add('select','duration_hours',ts('Duration'),CRM_Core_SelectValues::getHours());
        $this->add('select','duration_minutes',ts('Min'),CRM_Core_SelectValues::getMinutes());

        $this->add('text', 'location', ts('Location'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'location' ) );
        
        $this->add('textarea', 'details', ts('Details'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_Meeting', 'details' ) );
        
        $this->add('select','status',ts('Status'),CRM_Core_SelectValues::activityStatus());

        parent::buildQuickForm( );
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );

        $ids = array();

        // store the contact id and current drupal user id
        $params['source_contact_id'] = $this->_userId;
        $params['target_contact_id'] = $this->_contactId;
        
        if ($this->_action & CRM_Core_Action::UPDATE ) {
            $ids['meeting'] = $this->_id;
        }

        $meeting = CRM_Core_BAO_Meeting::add($params, $ids);

        CRM_Core_Session::setStatus( ts('Meeting "%1" has been saved.', array( 1 => $meeting->subject)) );
    }//end of function


}

?>
