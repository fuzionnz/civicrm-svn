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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * Definition of the Contact part of the CRM API. 
 * More detailed documentation can be found 
 * {@link http://objectledge.org/confluence/display/CRM/CRM+v1.0+Public+APIs
 * here}
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

require_once 'CRM/Activity/BAO/Activity.php';
require_once 'CRM/Core/DAO/OptionGroup.php';

/**
 * Create a new Activity.
 *
 * Creates a new Activity record and returns the newly created
 * activity object (including the contact_id property). Minimum
 * required data values for the various contact_type are:
 *
 * Properties which have administratively assigned sets of values
 * If an unrecognized value is passed, an error
 * will be returned. 
 *
 * Modules may invoke crm_get_contact_values($contactID) to
 * retrieve a list of currently available values for a given
 * property.
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param string $activity_type Which class of contact is being created.
 *            Valid values = 'SMS', 'Meeting', 'Event', 'PhoneCall'.
 *                            '
 *
 * @return CRM_Activity|CRM_Error Newly created Activity object
 * 
 */
 
function &civicrm_activity_create( &$params) {
    _civicrm_initialize( );

    // return error if we do not get any params
    if ( empty( $params ) ) {
        return civicrm_create_error( ts( 'Input Parameters empty' ) );
    }

    if ( empty( $params['activity_name'] ) ) {
        return civicrm_create_error( ts ( 'Missing Activity Name' ) );
    }   

    $ids = array();

    // Check for Activityy name
    _civicrm_activity_check_name( $params['activity_name'] );
    
    //check the type of activity
    if ($params['activity_name'] == 'Meeting' ) {
        $activityType = 'Meeting';
    } elseif ( $params['activity_name'] == 'Phonecall' ) {
        $activityType = 'Phonecall';
    } else {
        $activityType = 'Activity';
    }
    
    $activity = CRM_Activity_BAO_Activity::add( $params, $ids, $activityType );

    $activityArray = array(); 
    _civicrm_object_to_array( $activity, $activityArray);
    
    return $activityArray;
}

/**
 * 
 * Retrieves an array of valid values for "enum" 
 *
 * @contactID 
 *
 * @return  Array of $activity Values  
 *
 * @access public
 *
 */
function &civicrm_activities_get_contact($contactID)
{
    _civicrm_initialize( );
    
    if ( empty( $contactID ) ) {
        return civicrm_create_error( ts ( "Required parameter not found" ) );
    }

    $activity = array( );

    // get all the activities of a contact with $contactID
    $activity['meeting'  ]  =& _civicrm_activities_get( $contactID, 'CRM_Activity_DAO_Meeting'   );
    $activity['phonecall']  =& _civicrm_activities_get( $contactID, 'CRM_Activity_DAO_Phonecall' );
    $activity['activity' ]  =& _civicrm_activities_get( $contactID, 'CRM_Activity_DAO_Activity'  );
    
    return $activity;
}

/**
 * Update a specified activity.
 *
 * Updates activity with the values passed in the 'params' array. An
 * error is returned if an invalid id or activity Name is passed 
 * @param CRM_Activity $activity A valid Activity object
 * @param array       $params  Associative array of property
 *                             name/value pairs to be updated. 
 *  
 * @return CRM_Activity|CRM_Core_Error  Return the updated ActivtyType Object else
 *                                Error Object (if integrity violation)
 *
 * @access public
 *
 */
function &civicrm_activity_update( &$params ) {
    if ( ! is_array( $params ) ) {
        return civicrm_create_error( ts( 'Params is not an array' ) );
    }
    
    if ( ! isset($params['id'] ) ) {
        return civicrm_create_error( ts( 'Required parameter "id" missing' ) );
    }

    if ( empty( $params['activity_name'] ) )  {
        return civicrm_create_error( ts( 'Missing Activity Name' ) );
    }   
    
     _civicrm_activity_check_name( $params['activity_name'] );
  
    if ($params['activity_name']== 'Meeting' ) {
        $activity = _civicrm_activity_update( $params, 'CRM_Activity_DAO_Meeting'   );
    } elseif ( $params['activity_name'] == 'Phonecall'  ) {
        $activity = _civicrm_activity_update( $params, 'CRM_Activity_DAO_Phonecall' );
    } else {
        $activity = _civicrm_activity_update($params, 'CRM_Activity_DAO_Activity');
    }
    
    return $activity;
}
/**
 * Delete a specified Activity.
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function &civicrm_activity_delete( &$params ) {
    _civicrm_initialize( );
    
    if ( ! isset( $params['id'] )) {
        return civicrm_create_error( ts( 'Required parameter "id" not found' ) );
    }
    if ( empty( $params['activity_name'] ) ) {
        return civicrm_create_error( ts( 'Missing Activity Name' ) );
    }   
    
    _civicrm_activity_check_name( $params['activity_name'] );
    
    //check the type of activity
    if ( $params['activity_name'] == 'Meeting'  ) {
        $activityType = 'Meeting';
    } elseif ( $params['activity_name'] == 'Phonecall' ) {
        $activityType = 'Phonecall';
    } else {
        $activityType = 'Activity';
    }
    
    $activity = CRM_Activity_BAO_Activity::del( $params['id'], $activityType );
}

/**
 * Function to update activities
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function _civicrm_activity_update($params, $daoName) {
    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    $dao =& new $daoName();
    $dao->id = $params['id'];
    if ( $dao->find( true ) ) {
        $dao->copyValues( $params );
        $dao->save( );
    }
    $activity = array();
    _civicrm_object_to_array( $dao, $activity );
    
    return $activity;
}

/**
 * Delete a specified Activity.
 * @param CRM_Activity $activity Activity object to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 */
function &_civicrm_activities_get( $contactID, $daoName ) {

    require_once(str_replace('_', DIRECTORY_SEPARATOR, $daoName) . ".php");
    eval('$dao =& new $daoName( );');
    $dao->target_entity_id = $contactID;
    $activities = array();

    if ($dao->find()) {
        while ( $dao->fetch() ) {
            _civicrm_object_to_array( $dao, $activity );
            $activities[$dao->id] = $activity;
        }
    }

    return $activities;
}

function _civicrm_activity_check_name( $activityName ) {
    
    require_once 'CRM/Core/DAO/OptionGroup.php';
    $grpdao =& new CRM_Core_DAO_OptionGroup( );
    $grpdao->name = 'activity_type';
    $grpdao->find(true);
 
        
    require_once 'CRM/Core/DAO/OptionValue.php';
    $dao       =& new CRM_Core_DAO_OptionValue( );
    $dao->label = $activityName;
    $dao->option_group_id = $grpdao->id;
  
    if (! $dao->find( true ) ) {
        
        return civicrm_create_error( ts( "Invalid Activity Name" ) );
    }
    return true;
}
