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

require_once 'CRM/Core/Page/Basic.php';

class CRM_Admin_Page_Category extends CRM_Core_Page_Basic 
{

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links;

    function getBAOName() 
    {
        return 'CRM_Contact_BAO_Category';
    }

    function &links() 
    {
        if ( ! isset( self::$_links ) ) 
        {
            // helper variable for nicer formatting
            $deleteExtra = ts('Are you sure you want to delete this category?\n\nThis category will be removed from any currently tagged contacts, and users will no longer be able to assign contacts to this category.');

	    self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/category',
                                                                    'qs'    => 'action=update&id=%%id%%',
                                                                    'title' => ts('Edit Category') 
                                                                   ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/category',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'extra'    => 'onclick = "return confirm(\'' . $deleteExtra . '\');"',
                                                                    'title' => ts('Delete Category'), 
                                                                   ),
                                 );
        }
        return self::$_links;
    }

    function editForm() 
    {
        return 'CRM_Admin_Form_Category';
    }

    function editName() 
    {
        return 'Category';
    }

    function deleteName() 
    {
        return 'Category';
    }

    function userContext( $mode = null ) 
    {
        return 'civicrm/admin/category';
    }

   function deleteForm() 
   {
        return 'CRM_Admin_Form_Category';
   }
}

?>