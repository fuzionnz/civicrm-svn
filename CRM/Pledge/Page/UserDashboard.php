<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Contact/Page/View/UserDashBoard.php';

class CRM_Pledge_Page_UserDashboard extends CRM_Contact_Page_View_UserDashBoard 
{
   /**
    * This function is called when action is browse
    * 
    * return null
    * @access public
    */
    function listPledges( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Pledge_Form_Search', ts('Pledges'), null );
        $controller->setEmbedded( true );
        $controller->reset( );
        $controller->set( 'limit', 12 ); 
        $controller->set( 'cid'  , $this->_contactId );
        $controller->set( 'context'  , 'user' );
        $controller->set( 'force'  , 1 );
        $controller->process( );
        $controller->run( );
        
        //add honor block.
        require_once 'CRM/Pledge/BAO/Pledge.php';
        $honorParams = array( );
        $honorParams = CRM_Pledge_BAO_Pledge::getHonorContacts( $this->_contactId );
        if ( !empty($honorParams) ) {
            // assign vars to templates
            $this->assign( 'pledgeHonorRows', $honorParams );
            $this->assign( 'pledgeHonor', true );
        }
    }
    
    /**
     * This function is the main function that is called when the page
     * loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        parent::preProcess( );
        $this->listPledges( );
    }
}

