<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2013                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */

/**
 * Replace the value of an attribute in the input string. Assume
 * the the attribute is well formed, of the type name="value". If
 * no replacement is mentioned the value is inserted at the end of
 * the form element
 *
 * @param string $string    the html to be tweaked with
 * @param string $attribute the attribute to modify
 * @param string $value     the new attribute value
 *
 * @return string        the new modified html string
 * @access public
 */
function smarty_modifier_crmReplace($string, $attribute, $value) {
  static $endOfElement = '/>';

  // if we know what attribute we need to replace
  // we need to search and replace the string: $attribute=XXX or $attribute="XXX"
  // with $attribute=\"$value\"
  $pattern = '/' . $attribute . '="([^"]+?)"/';
  return preg_replace($pattern, $attribute . '="' . $value . '"', $string);
}

