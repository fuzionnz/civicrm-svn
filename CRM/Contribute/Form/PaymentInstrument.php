<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contribute/Form.php';
require_once 'CRM/Core/BAO/OptionGroup.php';
require_once 'CRM/Core/BAO/OptionValue.php';


/**
 * This class generates form components for Payment Instrument
 * 
 */
class CRM_Contribute_Form_PaymentInstrument extends CRM_Contribute_Form
{

    /**
     * The id of the object being edited / created
     *
     * @var int
     */
    protected $_optionGroupID;
    
    function preProcess( ) {
        parent::preProcess( );
        
        $groupParams = array( 'name' => 'payment_instrument' );
        $optionGroup = CRM_Core_BAO_OptionGroup::retrieve($groupParams, $defaults);

        $this->_optionGroupID = $optionGroup->id;
        
     }


    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) 
    {
        parent::buildQuickForm( );
        
        if ($this->_action & CRM_Core_Action::DELETE ) { 
            return;
        }

        $this->applyFilter('__ALL__', 'trim');
        $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue', 'name' ) );
        $this->addRule( 'name', ts('Please enter a valid Payment Instrument name.'), 'required' );
        $this->addRule( 'name', ts('Name already exists in Database.'), 'objectExists', array( 'CRM_Core_DAO_OptionValue', $this->_id ) );
        
        $this->add('text', 'description', ts('Description'), CRM_Core_DAO::getAttribute( 'CRM_Core_DAO_OptionValue', 'description' ) );

        $this->add('checkbox', 'is_active', ts('Enabled?'));

        if ($this->_action == CRM_Core_Action::UPDATE && CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $this->_id, 'is_reserved' )) { 
            $this->freeze(array('name', 'description', 'is_active' ));
        }
        
    }

       
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
      
        if($this->_action & CRM_Core_Action::DELETE) {
            CRM_Core_BAO_OptionValue::del($this->_id);
            CRM_Core_Session::setStatus( ts('Selected Payment Instrument has been deleted.') );
        } else { 

            $params = $ids = array( );
            // store the submitted values in an array
            $params = $this->exportValues();
            $groupParams = array( 'name' => 'payment_instrument' );
            require_once 'CRM/Core/OptionValue.php';
            $optionValue =  CRM_Core_OptionValue::addOptionValue($params, $groupParams, $this->_action, $this->_id);
            CRM_Core_Session::setStatus( ts('The Payment Instrument "%1" has been saved.', array( 1 => $optionValue->name )) );
        }
    }
}

?>
