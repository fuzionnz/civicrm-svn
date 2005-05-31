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
 * The core concept of the system is an action performed on an
 * object. Typically this will be a "data model" object as 
 * specified in the API specs. We attempt to keep the number
 * and type of actions consistent and similar across all 
 * objects (thus providing both reuse and standards)
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Utils/Array.php';

class CRM_Core_Action {

    /**
     * Different possible actions are defined here. Keep in sync with the
     * constant from CRM_Core_Form for various modes.
     *
     * @var const
     *
     * @access public
     */
    const
        NONE          =     0,
        ADD           =     1,
        UPDATE        =     2,
        VIEW          =     4,
        DELETE        =     8,
        BROWSE        =    16,
        ENABLE        =    32,
        DISABLE       =    64,
        EXPORT        =   128,
        BASIC         =   256,
        ADVANCED      =   512;
  
    /**
     * map the action names to the relevant constant. We perform
     * bit manipulation operations so we can perform multiple
     * actions on the same object if needed
     *
     * @var array  _names  tupe of variable name to action constant
     *
     * @access private
     * @static
     *
     */
    static $_names = array(
                           'add'           => self::ADD,
                           'update'        => self::UPDATE,
                           'view'          => self::VIEW  ,
                           'delete'        => self::DELETE,
                           'browse'        => self::BROWSE,
                           'enable'        => self::ENABLE,
                           'disable'       => self::DISABLE,
                           'export'        => self::EXPORT,
                           );

    /**
     * the flipped version of the names array, initialized when used
     * 
     * @var array
     * @static
     */
    static $_description;

    /**
     *
     * called by the request object to translate a string into a mask
     *
     * @param string $action the action to be resolved
     *
     * @return int the action mask corresponding to the input string
     * @access public
     * @static
     *
     */
    static function resolve( $str ) {
        $action = 0;
        if ( $str ) {
            $items = explode( '|', $str );
            $action = self::map( $items );
        }
        return $action;
    }

    /**
     * Given a string or an array of strings, determine the bitmask
     * for this set of actions
     *
     * @param mixed either a single string or an array of strings
     *
     * @return int the action mask corresponding to the input args
     * @access public
     * @static
     *
     */
    static function map( $item ) {
        $mask = 0;

        if ( is_array( $item ) ) {
            foreach ( $item as $it ) {
                $mask |= self::mapItem( $it );
            }
            return $mask;
        } else {
            return self::mapItem( $item );
        }
    }

    /**
     * Given a string determine the bitmask for this specific string
     *
     * @param string the input action to process
     *
     * @return int the action mask corresponding to the input string
     * @access public
     * @static
     *
     */
    static function mapItem( $item ) {
        $mask = CRM_Utils_Array::value( trim( $item ), self::$_names );
        return $mask ? $mask : 0;
    }

    /**
     *
     * Given an action mask, find the corresponding description
     *
     * @param int the action mask
     *
     * @return string the corresponding action description
     * @access public
     * @static
     *
     */
    static function description( $mask ) {
        if ( ! isset( $_description ) ) {
            self::$_description = array_flip( self::$_names );
        }
        
        return CRM_Utils_Array::value( $mask, self::$_description, 'NO DESCRIPTION SET' );
    }

    /**
     * given a set of links and a mask, return the html action string for
     * the links associated with the mask
     *
     * @param array $links  the set of link items
     * @param int   $mask   the mask to be used. a null mask means all items
     * @param array $values the array of values for parameter substitution in the link items
     *
     * @return string       the html string
     * @access public
     * @static
     */
    static function formLink( &$links, $mask, $values ) {
        $url = array( );
        foreach ( $links as $m => $link ) {
            if ( ! $mask || ( $mask & $m ) ) {
                $url[] = sprintf('<a href="%s" '.$link['extra'].'>%s</a>',
                                 CRM_Utils_System::url( $link['url'],
                                                        self::replace( $link['qs'], $values ) ),
                                 $link['name'] );
            }
        }

        $result = '';
        CRM_Utils_String::append( $result, '&nbsp;|&nbsp;', $url );

        return $result;
    }

    /**
     * given a string and an array of values, substitute the real values
     * in the placeholder in the str in the CiviCRM format
     *
     * @param string $str    the string to be replaced
     * @param array  $values the array of values for parameter substitution in the str
     *
     * @return string        the substituted string
     * @access public
     * @static
     */
    static function &replace( &$str, &$values ) {
        foreach ( $values as $n => $v ) {
            $str = str_replace( "%%$n%%", $v, $str );
        }
        return $str;
    }

    /**
     * get the mask for a permission (view, edit or null)
     *
     * @param string the permission
     *
     * @return int   the mask for the above permission
     * @static
     * @access public
     */
    static public function mask( $permission ) {
        if ( $permission == 'view' ) {
            return self::VIEW | self::EXPORT | self::BASIC | self::ADVANCED | self::BROWSE;
        } else if ( $permission == 'edit' ) {
            return 1023; // make sure we make this 2^n -1 if we add more actions;
        } else {
            return null;
        }
    }

}

?>