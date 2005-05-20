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

require_once 'CRM/Core/Page.php';

class CRM_Custom_Page_Group extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     */
    private static $_actionLinks;

    function &actionLinks()
    {
        if (!isset(self::$_actionLinks)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this custom data group?');
            self::$_actionLinks = array(
                                        CRM_Core_Action::UPDATE  => array(
                                                                          'name'  => ts('Edit'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=update&id=%%id%%',
                                                                          'title' => ts('Edit Custom Group') 
                                                                          ),
                                        CRM_Core_Action::DISABLE => array(
                                                                          'name'  => ts('Disable'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=disable&id=%%id%%',
                                                                          'title' => ts('Disable Custom Group'),
                                                                          'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                          ),
                                        CRM_Core_Action::ENABLE  => array(
                                                                          'name'  => ts('Enable'),
                                                                          'url'   => 'civicrm/admin/custom/group',
                                                                          'qs'    => 'action=enable&id=%%id%%',
                                                                          'title' => ts('Enable Custom Group'),
                                                                          ),
                                        CRM_Core_Action::BROWSE  => array(
                                                                          'name'  => ts('List/Edit Fields'),
                                                                          'url'   => 'civicrm/admin/custom/group/field',
                                                                          'qs'    => 'reset=1&action=browse&gid=%%id%%',
                                                                          'title' => ts('List Custom Group Fields'),
                                                                          ),
                                        );
        }
        return self::$_actionLinks;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action. 
     *
     * @param none
     * @return none
     * @access public
     *
     */
    function run()
    {

        CRM_Core_Error::le_method();

        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD)) {
            $this->edit($id, $action) ;
        } else if ($action & CRM_Core_Action::DISABLE) {
            CRM_Core_BAO_CustomGroup::setIsActive($id, 0);
        } else if ($action & CRM_Core_Action::ENABLE) {
            CRM_Core_BAO_CustomGroup::setIsActive($id, 1);
        } 
        $this->browse();

        // Call the parents run method
        parent::run();

        CRM_Core_Error::ll_method();
    }


    /**
     * edit custom group
     *
     * @param string $action the action to be invoked

     * @return none
     * @access public
     */
    function edit($id, $action)
    {

        CRM_Core_Error::le_method();

        // create a simple controller for editing custom data
        $controller = new CRM_Core_Controller_Simple('CRM_Custom_Form_Group', ts('Custom Group'), $action);

        // set the userContext stack
        $session = CRM_Core_Session::singleton();
        $session->pushUserContext(CRM_Utils_System::url('civicrm/admin/custom/group/', 'action=browse'));
        $controller->set('id', $id);
        $controller->setEmbedded(true);
        $controller->process();
        $controller->run();

        CRM_Core_Error::ll_method();
    }


    /**
     * Browse all custom data groups.
     *
     * @param none
     * @return none
     * @access public
     * @static
     */
    function browse($action=null)
    {
        $customGroup = array();

        CRM_Core_Error::le_method();

        $customGroupBAO = new CRM_Core_BAO_CustomGroup();
        $customGroupBAO->orderBy('weight');
        $customGroupBAO->find();

        // shld use it but it's an abstract class right now.
        // $basicPage = new CRM_Core_Page_Basic();
        while ($customGroupBAO->fetch()) {
            $customGroup[$customGroupBAO->id] = array();
            $customGroupBAO->storeValues($customGroup[$customGroupBAO->id]);

            $action = array_sum(array_keys($this->actionLinks()));
            if ($customGroupBAO->is_active) {
                $action -= CRM_Core_Action::ENABLE;
            } else {
                $action -= CRM_Core_Action::DISABLE;
            }
            $customGroup[$customGroupBAO->id]['action'] = CRM_Core_Action::formLink(self::actionLinks(), $action, 
                                                                                    array('id' => $customGroupBAO->id));
            //$basicPage($customGroupBAO, $action, $customGroup[$customGroupBAO->id], $actionLinks);
        }
        $this->assign('rows', $customGroup);
    }
}
?>