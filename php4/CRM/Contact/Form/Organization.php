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


require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/DAO.php';
require_once 'CRM/Contact/Form/Edit.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/SelectValues.php';
require_once 'CRM/Core/ShowHideBlocks.php';

/**
 * Auxilary class to provide support to the Contact Form class. Does this by implementing
 * a small set of static methods
 *
 */
class CRM_Contact_Form_Organization extends CRM_Core_Form 
{
    /**
     * This function provides the HTML form elements that are specific to this Contact Type
     *
     * @access public
     * @return None
     */
     function buildQuickForm( &$form ) {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Organization');

        $this->applyFilter('__ALL__','trim');
        
        // Organization_name
        $this->add('text', 'organization_name', 'Organization Name', $attributes['organization_name']);
        
        // legal_name
        $this->addElement('text', 'legal_name', 'Legal Name', $attributes['legal_name']);

        // nick_name
        $this->addElement('text', 'nick_name', 'Nick Name', $attributes['nick_name']);

        // sic_code
        $this->addElement('text', 'sic_code', 'SIC Code', $attributes['sic_code']);
    }

     function formRule( &$fields ) {
        $errors = array( );
        
        $primaryEmail = CRM_Contact_Form_Edit::formRule( $fields, $errors );
        
        // make sure that organization name is set
        if (! CRM_Utils_Array::value( 'organization_name', $fields ) ) {
            $errors['organization_name'] = 'Organization Name should be set.';
        }

        // add code to make sure that the uniqueness criteria is satisfied
        return empty( $errors ) ? true : $errors;
    }
}


    
?>