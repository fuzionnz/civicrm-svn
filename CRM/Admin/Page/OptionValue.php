<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';

/**
 * Page for displaying list of Option Value
 */
class CRM_Admin_Page_OptionValue extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
.     *
     * @var array
     * @static
     */
    static $_links = null;

    static $_gid = null;

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
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this Option Value?');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/optionValue',
                                                                    'qs'    => 'action=update&id=%%id%%&gid=%%gid%%&reset=1',
                                                                    'title' => ts('Edit Option Value') 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/optionValue',
                                                                    'qs'    => 'action=disable&id=%%id%%&gid=%%gid%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Option Value') 
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/optionValue',
                                                                    'qs'    => 'action=enable&id=%%id%%&gid=%%gid%%',
                                                                    'title' => ts('Enable Option Value') 
                                                                    ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/optionValue',
                                                                    'qs'    => 'action=delete&id=%%id%%&gid=%%gid%%',
                                                                    'title' => ts('Delete Option Value') 
                                                                   )
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
        $action = CRM_Utils_Request::retrieve('action', $this, false, 'browse'); // default to 'browse'

        // assign vars to templates
        $this->assign('action', $action);
        
        $id = CRM_Utils_Request::retrieve('id', $this, false, 0);
        $this->_gid = CRM_Utils_Request::retrieve('gid', $this, false, 0);
        $this->assign('gid' , $this->_gid );

        if ($this->_gid) {
            require_once 'CRM/Core/BAO/OptionGroup.php';
            $groupTitle = CRM_Core_BAO_OptionGroup::getTitle($this->_gid);
            CRM_Utils_System::setTitle(ts('%1 - Option Values', array(1 => $groupTitle)));
        }
        
        // what action to take ?
        if ($action & (CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE)) {
            $this->edit($action, $id , null , false) ;
        } 
        // finally browse the  groups value
        $this->browse();
        
        // parent run 
        parent::run();
    }

    /**
     * Browse all options value.
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse()
    {
        // get all group values sorted by weight
        $optionValue = array();
        require_once 'CRM/Core/DAO/OptionValue.php';
        

        $dao =& new CRM_Core_DAO_OptionValue();
        
        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $dao->domain_id = $config->domainID( );
        $dao->option_group_id = $this->_gid;

        $dao->orderBy('name');
        $dao->find();

        while ($dao->fetch()) {
            $optionValue[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $optionValue[$dao->id]);
            // form all action links
            $action = array_sum(array_keys($this->links()));
            if( $dao->is_default ) {
                $optionValue[$dao->id]['default_value'] = '[x]';
            }
            
            // update enable/disable links depending on if it is is_reserved or is_active
            if ($dao->is_reserved) {
                continue;
            } else {
                if ($dao->is_active) {
                    $action -= CRM_Core_Action::ENABLE;
                } else {
                    $action -= CRM_Core_Action::DISABLE;
                }
            }

            $optionValue[$dao->id]['action'] = CRM_Core_Action::formLink(self::links(), $action, 
                                                                                    array('id' => $dao->id,'gid' => $this->_gid ));
        }
        
        $this->assign('rows', $optionValue);
    }

    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_OptionValue';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Options Values';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/optionValue';
    }
}

?>
