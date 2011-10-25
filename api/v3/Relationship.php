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
 * File for the CiviCRM APIv3 relationship functions
 *
 * @package CiviCRM_APIv3
 * @subpackage API_Relationship
 * 
 * @copyright CiviCRM LLC (c) 2004-2011
 * @version $Id: Relationship.php 30486 2010-11-02 16:12:09Z shot $
 *
 */

require_once 'CRM/Contact/BAO/Relationship.php';
require_once 'CRM/Contact/BAO/RelationshipType.php';


/**
 * Add or update a relationship
 *
 * @param  array   $params   (reference ) input parameters
 * 
 * {@example RelationshipTypeCreate.php 0}
 * @return array (reference) id of created or updated record
 * @static void
 * @access public
 * {getfields relationship_create}
 */
function civicrm_api3_relationship_create( $params ) {

        // check entities exist
        _civicrm_api3_relationship_check_params( $params );
        $values = array( );
        require_once 'CRM/Contact/BAO/Relationship.php';
        $error = _civicrm_api3_relationship_format_params( $params, $values );

        if ( civicrm_api3_error( $error ) ) {
            return $error;
        }

        $ids = array( );
        $action = CRM_Core_Action::ADD;
        require_once 'CRM/Utils/Array.php';

        if( CRM_Utils_Array::value( 'id', $params ) ) {
            $ids['relationship']  = $params['id'];
            $ids['contactTarget'] = $params['contact_id_b'];
            $action               = CRM_Core_Action::UPDATE;
        }

        $values['relationship_type_id'] = $params['relationship_type_id'].'_a_b';
        $values['contact_check']        = array ( $params['contact_id_b'] => $params['contact_id_b'] );
        $ids   ['contact'      ]        = $params['contact_id_a'];

        $relationshipBAO = CRM_Contact_BAO_Relationship::create( $values, $ids );
        if ( is_a( $relationshipBAO, 'CRM_Core_Error' ) ) {
            return civicrm_api3_create_error( 'Relationship can not be created' );
        } else if ( $relationshipBAO[1] ) {
            return civicrm_api3_create_error( 'Relationship is not valid' );
        } else if ( $relationshipBAO[2] ) {
            return civicrm_api3_create_error( 'Relationship already exists' );
        }
        CRM_Contact_BAO_Relationship::relatedMemberships( $params['contact_id_a'], $values, $ids, $action );
        $relationID = $relationshipBAO[4][0];
        return civicrm_api3_create_success( array( $relationID => array( 'id' => $relationID,
                                                                         'moreIDs' => implode( ',', $relationshipBAO[4] ) ) ) );

}
/*
 * Adjust Metadata for Create action
 * 
 * @param array $params array or parameters determined by getfields
 */
function _civicrm_api3_relationship_create_spec(&$params){
  $params['contact_id_a']['api.required'] = 1;
  $params['contact_id_b']['api.required'] = 1;
  $params['relationship_type_id']['api.required'] = 1;
}
/**
 * Delete a relationship 
 *
 * @param  array $params
 *
 * @return boolean  true if success, else false
 * @static void
 * @access public
 */

function civicrm_api3_relationship_delete( $params ) {

        require_once 'CRM/Utils/Rule.php';
        if( ! CRM_Utils_Rule::integer( $params['id'] ) ) {
            return civicrm_api3_create_error( 'Invalid value for relationship ID' );
        }
    
        $relationBAO = new CRM_Contact_BAO_Relationship( );
        $relationBAO->id = $params['id'];
        if ( !$relationBAO->find( true ) ) {
            return civicrm_api3_create_error(  'Relationship id is not valid' );
        } else {
            $relationBAO->del( $params['id'] );
            return civicrm_api3_create_success(  'Deleted relationship successfully'  );
        }

}


/**
 * Function to get the relationship
 *
 * @param array   $params          (reference ) input parameters.
 * @todo  function in not a search - just returns all relationships for 'contact_id'
 * 
 * @return        Array of all relevant relationships.
 * {getfields relationship_get}
 * @access  public
 */
function civicrm_api3_relationship_get($params) 
{

        if(!CRM_Utils_Array::value('contact_id',$params)){
           $relationships = _civicrm_api3_basic_get(_civicrm_api3_get_BAO(__FUNCTION__), $params,FALSE);
        }else{
             $relationships= array();
             $relationships = CRM_Contact_BAO_Relationship::getRelationship($params['contact_id'],
                                        CRM_Utils_Array::value('status_id',$params),
                                         0,
                                         0,
                                         CRM_Utils_Array::value('id',$params),null );

     
    
        }
        foreach ( $relationships as $relationshipId => $values ) {
               _civicrm_api3_custom_data_get($relationships[$relationshipId],'Relationship',$relationshipId);
         }
    
        
        return civicrm_api3_create_success( $relationships ,$params);
       

}

/**
 * take the input parameter list as specified in the data model and 
 * convert it into the same format that we use in QF and BAO object
 *
 * @param array  $params       Associative array of property name/value
 *                             pairs to insert in new contact.
 * @param array  $values       The reformatted properties that we can use internally
 *                            '
 * @return array|CRM_Error
 * @access public
 */
function _civicrm_api3_relationship_format_params( $params, &$values ) {
    // copy all the relationship fields as is
   
    $fields = CRM_Contact_DAO_Relationship::fields( );
    _civicrm_api3_store_values( $fields, $params, $values );

    $relationTypes = CRM_Core_PseudoConstant::relationshipType( 'name' );
    
    foreach ($params as $key => $value) {
        // ignore empty values or empty arrays etc
        require_once 'CRM/Utils/System.php';
        if ( CRM_Utils_System::isNull( $value ) ) {
            continue;
        }
        
        switch ($key) {
        case 'contact_id_a':
        case 'contact_id_b':
            require_once 'CRM/Utils/Rule.php';
            if (!CRM_Utils_Rule::integer($value)) {
                return civicrm_api3_create_error("contact_id not valid: $value");
            }
            $dao = new CRM_Core_DAO();
            $qParams = array();
            $svq = $dao->singleValueQuery("SELECT id FROM civicrm_contact WHERE id = $value",
                                          $qParams);
            if (!$svq) {
                return civicrm_api3_create_error("Invalid Contact ID: There is no contact record with contact_id = $value.");
            }
            break;
            

            
        case 'relationship_type':
            foreach ( $relationTypes as $relTypId => $relValue ) {
                if (  CRM_Utils_Array::key( ucfirst( $value ), $relValue ) ) {
                    $relationshipTypeId = $relTypId;
                    break;
                }
            }
            
            if ( $relationshipTypeId ) {
                if ( CRM_Utils_Array::value( 'relationship_type_id', $values ) &&
                     $relationshipTypeId != $values['relationship_type_id'] ) {
                    return civicrm_api3_create_error( 'Mismatched Relationship Type and Relationship Type Id' );
                } 
                $values['relationship_type_id'] = $params['relationship_type_id'] = $relationshipTypeId ;
            } else {
                return civicrm_api3_create_error( 'Invalid Relationship Type' );
            } 

        case 'relationship_type_id':            
            if ( $key == 'relationship_type_id' && !array_key_exists($value, $relationTypes) ) {
                return civicrm_api3_create_error("$key not a valid: $value");
            } 
        
            // execute for both relationship_type and relationship_type_id
            $relation = $relationTypes[$params['relationship_type_id']];
            require_once 'CRM/Contact/BAO/Contact.php';
            if ($relation['contact_type_a'] && 
                $relation['contact_type_a'] != CRM_Contact_BAO_Contact::getContactType($params['contact_id_a'])) {
                return civicrm_api3_create_error("Contact ID :{$params['contact_id_a']} is not of contact type {$relation['contact_type_a']}");
            }
            if ($relation['contact_type_b'] && 
                $relation['contact_type_b'] != CRM_Contact_BAO_Contact::getContactType($params['contact_id_b'])) {
                return civicrm_api3_create_error("Contact ID :{$params['contact_id_b']} is not of contact type {$relation['contact_type_b']}");
            }
            break;
              
        default:
            break;
        }
    }
    
    if ( array_key_exists( 'note', $params ) ) {
        $values['note'] = $params['note'];
    }
    _civicrm_api3_custom_format_params( $params, $values, 'Relationship' );
    
    return array();
}
/*
 * @deprecated - checking to be moved to wrapper
 */
function _civicrm_api3_relationship_check_params( &$params ) {


    // check params for validity of Relationship id
    if ( CRM_Utils_Array::value( 'id', $params ) ) {
        require_once 'CRM/Contact/BAO/Relationship.php';
        $relation     = new CRM_Contact_BAO_Relationship();
        $relation->id = $params['id'];
        if ( !$relation->find( true ) ) {
            return civicrm_api3_create_error( 'Relationship id is not valid' );
        } else {
            if ( ( $params['contact_id_a'] != $relation->contact_id_a ) ||
                 ( $params['contact_id_b'] != $relation->contact_id_b ) ) {
                return civicrm_api3_create_error( 'Cannot change the contacts once relationship has been created' );
            }
        }
    }

    return array();
}
