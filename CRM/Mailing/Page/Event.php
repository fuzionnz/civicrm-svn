<?php 

/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.7                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2007                                  | 
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       | 
 | about the Affero General Public License or the licensing  of       | 
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   | 
 | http://www.civicrm.org/licensing/                                 | 
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Donald A. Lobo <lobo@civicrm.org> 
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Mailing/Selector/Browse.php';
require_once 'CRM/Core/Selector/Controller.php';

/**
 * This implements the profile page for all contacts. It uses a selector
 * object to do the actual dispay. The fields displayd are controlled by
 * the admin
 */
class CRM_Mailing_Page_Event extends CRM_Core_Page {

    /**
     * all the fields that are listings related
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /** 
     * run this page (figure out the action needed and perform it). 
     * 
     * @return void 
     */ 
    function run( ) {
        require_once 'CRM/Mailing/Selector/Event.php';
        $selector =&
            new CRM_Mailing_Selector_Event( 
                      CRM_Utils_Request::retrieve('event', 'String',
                                                  $this),
                      CRM_Utils_Request::retrieve('distinct', 'Boolean',
                                                  $this),
                      CRM_Utils_Request::retrieve('mid', 'Positive',
                                                  $this),
                      CRM_Utils_Request::retrieve('jid', 'Positive', 
                                                  $this),
                      CRM_Utils_Request::retrieve('uid', 'Positive', 
                                                  $this)
                      );
        
        CRM_Utils_System::setTitle($selector->getTitle());
        $this->assign('title',$selector->getTitle());

        $controller =& new CRM_Core_Selector_Controller(
                        $selector ,
                        $this->get( CRM_Utils_Pager::PAGE_ID ),
                        $this->get( CRM_Utils_Sort::SORT_ID  ),
                        CRM_Core_Action::VIEW, 
                        $this, 
                        CRM_Core_Selector_Controller::TEMPLATE );

        $controller->setEmbedded( true );
        $controller->run( );
        
        return parent::run( );
    }

}

?>
