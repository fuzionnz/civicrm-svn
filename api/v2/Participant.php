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
 * File for the CiviCRM APIv2 participant functions
 *
 * @package CiviCRM_APIv2
 * @subpackage API_Participant
 * 
 * @copyright CiviCRM LLC (c) 2004-2010
 * @version $Id$
 *
 */

/**
 * Files required for this package
 */
require_once 'api/v2/utils.php';

/**
 * Create an Event Participant
 *  
 * This API is used for creating a participants in an event.
 * Required parameters : event_id AND contact_id for new creation
 *                     : participant as name/value with participantid for edit
 * @param   array  $params     an associative array of name/value property values of civicrm_participant
 * 
 * @return array participant id if participant is created/edited otherwise is_error = 1
 * @access public
 */
function civicrm_participant_create(&$params)
{
    _civicrm_initialize();
    
    if ( !is_array( $params ) ) {
        $error = civicrm_create_error( 'Parameters is not an array' );
        return $error;
    }
    
    if ( !isset($params['event_id']) || !isset($params['contact_id'])) {
        $error = civicrm_create_error( 'Required parameter missing' );
        return $error;
    }
    
    if ( !isset($params['status_id'] )) {
        $params['status_id'] = 1;
    } 
    
    if ( !isset($params['register_date'] )) {
        $params['register_date']= date( 'YmdHis' );
    }
    
    require_once 'CRM/Event/BAO/Participant.php';
    $participant = CRM_Event_BAO_Participant::create($params);
    
    if ( is_a( $participant, 'CRM_Core_Error' ) ) {
        return civicrm_create_error( "Participant is not created" );
    } else {
        return civicrm_create_success( $participant->id );
    }
}

/**
 * Retrieve a specific participant, given a set of input params
 * If more than one matching participant exists, return an error, unless
 * the client has requested to return the first found contact
 *
 * @param  array   $params           (reference ) input parameters
 *
 * @return array (reference )        array of properties, if error an array with an error id and error message
 * @static void
 * @access public
 */
function &civicrm_participant_get( &$params ) {
    _civicrm_initialize( );
 
    $values = array( );
    if ( empty( $params ) ) {
        $error = civicrm_create_error( ts( 'No input parameters present' ) );
        return $error;
    }
    
    if ( ! is_array( $params ) ) {
        $error = civicrm_create_error( ts( 'Input parameters is not an array' ) );
        return $error;
    }

    if ( isset ( $params['id'] ) ) {
        $params['participant_id' ] = $params['id'];
        unset( $params['id'] );
    }
    
    $participant  =& civicrm_participant_search( $params );
    

    if ( count( $participant ) != 1 &&
         ! CRM_Utils_Array::value( 'returnFirst', $params ) ) {
        $error = civicrm_create_error( ts( '%1 participant matching input params', array( 1 => count( $participant ) ) ),
                                       $participant );
        return $error;
    }

    if ( civicrm_error( $participant ) ) {
        return $participant;
    }

    $participant = array_values( $participant );
    return $participant[0];
}

/**
 * Get contact participant record.
 * 
 * This api is used for finding an existing participant record.
 *
 * @param  array  $params     an associative array of name/value property values of civicrm_participant
 *
 * @return  participant property values.
 * @access public
 */  

function &civicrm_participant_search( &$params ) {

    if( ! is_array($params) ) {
        return civicrm_create_error( 'Params need to be of type array!' );
    }
    
    $inputParams      = array( );
    $returnProperties = array( );
    $otherVars = array( 'sort', 'offset', 'rowCount' );
    
    $sort     = null;
    $offset   = 0;
    $rowCount = 25;
    foreach ( $params as $n => $v ) {
        if ( substr( $n, 0, 7 ) == 'return.' ) {
            $returnProperties[ substr( $n, 7 ) ] = $v;
        } elseif ( in_array ( $n, $otherVars ) ) {
            $$n = $v;
        } else {
            $inputParams[$n] = $v;
        }
    }

    // add is_test to the clause if not present
    if ( ! array_key_exists( 'participant_test', $inputParams ) ) {
        $inputParams['participant_test'] = 0;
    }

    require_once 'CRM/Contact/BAO/Query.php';
    require_once 'CRM/Event/BAO/Query.php';  
    if ( empty( $returnProperties ) ) {
        $returnProperties = CRM_Event_BAO_Query::defaultReturnProperties( CRM_Contact_BAO_Query::MODE_EVENT );
    }

    $newParams =& CRM_Contact_BAO_Query::convertFormValues( $params);
    $query = new CRM_Contact_BAO_Query( $newParams, $returnProperties, null );
    list( $select, $from, $where ) = $query->query( );
    
    $sql = "$select $from $where";  

    if ( ! empty( $sort ) ) {
        $sql .= " ORDER BY $sort ";
    }
    $sql .= " LIMIT $offset, $rowCount ";
    $dao =& CRM_Core_DAO::executeQuery( $sql, CRM_Core_DAO::$_nullArray );
    
    $participant = array( );
    while ( $dao->fetch( ) ) {
        $participant[$dao->participant_id] = $query->store( $dao );
    }
    $dao->free( );

    return $participant;

}

/**
 * Update an existing contact participant
 *
 * This api is used for updating an existing contact participant.
 * Required parrmeters : id of a participant
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_participant
 * 
 * @return array of updated participant property values
 * @access public
 */
function &civicrm_participant_update(&$params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        return civicrm_create_error( 'Parameters is not an array' );
    }
    
    if ( !isset($params['id']) ) {
        $error = civicrm_create_error( 'Required parameter missing' );
        return $error;
    }
    
    require_once 'CRM/Event/BAO/Participant.php';
    $participantBAO = new CRM_Event_BAO_Participant( );
    $participantBAO->id = $params['id'];
    $fields = $participantBAO->fields( );
    $datefields = array("register_date" => "register_date");    
    foreach ( array('source','status_id','register_date','role_id') as $v) {    
        $fields[$v] = $fields['participant_'.$v];
        unset( $fields['participant_'.$v] );
    }
    
    if ($participantBAO->find(true)) {
        foreach ( $fields as $name => $field) {
            if (array_key_exists($name, $params)) {
                $participantBAO->$name = $params[$name];
            }
        }
        
        //fix the dates 
        foreach ( $datefields as $key => $value ) {
            $participantBAO->$key  = CRM_Utils_Date::customFormat($participantBAO->$key,'%Y%m%d');
        }
        $participantBAO->save();
    }
    
    $participant = array();
    _civicrm_object_to_array( $participantBAO, $participant );
    return $participant;
}



/**
 * Deletes an existing contact participant
 * 
 * This API is used for deleting a contact participant
 * 
 * @param  Int  $participantID   Id of the contact participant to be deleted
 * 
 * @return boolean        true if success, else false
 * @access public
 */
function &civicrm_participant_delete( &$params )
{
    _civicrm_initialize();
    
    if ( !is_array( $params ) ) {
        $error = civicrm_create_error( 'Params is not an array' );
        return $error;
    }
    
    if ( !isset($params['id'])) {
        $error = civicrm_create_error( 'Required parameter missing' );
        return $error;
    }
    require_once 'CRM/Event/BAO/Participant.php';
    $participant = new CRM_Event_BAO_Participant();
    $result = $participant->deleteParticipant( $params['id'] );
    
    if ( $result ) {
        $values = civicrm_create_success( );
    } else {
        $values = civicrm_create_error('Error while deleting participant');
    }
    return $values;
}


/**
 * Create a Event Participant Payment
 *  
 * This API is used for creating a Participant Payment of Event.
 * Required parameters : participant_id, contribution_id.
 * 
 * @param   array  $params     an associative array of name/value property values of civicrm_participant_payment
 * 
 * @return array of newly created payment property values.
 * @access public
 */
function &civicrm_participant_payment_create(&$params)
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        $error = civicrm_create_error( 'Params is not an array' );
        return $error;
    }
    
    if ( !isset($params['participant_id']) || !isset($params['contribution_id']) ) {
        $error = civicrm_create_error( 'Required parameter missing' );
        return $error;
    }
   
    $ids= array();
    if( CRM_Utils_Array::value( 'id', $params ) ) {
        $ids['id'] = $params['id']; 
    }
    require_once 'CRM/Event/BAO/ParticipantPayment.php';
    $participantPayment = CRM_Event_BAO_ParticipantPayment::create($params, $ids);
    
    if ( is_a( $participantPayment, 'CRM_Core_Error' ) ) {
        $error = civicrm_create_error( "Participant payment could not be created" );
        return $error;
    } else {
        $payment = array( );
        $payment['id'] = $participantPayment->id;
        $payment['is_error']   = 0;
    }
    return $payment;
   
}

/**
 * Update an existing contact participant payment
 *
 * This api is used for updating an existing contact participant payment
 * Required parameters : id of a participant_payment
 * 
 * @param  Array   $params  an associative array of name/value property values of civicrm_participant_payment
 * 
 * @return array of updated participant_payment property values
 * @access public
 */
function &civicrm_participant_payment_update( &$params )
{
    _civicrm_initialize();
    if ( !is_array( $params ) ) {
        $error = civicrm_create_error( 'Params is not an array' );
        return $error;
    }
    
    if ( !isset($params['id']) ) {
        $error = civicrm_create_error( 'Required parameter missing' );
        return $error;
    }

    $ids = array();
    $ids['id'] = $params['id'];

    require_once 'CRM/Event/BAO/ParticipantPayment.php';
    $payment = CRM_Event_BAO_ParticipantPayment::create( $params, $ids );
   
    $participantPayment = array();
    _civicrm_object_to_array( $payment, $participantPayment );
    
    return $participantPayment;
}

/**
 * Deletes an existing Participant Payment
 * 
 * This API is used for deleting a Participant Payment
 * 
 * @param  Int  $participantPaymentID   Id of the Participant Payment to be deleted
 * 
 * @return null if successfull, array with is_error=1 otherwise
 * @access public
 */
function civicrm_participant_payment_delete( &$params )
{
    _civicrm_initialize();
    
    if ( !is_array( $params ) ) {
        $error = civicrm_create_error( 'Params is not an array' );
        return $error;
    }
    
    if ( ! CRM_Utils_Array::value( 'id', $params ) ) {
        $error = civicrm_create_error( 'Invalid or no value for Participant payment ID' );
        return $error;
    }
    require_once 'CRM/Event/BAO/ParticipantPayment.php';
    $participant = new CRM_Event_BAO_ParticipantPayment();
    
    return $participant->deleteParticipantPayment( $params ) ? civicrm_create_success( ) : civicrm_create_error('Error while deleting participantPayment');
}

/**
 *
 * @param <type> $params
 * @param <type> $onDuplicate
 * @return <type>
 */
function civicrm_create_participant_formatted( &$params , $onDuplicate ) 
{
    _civicrm_initialize( );

    // return error if we have no params
    if ( empty( $params ) ) {
        return civicrm_create_error( 'Input Parameters empty' );
    }

    require_once 'CRM/Event/Import/Parser.php';
    if ( $onDuplicate != CRM_Event_Import_Parser::DUPLICATE_NOCHECK) {
        CRM_Core_Error::reset( );
        $error = civicrm_participant_check_params( $params );
        if ( civicrm_error( $error ) ) {
            return $error;
        }
    }
    
    return civicrm_participant_create( $params );
}

/**
 *
 * @param <type> $params
 * @return <type> 
 */
function civicrm_participant_check_params( &$params ) 
{
    require_once 'CRM/Event/BAO/Participant.php';
    
    $result = array( );
    
    if( CRM_Event_BAO_Participant::checkDuplicate( $params, $result ) ) {
        $participantID = array_pop( $result );
        
        $error = CRM_Core_Error::createError( "Found matching participant record.", 
                                              CRM_Core_Error::DUPLICATE_PARTICIPANT, 
                                              'Fatal', $participantID );
        
        return civicrm_create_error( $error->pop( ),
                                     array( 'contactID'     => $params['contact_id'],
                                            'participantID' => $participantID ) );
    }
    
    return true;
}



