<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 * File for the CiviCRM APIv3 activity functions
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Activity
 * @copyright CiviCRM LLC (c) 2004-2011
 * @version $Id: Activity.php 30486 2010-11-02 16:12:09Z shot $
 *
 */

/**
 * Include common API util functions
 */
require_once 'api/v3/utils.php';

require_once 'CRM/Activity/BAO/Activity.php';
require_once 'CRM/Core/DAO/OptionGroup.php';

/**
 * Create a new Activity.
 *
 * Creates a new Activity record and returns the newly created
 * activity object (including the contact_id property).
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param string $activity_type Which class of contact is being created.
 *            Valid values = 'SMS', 'Meeting', 'Event', 'PhoneCall'.
 * {@getfields activity}
 * {@getfields activity}
 * @return CRM_Activity|CRM_Error Newly created Activity object
 *
 * @todo Eileen 2 Feb - custom data fields per test are non std
 *
 * @example ActivityCreate.php
 * {@example ActivityCreate.php 0}
 *
 */
function civicrm_api3_activity_create( $params ) {

    if ( !CRM_Utils_Array::value('source_contact_id',$params )){
           $session = CRM_Core_Session::singleton( );
           $params['source_contact_id']  =  $session->get( 'userID' );
    }

    civicrm_api3_verify_mandatory($params,
                                  null,
                                  array('source_contact_id',
                                        array('subject','activity_subject'),
                                        array('activity_name','activity_type_id')));
    $errors = array( );

    // check for various error and required conditions
    $errors = _civicrm_api3_activity_check_params( $params ) ;

    if ( !empty( $errors ) ) {
        return $errors;
    }


    // processing for custom data
    $values = array();
    _civicrm_api3_custom_format_params( $params, $values, 'Activity' );

    if ( ! empty($values['custom']) ) {
        $params['custom'] = $values['custom'];
    }

    $params['skipRecentView'] = true;

    if ( CRM_Utils_Array::value('activity_id', $params) ) {
        $params['id'] = $params['activity_id'];
    }

    // If this is a case activity, see if there is an existing activity
    // and set it as an old revision. Also retrieve details we'll need.
    $case_id = '';
    $createRevision = false;
    $oldActivityValues = array();
    if ( CRM_Utils_Array::value('case_id', $params) ) {
    	$case_id = $params['case_id'];
        if ( CRM_Utils_Array::value('id', $params) ) {	            
            $oldActivityParams = array( 'id' => $params['id'] );
            CRM_Activity_BAO_Activity::retrieve( $oldActivityParams, $oldActivityValues );
            if ( empty( $oldActivityValues ) ) {	
            	return civicrm_api3_create_error( ts("Unable to locate existing activity."), null, CRM_Core_DAO::$_nullObject );
            } else {   
                require_once 'CRM/Activity/DAO/Activity.php';
                $activityDAO = new CRM_Activity_DAO_Activity( );
            	$activityDAO->id = $params['id'];
            	$activityDAO->is_current_revision = 0;
		        if ( ! $activityDAO->save( ) ) {
		        	return civicrm_api3_create_error( ts("Unable to revision existing case activity."), null, $activityDAO );
		        }
                $createRevision = true;
	        }
        }
    }

    $deleteActivityAssignment = false;
    if ( isset($params['assignee_contact_id']) ) {
        $deleteActivityAssignment = true;
    }

    $deleteActivityTarget = false;
    if ( isset($params['target_contact_id']) ) {
        $deleteActivityTarget = true;
    }

    $params['deleteActivityAssignment'] = CRM_Utils_Array::value( 'deleteActivityAssignment', $params, $deleteActivityAssignment );
    $params['deleteActivityTarget'] = CRM_Utils_Array::value( 'deleteActivityTarget', $params, $deleteActivityTarget );

    if ( $case_id && $createRevision ) {
    	// This is very similar to the copy-to-case action.
        if ( !CRM_Utils_Array::crmIsEmptyArray( $oldActivityValues['target_contact'] ) ) {
            $oldActivityValues['targetContactIds'] = implode( ',', array_unique( $oldActivityValues['target_contact'] ) );
        }
        if ( !CRM_Utils_Array::crmIsEmptyArray( $oldActivityValues['assignee_contact'] ) ) {
            $oldActivityValues['assigneeContactIds'] = implode( ',', array_unique( $oldActivityValues['assignee_contact'] ) );
        }
        $oldActivityValues['mode'] = 'copy';
        $oldActivityValues['caseID'] = $case_id;
        $oldActivityValues['activityID'] = $oldActivityValues['id'];
        $oldActivityValues['contactID'] = $oldActivityValues['source_contact_id'];

        require_once 'CRM/Activity/Page/AJAX.php';
        $copyToCase = CRM_Activity_Page_AJAX::_convertToCaseActivity( $oldActivityValues );
        if ( empty( $copyToCase['error_msg'] ) ) {
        	// now fix some things that are different from copy-to-case
        	// then fall through to the create below to update with the passed in params
        	$params['id'] = $copyToCase['newId'];
	        $params['is_auto'] = 0;
	        $params['original_id'] = empty( $oldActivityValues['original_id'] ) ? $oldActivityValues['id'] : $oldActivityValues['original_id'] ;
        } else {
            return civicrm_api3_create_error( ts("Unable to create new revision of case activity."), null, CRM_Core_DAO::$_nullObject );
        }  
    }

    // create activity
    $activityBAO = CRM_Activity_BAO_Activity::create( $params );
    
    if ( isset( $activityBAO->id ) ) {
    	if ( $case_id && ! $createRevision) {
    		// If this is a brand new case activity we need to add this
            $caseActivityParams = array ('activity_id' => $activityBAO->id, 'case_id' => $case_id );
            require_once 'CRM/Case/BAO/Case.php';
            CRM_Case_BAO_Case::processCaseActivity ( $caseActivityParams );      
    	}

        _civicrm_api3_object_to_array( $activityBAO, $activityArray[$activityBAO->id]);
        return civicrm_api3_create_success($activityArray,$params,'activity','get',$activityBAO);
    }
}

/*
 * Return valid fields for API
 */
function civicrm_api3_activity_getfields( $params ) {
    $fields =  _civicrm_api_get_fields('activity') ;
    //activity_id doesn't appear to work so let's tell them to use 'id' (current focus is ensuring id works)
    $fields['id'] = $fields['activity_id'];
    unset ($fields['activity_id']);
    $fields['assignee_contact_id'] = array('name' => 'assignee_id',
                                           'title' => 'assigned to',
                                           'type' => 1,
                                           'FKClassName' => 'CRM_Activity_DAO_ActivityAssignment');
    $fields['target_contact_id'] = array('name' => 'target_id',
                                           'title' => 'Activity Target',
                                           'type' => 1,
                                           'FKClassName' => 'CRM_Activity_DAO_ActivityTarget');
    $fields['activity_status_id'] = array('name' => 'status_id',
                                           'title' => 'Status Id',
                                           'type' => 1,);

    require_once ('CRM/Core/BAO/CustomField.php');

    return civicrm_api3_create_success($fields );
}



/**
 *
 * @param array $params
 * @return array
 *
 * @todo - if you pass in contact_id do you / can you get custom fields
 *
 * {@example ActivityGet.php 0}
 */

function civicrm_api3_activity_get( $params ) {
        civicrm_api3_verify_mandatory($params);

        if (!empty($params['contact_id'])){
           $activities = CRM_Activity_BAO_Activity::getContactActivity( $params['contact_id'] );
           //BAO function doesn't actually return a contact ID - hack api for now & add to test so when api re-write happens it won't get missed
           foreach ($activities as $key => $activityArray){
              $activities[$key]['id'] = $key ;
          }
        }else{
          $activities = _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params, FALSE);
        }
        if(CRM_Utils_Array::value('return.assignee_contact_id',$params)){
          foreach ($activities as $key => $activityArray){
              $activities[$key]['assignee_contact_id'] = CRM_Activity_BAO_ActivityAssignment::retrieveAssigneeIdsByActivityId($activityArray['id'] ) ;
          }
        }
        if(CRM_Utils_Array::value('return.target_contact_id',$params)){
          foreach ($activities as $key => $activityArray){
              $activities[$key]['target_contact_id'] = CRM_Activity_BAO_ActivityTarget::retrieveTargetIdsByActivityId($activityArray['id'] ) ;
          }
        }
        foreach ( $params as $n => $v ) {
           if ( substr( $n, 0, 13 ) == 'return.custom' ) { // handle the format return.sort_name=1,return.display_name=1
               $returnProperties[ substr( $n, 7 ) ] = $v;
           }
        }
        if ( !empty( $activities ) && (!empty($returnProperties) || !empty($params['contact_id']))) {
          foreach ($activities as $activityId => $values){

             _civicrm_api3_custom_data_get($activities[$activityId],'Activity',$activityId,null,$values['activity_type_id']);
          }
        }
        //legacy custom data get - so previous formatted response is still returned too
        return civicrm_api3_create_success( $activities ,$params,'activity','get');

}

/**
 * Delete a specified Activity.
 * @param array $params array holding 'id' of activity to be deleted
 *
 * @return void|CRM_Core_Error  An error if 'activityName or ID' is invalid,
 *                         permissions are insufficient, etc.
 *
 * @access public
 *
 * {@example ActivityDelete.php 0}
 */
function civicrm_api3_activity_delete( $params )
{
        civicrm_api3_verify_mandatory($params);
        $errors = array( );

        //check for various error and required conditions
        $errors = _civicrm_api3_activity_check_params( $params ) ;

        if ( !empty( $errors ) ) {
            return $errors;
        }

        if ( CRM_Activity_BAO_Activity::deleteActivity( $params ) ) {
            return civicrm_api3_create_success(1,$params,'activity','delete' );
        } else {
            return civicrm_api3_create_error(  'Could not delete activity'  );
        }

}


/**
 * Function to check for required params
 *
 * @param array   $params  associated array of fields
 * @param boolean $addMode true for add mode
 *
 * @return array $error array with errors
 */
function _civicrm_api3_activity_check_params ( & $params)
{

   $contactIDFields = array_intersect_key($params, array('source_contact_id' => 1,'assignee_contact_id' => 1, 'target_contact_id' => 1));
   if(!empty($contactIDFields)){
   $contactIds = array();
   foreach ($contactIDFields as $fieldname => $contactfield) {
     if(empty($contactfield))break;
     if(is_array($contactfield)) {
       foreach ($contactfield as $contactkey => $contactvalue) {
         $contactIds[$contactvalue] = $contactvalue;
       }
     }else{
       $contactIds[$contactfield] = $contactfield;
     }
   }


        $sql = '
SELECT  count(*)
  FROM  civicrm_contact
 WHERE  id IN (' . implode( ', ', $contactIds ) . ' )';
        if ( count( $contactIds ) !=  CRM_Core_DAO::singleValueQuery( $sql ) ) {
            return civicrm_api3_create_error( 'Invalid '. ucfirst($key) .' Contact Id' );
        }

   }
   
   
    $activityIds = array( 'activity' => CRM_Utils_Array::value( 'id', $params ),
                          'parent'   => CRM_Utils_Array::value( 'parent_id', $params ),
                          'original' => CRM_Utils_Array::value( 'original_id', $params )
                          );

    foreach ( $activityIds as $id => $value ) {
        if (  $value &&
              !CRM_Core_DAO::getFieldValue( 'CRM_Activity_DAO_Activity', $value, 'id' ) ) {
            return civicrm_api3_create_error(  'Invalid ' . ucfirst( $id ) . ' Id' );
        }
    }


    require_once 'CRM/Core/PseudoConstant.php';
    $activityTypes = CRM_Core_PseudoConstant::activityType( true, true, false, 'name', true );
    $activityName   = CRM_Utils_Array::value( 'activity_name', $params );
    $activityTypeId = CRM_Utils_Array::value( 'activity_type_id', $params );

    if ( $activityName ) {
        $activityNameId = array_search( ucfirst( $activityName ), $activityTypes );

        if ( !$activityNameId ) {
            return civicrm_api3_create_error(  'Invalid Activity Name'  );
        } else if ( $activityTypeId && ( $activityTypeId != $activityNameId ) ) {
            return civicrm_api3_create_error(  'Mismatch in Activity'  );
        }
        $params['activity_type_id'] = $activityNameId;
    } else if ( $activityTypeId &&
                !array_key_exists( $activityTypeId, $activityTypes ) ) {
        return civicrm_api3_create_error( 'Invalid Activity Type ID' );
    }


    /*
     * @todo unique name for status_id is activity status id - status id won't be supported in v4
     */
    if (!empty($params['status_id'])){
        $params['activity_status_id'] = $params['status_id'];
    }
    // check for activity status is passed in
    if ( isset( $params['activity_status_id'] ) ) {
        require_once "CRM/Core/PseudoConstant.php";
        $activityStatus = CRM_Core_PseudoConstant::activityStatus( );

        if ( is_numeric( $params['activity_status_id'] ) && !array_key_exists( $params['activity_status_id'], $activityStatus ) ) {
            return civicrm_api3_create_error( 'Invalid Activity Status' );
        } elseif ( !is_numeric( $params['activity_status_id'] ) ) {
            $statusId = array_search( $params['activity_status_id'], $activityStatus );

            if ( !is_numeric( $statusId ) ) {
                return civicrm_api3_create_error( 'Invalid Activity Status' );
            }
        }
    }

    if ( isset( $params['priority_id'] ) )  {
        if ( is_numeric( $params['priority_id'] ) ) {
            require_once "CRM/Core/PseudoConstant.php";
            $activityPriority = CRM_Core_PseudoConstant::priority( );
            if ( !array_key_exists( $params['priority_id'], $activityPriority ) ) {
                return civicrm_api3_create_error( 'Invalid Priority' );
            }
        } else {
            return civicrm_api3_create_error( 'Invalid Priority' );
        }
    }

    // check for activity duration minutes
    if ( isset( $params['duration_minutes'] ) && !is_numeric( $params['duration_minutes'] ) ) {
        return civicrm_api3_create_error('Invalid Activity Duration (in minutes)' );
    }


     //if adding a new activity & date_time not set make it now
    if (!CRM_Utils_Array::value( 'id', $params ) &&
         !CRM_Utils_Array::value( 'activity_date_time', $params ) ) {
        $params['activity_date_time'] = CRM_Utils_Date::processDate( date( 'Y-m-d H:i:s' ) );
    }

    return null;
}

/**
 *
 * @param  array $result
 * @param  int   $activityTypeID  activity type id
 * @return array $params          associated array of activity params
 */
function civicrm_api3_activity_buildmailparams( $result, $activityTypeID ) 
{
    // get ready for collecting data about activity to be created
    $params = array( 'version' => 3 );

    $params['activity_type_id']   = $activityTypeID;
    $params['status_id']          = 2;
    $params['source_contact_id']  = $params['assignee_contact_id'] = $result['from']['id'];
    $params['target_contact_id']  = array( );
    $keys = array( 'to', 'cc', 'bcc' );
    foreach ( $keys as $key ) {
        if ( is_array( $result[$key] ) ) {
            foreach ( $result[$key] as $key => $keyValue ) {
                $params['target_contact_id'][]  = $keyValue['id'];
            }
        }
    }
    $params['subject']            = CRM_Utils_Array::value( 'subject', $result );
    $params['activity_date_time'] = CRM_Utils_Array::value( 'date', $result );
    $params['details']            = CRM_Utils_Array::value( 'body', $result );

    for ( $i = 1; $i <= 5; $i++ ) {
        if ( isset( $result["attachFile_$i"] ) ) {
            $params["attachFile_$i"] = CRM_Utils_Array::value( "attachFile_$i", $result );
        }
    }

    return $params;
}

/**
 * Convert an email file to an activity
 */
function civicrm_api3_activity_process_email( $file, $activityTypeID, $result = array( ) ) 
{
    // do not parse if result array already passed (towards EmailProcessor..)
    if ( empty( $result ) ) {
        // might want to check that email is ok here
        if ( !file_exists( $file ) || !is_readable( $file ) ) {
            return civicrm_api3_create_error( ts( 'File %1 does not exist or is not readable',
                                                  array( 1 => $file ) ) );
        }
    }

    require_once 'CRM/Utils/Mail/Incoming.php';
    $result = CRM_Utils_Mail_Incoming::parse( $file );
    if ( civicrm_api3_error( $result ) ) {
        return $result;
    }

    $params = civicrm_api3_activity_buildmailparams( $result, $activityTypeID );
    return civicrm_api( 'activity', 'create', $params );
}