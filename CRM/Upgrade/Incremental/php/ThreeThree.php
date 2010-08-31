<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

class CRM_Upgrade_Incremental_php_ThreeThree {
    
    function verifyPreDBstate ( &$errors ) {
        return true;
    }
    
    function upgrade_3_3_alpha1( $rev ) 
    {
        $config = CRM_Core_Config::singleton( );
        if ( $config->userFramework == 'Drupal' ) {
            // CRM-6426 - make civicrm profiles permissioned on drupal my account
            require_once 'CRM/Utils/System/Drupal.php';
            CRM_Utils_System_Drupal::updateCategories( );
        }
        
        $upgrade =& new CRM_Upgrade_Form( );
        $upgrade->processSQL( $rev );
    }

}
