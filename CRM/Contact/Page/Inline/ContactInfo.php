<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */

/**
 * Page to disply contact information on topi of summary 
 *
 */
class CRM_Contact_Page_Inline_ContactInfo {

  /**
   * Run the page.
   *
   * This method is called after the page is created.
   *
   * @return void
   * @access public
   *
   */
  function run() {
    // get the emails for this contact
    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject, TRUE, NULL, $_REQUEST);

    $params = array(
      'id' => $contactId
    );

    $defaults = array();
    CRM_Contact_BAO_Contact::getValues( $params, $defaults );

    //get the current employer name
    if (CRM_Utils_Array::value('contact_type', $defaults) == 'Individual') {
      if (CRM_Utils_Array::value('employer_id', $defaults) &&
        CRM_Utils_Array::value('organization_name', $defaults)) {
        $defaults['current_employer'] = $defaults['organization_name'];
        $defaults['current_employer_id'] = $defaults['employer_id'];
      }
    }

    $template = CRM_Core_Smarty::singleton();
    $template->assign('contactId', $contactId);
    $template->assign($defaults);
    
    // check logged in user permission
    $page = new CRM_Core_Page();
    CRM_Contact_Page_View::checkUserPermission($page, $contactId);
    $template->assign($page);
 
        echo $content = $template->fetch('CRM/Contact/Page/Inline/ContactInfo.tpl');
    CRM_Utils_System::civiExit();
  }
}

