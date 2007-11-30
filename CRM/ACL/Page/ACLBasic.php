<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */
class CRM_ACL_Page_ACLBasic extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_ACL_BAO_ACL';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
          if (!(self::$_links)) {
              $disableExtra = ts('Are you sure you want to disable this ACL?');
            // helper variable for nicer formatting
              self::$_links = array(
                                    CRM_Core_Action::UPDATE  => array(
                                                                      'name'  => ts('Edit'),
                                                                      'url'   => 'civicrm/acl/basic',
                                                                      'qs'    => 'reset=1&action=update&id=%%id%%',
                                                                      'title' => ts('Edit ACL') 
                                                                      ),
                                    CRM_Core_Action::DELETE  => array(
                                                                      'name'  => ts('Delete'),
                                                                      'url'   => 'civicrm/acl/basic',
                                                                      'qs'    => 'reset=1&action=delete&id=%%id%%',
                                                                      'title' => ts('Delete ACL') 
                                                                      ),

                                 );
        }
        return self::$_links;
    }

    /**
     * Run the page.
     *
     * This method is called after the page is created. It checks for the  
     * type of action and executes that action.
     * Finally it calls the parent's run method.
     *
     * @return void
     * @access public
     *
     */
    function run()
    {
        // get the requested action
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        $id = CRM_Utils_Request::retrieve('id', 'Positive',
                                          $this, false, 0);
        
        // set breadcrumb to append to admin/access
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin/access', 'reset=1' );
        CRM_Utils_System::appendBreadCrumb( ts('Access Control'), $breadCrumbPath );

        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE)) {
            $this->edit($action, $id) ;
        } 
        // finally browse the acl's
         $this->browse();
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all acls
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        require_once 'CRM/ACL/DAO/ACL.php';

        // get all acl's sorted by weight
        $acl =  array( );
        $query = "
  SELECT *
    FROM civicrm_acl
   WHERE domain_id = %1
     AND ( object_table NOT IN ( 'civicrm_saved_search', 'civicrm_uf_group', 'civicrm_custom_group' ) )
ORDER BY entity_id
";
        $params = array( 1 => array( CRM_Core_Config::domainID( ), 'Integer' ) );
        $dao    = CRM_Core_DAO::executeQuery( $query, $params );

        require_once 'CRM/Core/OptionGroup.php';
        $roles  = CRM_Core_OptionGroup::values( 'acl_role' );

        while ( $dao->fetch( ) ) {
            if ( ! array_key_exists( $dao->entity_id, $acl ) ) {
                $acl[$dao->entity_id] = array();
                $acl[$dao->entity_id]['name']         = $dao->name;
                $acl[$dao->entity_id]['entity_id']    = $dao->entity_id;
                $acl[$dao->entity_id]['entity_table'] = $dao->entity_table;
                $acl[$dao->entity_id]['object_table'] = $dao->object_table;
                $acl[$dao->entity_id]['is_active']    = 1;

                if ( $acl[$dao->entity_id]['entity_id'] ) {
                    $acl[$dao->entity_id]['entity'] = $roles [$acl[$dao->entity_id]['entity_id']];
                } else {
                    $acl[$dao->entity_id]['entity'] = ts( 'Any Role' );
                }

                // form all action links
                $action = array_sum(array_keys($this->links()));
    
                $acl[$dao->entity_id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                     array('id' => $dao->entity_id));
            } else {
                $acl[$dao->entity_id]['object_table'] .= ", {$dao->object_table}";
            }
        }
        $this->assign('rows', $acl);
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_ACL_Form_ACLBasic';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Core ACLs';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/acl/basic';
    }
}

?>
