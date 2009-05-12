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
 * Definition of the ActivityContact part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */
 
/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

require_once 'CRM/Activity/BAO/Activity.php';

/**
 * Retrieve a set of activities, specific to given input params.
 *
 * @param  array  $params (reference ) input parameters.
 *
 * @return array (reference)  array of activities / error message.
 * @access public
 */
function civicrm_activity_contact_get( $params ) {
  _civicrm_initialize( );
  
  $contactId = CRM_Utils_Array::value( 'contact_id', $params ); 
  if ( empty( $contactId ) ) {
      return civicrm_create_error( ts ( "Required parameter not found" ) );
  }
  
  if ( !is_numeric( $contactId ) ) {
      return civicrm_create_error( ts ( "Invalid contact Id" ) );
  }
  
  $activities =  & _civicrm_activities_get( $contactId );
     
  if ( $activities ) {
      return civicrm_create_success( $activities );
  } else {
      return civicrm_create_error( ts( 'Invalid Data' ) );
  }
}

/**
 * Retrieve a set of Activities specific to given contact Id.
 * @param int $contactID.
 *
 * @return array (reference)  array of activities.
 * @access public
 */
function &_civicrm_activities_get( $contactID, $type = 'all' ) 
{
    $activities = CRM_Activity_BAO_Activity::getContactActivity( $contactID );
    
    //get the custom data.
    if ( is_array( $activities ) && !empty( $activities ) ) {
        require_once 'api/v2/Activity.php';
        foreach ( $activities as $activityId => $values ) {
            $customParams =  array( 'activity_id'      => $activityId,
                                    'activity_type_id' => CRM_Utils_Array::value( 'activity_type_id', $values ) );
            
            $customData = civicrm_activity_custom_get( $customParams );
            
            if ( is_array( $customData ) && !empty( $customData ) ) {
                $activities[$activityId] = array_merge( $activities[$activityId], $customData );
            }
        }
    }
    
    return $activities;
}
