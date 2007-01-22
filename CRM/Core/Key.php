<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://civicrm.org/licensing/                                      |
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

class CRM_Core_Key {

    /**
     * Generate a private key per session and store in session
     *
     * @return string private key for this session
     * @static
     * @access private
     */
    static function privateKey( ) {
        static $key = null;
        if ( ! $key ) {
            $session =& CRM_Core_Session::singleton( );
            $key     =  $session->get( 'qfPrivateKey' );
            if ( ! $key ) {
                $key =
                    md5( uniqid( mt_rand( ), true ) ) .
                    md5( uniqid( mt_rand( ), true ) );
                $session->set( 'qfPrivateKey', $key );
            }
        }
        return $key;
    }

    /**
     * Generate a form key based on form name, the current user session
     * and a private key. Modelled after drupal's form API
     *
     * @param string $value name of the form
     * 
     * @return string       valid formID
     * @static
     * @acess public
     */
    static function get( $name ) {
        $privateKey = self::privateKey( );
        $key = md5( session_id( ) . $name . $privateKey );

        // now generate a random number between 1 and 100K and add it to the key
        // so that we can have forms in mutiple tabs etc
        return $key . '_' . mt_rand( 1, 10000 );
    }

    /**
     * Validate a form key based on the form name
     *
     * @param string $formKey 
     * @param string $name
     *
     * @return string $formKey if valid, else null
     * @static
     * @acess public
     */
    static function validate( $key, $name ) {
        list( $k, $t ) = explode( '_', $key );
        if ( $t < 1 || $t > 10000 ) {
            return null;
        }

        $privateKey = self::privateKey( );
        if ( $k != md5( session_id( ) . $name . $privateKey ) ) {
            return null;
        }
        return $key;
    }

}

?>