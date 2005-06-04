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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */



require_once 'CRM/Contact/DAO/IM.php';
require_once 'CRM/Utils/Array.php';
require_once 'CRM/Contact/BAO/Block.php';
require_once 'CRM/Contact/DAO/IM.php';

/**
 * BAO object for crm_im table
 */
class CRM_Contact_BAO_IM extends CRM_Contact_DAO_IM {
    /**
     * takes an associative array and creates a contact object
     *
     * the function extract all the params it needs to initialize the create a
     * contact object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param array  $ids            the array that holds all the db ids
     * @param int    $locationId
     * @param int    $imId
     * @param bool   $isPrimary      Has any previous entry been marked as isPrimary?
     *
     * @return object CRM_Contact_BAO_IM object
     * @access public
     * @static
     */
     function add( &$params, &$ids, $locationId, $imId, &$isPrimary ) {
        if ( ! CRM_Contact_BAO_IM::dataExists( $params, $locationId, $imId, $ids ) ) {
            return null;
        }

        $im = new CRM_Contact_DAO_IM();
        $im->name         = $params['location'][$locationId]['im'][$imId]['name'];
        $im->id = CRM_Utils_Array::value( $imId, $ids['location'][$locationId]['im'] );
        if ( empty( $im->name ) ) {
            $im->delete( );
            return null;
        }

        $im->location_id  = $params['location'][$locationId]['id'];
        $im->provider_id  = $params['location'][$locationId]['im'][$imId]['provider_id'];
        if (! $im->provider_id ) {
            $im->provider_id  = 'null';
        }

        // set this object to be the value of isPrimary and make sure no one else can be isPrimary
        $im->is_primary   = $isPrimary;
        $isPrimary        = false;

        return $im->save( );
    }

    /**
     * Check if there is data to create the object
     *
     * @param array  $params         (reference ) an assoc array of name/value pairs
     * @param int    $locationId
     * @param int    $imId
     * @param array  $ids            the array that holds all the db ids
     *
     * @return boolean
     * @access public
     * @static
     */
     function dataExists( &$params, $locationId, $imId, &$ids ) {
        if (CRM_Utils_Array::value( $imId, $ids['location'][$locationId]['im'] )) {
            return true;
        }
        
        return CRM_Contact_BAO_Block::dataExists('im', array( 'name' ), $params, $locationId, $imId );
    }


    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params        input parameters to find object
     * @param array $values        output values of the object
     * @param array $ids           the array that holds all the db ids
     * @param int   $blockCount    number of blocks to fetch
     *
     * @return void
     * @access public
     * @static
     */
     function getValues( &$params, &$values, &$ids, $blockCount = 0 ) {
        $im = new CRM_Contact_BAO_IM( );
        return CRM_Contact_BAO_Block::getValues( $im, 'im', $params, $values, $ids, $blockCount );
    }

}

?>