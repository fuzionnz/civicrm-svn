<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/ACL/DAO/GroupJoin.php';

/**
 *  Access Control GroupJoin
 */
class CRM_ACL_BAO_GroupJoin extends CRM_ACL_DAO_GroupJoin {
    static $_entityTable = null;

    static function entityTable( ) {
        if ( ! self::$_entityTable ) {
            self::$_entityTable = array(
                                        'civicrm_contact' => ts( 'Contact' ),
                                        'civicrm_group'   => ts( 'Group'   ), );
        }
        return self::$_entityTable;
    }

    static function create( &$params ) {
        $dao =& new CRM_ACL_DAO_GroupJoin( );
        $dao->copyValues( $params );
        $dao->domain_id = CRM_Core_Config::domainID( );

        $dao->save( );
    }

    static function retrieve( &$params, &$defaults ) {
        CRM_Core_DAO::commonRetrieve( 'CRM_ACL_DAO_GroupJoin', $params, $defaults );
        CRM_Core_Error::debug( 'd', $defaults );
    }    
}

?>
