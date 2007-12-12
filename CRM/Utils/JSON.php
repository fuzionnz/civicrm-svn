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

/**
 * Class handles functions for JSON format
 */
class CRM_Utils_JSON
{
    /**
     * Function to create JSON object
     * 
     * @param  array  $params     associated array, that needs to be
     *                            converted to JSON array
     * @param  string $identifier identifier for the JSON array 
     * 
     * @return string $jsonObject JSON array     
     * @static
     */
    static function encode ( $params, $identifier = 'id' ) 
    {
        $jsonObject = '{ identifier: "'. $identifier .'", items: [';
        
        foreach ( $params as $value ) {
            $jsonObject .= '{ name: "' . $value['name'] . '",' . $identifier .': "'.$value[$identifier] . '" },';
        }

        $jsonObject .= ' ]}';

        return $jsonObject;
    }
}
?>