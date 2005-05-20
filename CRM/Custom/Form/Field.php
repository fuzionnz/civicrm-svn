<?php
/*
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * form to process actions on the field aspect of Custom
 */
class CRM_Custom_Form_Field extends CRM_Core_Form {
    /**
     * the custom group id saved to the session for an update
     *
     * @var int
     */
    protected $_gid;

    /**
     * The field id, used when editing the field
     *
     * @var int
     */
    protected $_id;


    /**
     * Array for valid combinations of data_type & html_type
     *
     * @var array
     * @static
     */
    public static $dataType = array('String', 'Int', 'Float', 'Money', 'Text', 'Date', 'Boolean', 'Set', 'Enum', 'Array');
    public static $dataToHTML = array(
                                      array('Text'),
                                      array('Text'),
                                      array('Text'),
                                      array('Text'),
                                      array('TextArea'),
                                      array('Select Date'),
                                      array('Radio'),
                                      array('Checkbox'),
                                      array('Radio'),
                                      array('Checkbox', 'Radio', 'Select'),
                                      );
    
    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this);
        $this->_id  = CRM_Utils_Request::retrieve('id' , $this);
    }

    /**
    * This function sets the default values for the form. Note that in edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues()
    {
        $defaults = array();
        
        if (isset($this->_id)) {
            $params = array('id' => $this->_id);
            CRM_Core_BAO_CustomField::retrieve($params, $defaults);
            $this->_gid = $defaults['custom_group_id'];
        } else {
            $defaults['is_active'] = 1;
        }
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->applyFilter('__ALL__', 'trim');
        // label
        $this->add( 'text', 'label', ts('Field Label'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'label'), true);
        $this->addRule('label', ts('Please enter label for this field.'), 'title');

        // data type, html type
        $dataHTMLElement = $this->addElement('hierselect', 'data_type', ts('Data Type'));
        $dataHTMLElement->setOptions(array(self::$dataType, self::$dataToHTML));

        // weight
        $this->add('text', 'weight', ts('Weight'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'default_value'), true);
        $this->addRule('weight', ts(' is a numeric field') , 'numeric');
        
        // is required ?
        $this->add('checkbox', 'is_required', ts('Required?') );

        // default value, help pre, help post, mask, attributes, javascript ?
        $this->add('text', 'default_value', ts('Default Value'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'default_value'));
        $this->add('textarea', 'help_pre', ts('Help Pre'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'help_pre'));        
        $this->add('textarea', 'help_post', ts('Help Post'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'help_post'));        
        $this->add('text', 'mask', ts('Mask'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'mask'));        
        
        // hack: we use fattributes since QF uses attributes variable for the form!
        $this->add('text', 'fattributes', ts('Attributes'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'attributes'));       
        $this->add('text', 'javascript', ts('Javascript'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_CustomField', 'javascript'));       
        
        // is active ?
        $this->add('checkbox', 'is_active', ts('Active?'));
        
        $this->addButtons(array(
                                array ('type'      => 'next',
                                       'name'      => 'Save',
                                       'isDefault' => true),
                                array ('type'      => 'reset',
                                       'name'      => 'Reset'),
                                array ('type'      => 'cancel',
                                       'name'      => 'Cancel'),
                                )
                          );
        if ($this->_mode & self::MODE_VIEW) {
            $this->freeze();
            $this->addElement('button', 'done', ts('Done'), array('onClick' => "location.href='civicrm/admin/custom/group/field?reset=1&action=browse&gid=" . $this->_gid . "'"));
        }
    }
    
    /**
     * Process the form
     *
     * @return void
     * @access public
     */
    public function postProcess()
    {
        // store the submitted values in an array
        $params = $this->controller->exportValues('Field');

        // set values for object properties
        $customField                = new CRM_Core_DAO_CustomField( );
        $customField->label         = $params['label'];
        $customField->name          = CRM_Utils_String::titleToVar( $params['label'] );
        $customField->data_type     = $params['data_type'];
        $customField->html_type     = $params['html_type'];
        $customField->weight        = $params['weight'];
        $customField->default_value = $params['default_value'];
        $customField->help_pre      = $params['help_pre'];
        $customField->help_post     = $params['help_post'];
        $customField->mask          = $params['mask'];
        $customField->attributes    = $params['fattributes'];
        $customField->javascript    = $params['javascript'];
        $customField->is_required   = CRM_Utils_Array::value( 'is_required', $params, false );
        $customField->is_active     = CRM_Utils_Array::value( 'is_active', $params, false );

        if ($this->_mode & self::MODE_UPDATE) {
            $customField->id = $this->_id;
        }

        $customField->custom_group_id = $this->_gid;
        $customField->save();

        CRM_Core_Session::setStatus( ts('Your custom field - \' %1 \' has been saved', 
                                        array( 1 => $customField->label ) ) );
    }
}
?>