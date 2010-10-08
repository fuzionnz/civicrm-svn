<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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
 * This class stores logic for managing CiviCRM extensions.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Extensions.php';

class CRM_Core_Extensions_Extension
{

    /**
     * 
     */
    const OPTION_GROUP_NAME = 'system_extensions';

    public $type = null;
    
    public $path = null;
    
    function __construct( $key, $type = null, $name = null, $label = null, $file = null, $is_active = 0 ) {
        $this->key = $key;
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
        $this->file = $file;
        $this->is_active = $is_active;
        
        $config =& CRM_Core_Config::singleton( );
        $this->path = $config->extensionsDir . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR;
    }

    public function setId( $id ) {
        $this->id = $id;
    }

    public function readXMLInfo( ) {
        $info = $this->_parseXMLFile( $this->path . 'info.xml' );
        $this->type = (string) $info->attributes()->type;
        $this->file = (string) $info->file;
        $this->label = (string) $info->name;

        foreach( $info as $attr => $val ) {
            if( count($val->children()) == 0 ) {
                $this->$attr = (string) $val;
            } else {
                $this->$attr = (array) $val;
            }
        }
    }

    private function _parseXMLFile( $file ) {
        if( file_exists( $file ) ) {
            $dom = DomDocument::load( $file );
            if( $dom ) {
                $dom->xinclude( );
                return simplexml_import_dom( $dom );
            }
        } else {
            CRM_Core_Error::fatal( 'Extension directory ' . $file . ' does not exist.' );
        }
        return array();
    }
    
    public function install( ) {
        $this->_registerExtensionByType();
        $this->_createExtensionEntry();
    }
    
    public function uninstall( ) {
        $this->_removeExtensionByType();
        $this->_removeExtensionEntry();
    }    

    private function _registerExtensionByType() {
        $hcName = "CRM_Core_Extensions_" . ucwords($this->type);
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $hcName) . '.php');
        $ext = new $hcName( $this );
        $ext->install();
    }
    
    private function _removeExtensionByType() {
        $hcName = "CRM_Core_Extensions_" . ucwords($this->type);
        require_once(str_replace('_', DIRECTORY_SEPARATOR, $hcName) . '.php');
        $ext = new $hcName( $this );
        $ext->uninstall();
    }    

    private function _removeExtensionEntry() {
        CRM_Core_BAO_OptionValue::del($this->id);
        CRM_Core_Session::setStatus( ts('Selected option value has been deleted.') );
    }
    
    private function _createExtensionEntry() {
        $groupId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', self::OPTION_GROUP_NAME, 'id', 'name' );
        $weight = CRM_Utils_Weight::getDefaultWeight( 'CRM_Core_DAO_OptionValue', array( 'option_group_id' => $groupId) );
            
        $params = array( 'option_group_id' => $groupId,
                         'weight' => $weight,
                         'label' => $this->label,
                         'name'  => $this->name,
                         'value' => $this->key,
                         'grouping' => $this->type,
                         'description' => $this->file,
                         'is_active' => 1
                      );

        $ids = array();
        $optionValue = CRM_Core_BAO_OptionValue::add($params, $ids);    
    }
    

}