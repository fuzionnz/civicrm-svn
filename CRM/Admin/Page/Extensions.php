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
 * This is a part of CiviCRM extension management functionality.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * This page displays the list of extensions registered in the system.
 */
class CRM_Admin_Page_Extensions extends CRM_Core_Page_Basic 
{

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    static $_extensions = null;

    static $_extEnabled = TRUE;

    /**
     * Obtains the group name from url and sets the title.
     *
     * @return void
     * @access public
     *
     */
    function preProcess( )
    {

        require_once 'CRM/Core/Extensions.php';

        $ext = new CRM_Core_Extensions();

        self::$_extEnabled = $ext->extensionsEnabled();
        self::$_extensions = $ext->getExtensions();

        CRM_Utils_System::setTitle(ts('CiviCRM Extensions'));
            
    }

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Core_BAO_OptionValue';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            self::$_links = array(
                                  CRM_Core_Action::ADD     => array(
                                                                    'name'  => ts('Install'),
                                                                    'url'   => 'civicrm/admin/extensions',
                                                                    'qs'    => 'action=add&id=%%id%%&key=%%key%%',
                                                                    'title' => ts('Install')
                                                                    ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'extra' => 'onclick = "enableDisable( \'%%id%%\',\''. 'CRM_Core_Extensions' . '\',\'' . 'disable-enable' . '\' );"',
                                                                    'ref'   => 'enable-action',
                                                                    'title' => ts('Enable')
                                                                    ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'extra' => 'onclick = "enableDisable( \'%%id%%\',\''. 'CRM_Core_Extensions' . '\',\'' . 'enable-disable' . '\' );"',
                                                                    'ref'   => 'disable-action',
                                                                    'title' => ts('Disable')
                                                                    ),

                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/extensions',
                                                                    'qs'    => 'action=delete&id=%%id%%&key=%%key%%',
                                                                    'title' => ts('Delete Extension') 
                                                                    )
                                  );
            
        }
        return self::$_links;
    }

    /**
     * Run the basic page (run essentially starts execution for that page).
     *
     * @return void
     */
    function run()
    {
        $this->preProcess();
        parent::run();
    }
    
    /**
     * Browse all options
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        $this->assign('extEnabled', self::$_extEnabled );
        if( self::$_extEnabled ) {
            foreach( self::$_extensions as $status => $types ) {
                if( $status === 'enabled' || $status === 'uploaded' ) {
                    foreach( $types as $type => $exts ) {
                        foreach( $exts as $name => $row ) {
                            if( $status === 'uploaded' ) {
                                $action = array_sum(array_keys($this->links()));
                                $action -= CRM_Core_Action::DISABLE;
                                $action -= CRM_Core_Action::ENABLE;
                            } else {
                                if( $row['is_active'] ) {
                                    $action = CRM_Core_Action::DISABLE;
                                } else {
                                    $action = array_sum(array_keys($this->links()));
                                    $action -= CRM_Core_Action::DISABLE;
                                    $action -= CRM_Core_Action::ADD;
                                }
                            }
                            $row['label'] = (string) $row['label'] . ' (' . $name . ')';
                            if( $row['is_corrupt'] ) {
                                $row['label'] .= "   CORRUPT!";
                                $action -= CRM_Core_Action::ENABLE;
                            }
                            $row['version'] = (string) $row['info']->version;
                            $row['action'] = CRM_Core_Action::formLink(self::links(), $action,
                                                                                    array('id' => $row['id'], 
                                                                                          'key' => $name ));
                            $rows[$status][$row['id']] = $row;
                        }
                    }
                }
            }
        }

        $this->assign('rows', $rows['enabled']);
        $this->assign('rowsUploaded', $rows['uploaded']);

    }
    
    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_Extensions';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'CRM_Admin_Form_Extensions';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/extensions';
    }

    /**
     * function to get userContext params
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    function userContextParams( $mode = null ) {
        return 'reset=1&action=browse';
    }

}


