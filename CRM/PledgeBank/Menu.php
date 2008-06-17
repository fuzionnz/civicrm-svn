<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                  |
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
 * Menu for the PledgeBank module
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Menu.php';

class CRM_PledgeBank_Menu {

    static function permissioned( ) {
        $items = array(
                       'civicrm/pledge' => 
                       array( 
                             'query'   => array('reset' => 1),
                             'title'   => ts('PledgeBank'), 
                             'access_arguments'  => array( array( 'access PledgeBank') ), 
                             'page_type' => CRM_Core_Menu::MENU_ITEM,
                             'weight'    => 900,
                             'component' => 'PledgeBank',
                             ),
                       
                       'civicrm/pledge/info' =>
                       array( 
                             'path'    => 'civicrm/pledge/info', 
                             'query'   => array('reset' => 1),
                             'access_arguments'  => 1,
                             'weight'  => 0, 
                             ),
                       
                       
                       'civicrm/pledge/signer' =>
                       array( 
                             'path'    => 'civicrm/pledge/signer', 
                             'query'   => array('reset' => 1),
                             'title'   => ts( 'Pledge Signers List' ), 
                             'access_arguments'  => array( array( 'view pledge signers' ) ),
                             'weight'  => 0, 
                             ),
                       );
        return $items;
    }

}


