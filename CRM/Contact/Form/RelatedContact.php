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

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';

/**
 * This class generates form components generic to all the contact types.
 * 
 * It delegates the work to lower level subclasses and integrates the changes
 * back in. It also uses a lot of functionality with the CRM API's, so any change
 * made here could potentially affect the API etc. Be careful, be aware, use unit tests.
 *
 */
class CRM_Contact_Form_RelatedContact extends CRM_Core_Form
{
    /**
     * The contact type of the form
     *
     * @var string
     */
    protected $_contactType;

    /**
     * The contact id, used when editing the form
     *
     * @var int
     */
    public $_contactId;
    
    /**
     * form defaults
     *
     * @var array
     */
    protected $_defaults = array( );

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        // reset action from the session
        $this->_action      = CRM_Utils_Request::retrieve( 'action', 'String', 
                                                           $this, false, 'update' );
        $this->_contactId   = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
        
        if ( $this->_contactId ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $contact =& new CRM_Contact_DAO_Contact( );
            $contact->id = $this->_contactId;
            if ( ! $contact->find( true ) ) {
                CRM_Core_Error::statusBounce( ts('contact does not exist: %1', array(1 => $this->_contactId)) );
            }
            $this->_contactType    = $contact->contact_type;
           
            // check for permissions
            require_once 'CRM/Contact/BAO/Contact/Permission.php';
            if ( ! CRM_Contact_BAO_Contact_Permission::allow( $this->_contactId, CRM_Core_Permission::EDIT ) ) {
                CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to edit this contact.') );
            }
            
            list( $displayName, $contactImage ) = CRM_Contact_BAO_Contact::getDisplayAndImage( $this->_contactId );
            CRM_Utils_System::setTitle( $displayName, $contactImage . ' ' . $displayName ); 
        } else {
            CRM_Core_Error::statusBounce( ts('Could not get a contact_id and/or contact_type') );
        }
    }
    

    /**
     * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( ) 
    {
        return $this->_defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $params       = array( );
        $params['id'] = $params['contact_id'] = $this->_contactId;
        $contact = CRM_Contact_BAO_Contact::retrieve( $params, $this->_defaults );

        $address   = CRM_Utils_Array::value( 'address',
                                             $this->_defaults['location'][1] );
        $countryID = CRM_Utils_Array::value( 'country_id',
                                             $address );
        $stateID   = CRM_Utils_Array::value( 'state_province_id',
                                             $address );
        CRM_Contact_BAO_Contact_Utils::buildOnBehalfForm($this,
                                                         $this->_contactType, 
                                                         $countryID,
                                                         $stateID,
                                                         'Contact Information',
                                                         true );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
    }
    
    
    /**
     * Form submission of new/edit contact is processed.
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        $params['location'][1]['is_primary'] = 1;
	    $params['contact_type']              = $this->_contactType;
        $params['contact_id']                = $this->_contactId;

        require_once 'CRM/Contact/BAO/Contact.php';
        $contact =& CRM_Contact_BAO_Contact::create($params, true, false );
        
        if ( $this->_contactType == 'Household' && ( $this->_action & CRM_Core_Action::UPDATE ) ) {
            require_once 'CRM/Contact/Form/Household.php';
            CRM_Contact_Form_Household::synchronizeIndividualAddresses( $contact->id );
        }

        // set status message.
        CRM_Core_Session::setStatus(ts('Your %1 contact record has been saved.', 
                                       array(1 => $contact->contact_type_display)));
    }
 
}


