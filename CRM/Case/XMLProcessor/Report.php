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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Case/XMLProcessor.php';

class CRM_Case_XMLProcessor_Report extends CRM_Case_XMLProcessor {
    
    /**
     * The default variable defined
     *
     * @var boolean
     */
    protected $_isRedact;
    
    public function __construct( ) {
    
    }


    function run( $clientID,
                  $caseID,
                  $activitySetName,
                  $params ) {
       
        $contents = self::getCaseReport( $clientID,
                                         $caseID,
                                         $activitySetName,
                                         $params,
                                         $this );
        
        require_once 'CRM/Case/Audit/Audit.php';
        return Audit::run( $contents, $clientID, $caseID );
        
        /******
         require_once 'CRM/Utils/System.php';
         CRM_Utils_System::download( "{$case['clientName']} {$case['caseType']}",
         'text/xml',
         $contents,
         'xml', true );
        ******/
    }
    
    function &caseInfo( $clientID,
                        $caseID ) {
        $case = array( );
        if ( $this->_isRedact == 1 ) {
            require_once "CRM/Case/PseudoConstant.php";
            foreach ( array('redactionStringRules', 'redactionRegexRules' ) as $key => $rule ) {
                $$rule = CRM_Case_PseudoConstant::redactionRule($key);
                if (!empty($$rule)) {
                    $this->{'_'. $rule} = array_change_key_case(array_flip($$rule),CASE_LOWER);
                    foreach($this->{'_'. $rule} as &$value) {
                        //suffixed with a randomly generated 4-digit number
                        $value.= rand(10000 ,100000);
                    }    
                }    
            }     
        }
        
        $case['clientName'] = $this->redact(CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                         $clientID,
                                                                         'display_name' ));
        
        require_once 'CRM/Case/DAO/Case.php';
        $dao = new CRM_Case_DAO_Case( );
        $dao->id = $caseID;
        if ( $dao->find( true ) ) {
            $case['subject']    = $dao->subject;
            $case['start_date'] = $dao->start_date;
            $case['end_date']   = $dao->end_date;
            // FIXME: when we resolve if case_type_is single or multi-select
            if ( strpos( $dao->case_type_id, CRM_Core_DAO::VALUE_SEPARATOR ) !== false ) {
                $caseTypeID = substr( $dao->case_type_id, 1, -1 );
            } else {
                $caseTypeID = $dao->case_type_id;
            }
            $caseTypeIDs = explode( CRM_Core_DAO::VALUE_SEPARATOR,
                                    $dao->case_type_id );
            $case['caseType']   = CRM_Core_OptionGroup::getLabel( 'case_type',
                                                                  $caseTypeID );
            $case['status']     = CRM_Core_OptionGroup::getLabel( 'case_status',
                                                                  $dao->status_id );
        }
        
        return $case;
    }
    
    function getActivityTypes( $xml, $activitySetName ) {
        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( (string ) $activitySetXML->name == $activitySetName ) {
                    $activityTypes    =  array( );
                    $allActivityTypes =& $this->allActivityTypes( );
                    foreach ( $activitySetXML->ActivityTypes as $activityTypesXML ) {
                        foreach ( $activityTypesXML as $activityTypeXML ) {
                            $activityTypeName =  (string ) $activityTypeXML->name;
                            $activityTypeInfo = CRM_Utils_Array::value( $activityTypeName, $allActivityTypes );
                            if ( $activityTypeInfo ) {
                                $activityTypes[$activityTypeInfo['id']] = $activityTypeInfo;
                            }
                        }
                    }
                    return  empty( $activityTypes ) ? false : $activityTypes;
                }
            }
        }
        return false;
    }    
    
    function getActivitySetLabel( $xml, $activitySetName ) {
        foreach ( $xml->ActivitySets as $activitySetsXML ) {
            foreach ( $activitySetsXML->ActivitySet as $activitySetXML ) {
                if ( (string ) $activitySetXML->name == $activitySetName ) {
                    return (string ) $activitySetXML->label;
                }
            }
        }
        return null;
    }
    
    function getActivities( $clientID,
                            $caseID,
                            $activityTypes,
                            &$activities ) {
        // get all activities for this case that in this activityTypes set
        
        foreach ( $activityTypes as $aType ) {
            $map[$aType['id']] = $aType;
        }
        
        $activityTypeIDs = implode( ',', array_keys( $map ) );
        $query = "
SELECT a.*, c.id as caseID
FROM   civicrm_activity a,
       civicrm_case     c,
       civicrm_case_activity ac
WHERE  a.is_current_revision = 1
AND    a.activity_type_id IN ( $activityTypeIDs )
AND    c.id = ac.case_id
AND    a.id = ac.activity_id
AND    ac.case_id = %1
";
        
        $params = array( 1 => array( $caseID, 'Integer' ) );
        $dao = CRM_Core_DAO::executeQuery( $query, $params );
        while ( $dao->fetch( ) ) {
            $activityTypeInfo = $map[$dao->activity_type_id];
            $activities[] = $this->getActivity( $clientID,
                                                $dao,
                                                $map[$dao->activity_type_id] );
        }
    }
    
    function &getActivityInfo( $clientID, $activityID, $anyActivity = false ) {
        static $activityInfos = array( );
        
        require_once 'CRM/Core/OptionGroup.php';
        
        $index = $activityID . '_' . (int) $anyActivity;

        if ( $clientID ) {
            $index = $index . '_' . $clientID;   
        }
        
        if ( ! array_key_exists($index, $activityInfos) ) {
            $activityInfos[$index] = array( );
            $selectCaseActivity = "";
            $joinCaseActivity   = "";
            
            if ( $clientID ) {
                $selectCaseActivity = ", ca.case_id as caseID ";
                $joinCaseActivity   = " INNER JOIN civicrm_case_activity ca ON a.id = ca.activity_id ";
            }
            
            $query = "
SELECT     a.*, aa.assignee_contact_id as assigneeID, at.target_contact_id as targetID
{$selectCaseActivity}
FROM       civicrm_activity a
{$joinCaseActivity}
LEFT JOIN civicrm_activity_target at ON a.id = at.activity_id
LEFT JOIN civicrm_activity_assignment aa ON a.id = aa.activity_id
WHERE      a.id = %1 
";
            $params = array( 1 => array( $activityID, 'Integer' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
            if ( $dao->fetch( ) ) {
                //if activity type is email get info of all activities.
                if ( $dao->activity_type_id == CRM_Core_OptionGroup::getValue( 'activity_type', 'Email', 'name' ) ) {
                    $anyActivity = true;
                }
                $activityTypes    = $this->allActivityTypes( false, $anyActivity );
                $activityTypeInfo = null;
                
                if ( isset($activityTypes[$dao->activity_type_id]) ) {
                    $activityTypeInfo = $activityTypes[$dao->activity_type_id];
                }
                if ( $activityTypeInfo ) {
                    $activityInfos[$index] = $this->getActivity( $clientID, $dao, $activityTypeInfo );
                }
            }
        }
        return $activityInfos[$index];
    }
    
    function &getActivity( $clientID,
                           $activityDAO,
                           &$activityTypeInfo ) {
        require_once 'CRM/Core/OptionGroup.php';
        
        $activity = array( );
        $activity['fields'] = array( );
        
        if ( $clientID ) {
            $clientID = CRM_Utils_Type::escape($clientID,   'Integer');
            
            $activity['editURL'] = CRM_Utils_System::url( 'civicrm/case/activity',
                                                          "reset=1&cid={$clientID}&caseid={$activityDAO->caseID}&action=update&atype={$activityDAO->activity_type_id}&id={$activityDAO->id}" );
            
            $activity['fields'][] = array( 'label' => 'Client',
                                           'value' => $this->redact(CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                                                 $clientID,
                                                                                                 'display_name' )
                                                                    ),
                                           'type'  => 'String' );
        }
        
        // Activity Type info is a special field
        $activity['fields'][] = array( 'label'    => 'Activity Type',
                                       'value'    => $activityTypeInfo['label'],
                                       'type'     => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Subject',
                                       'value' => $activityDAO->subject,
                                       'type'  => 'Memo' );
        
        $activity['fields'][] = array( 'label' => 'Created By',
                                       'value' => $this->redact($this->getCreatedBy( $activityDAO->id )),
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Reported By',
                                      'value' => $this->redact(CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                                            $activityDAO->source_contact_id,
                                                                                            'display_name' )
                                                               ),
                                       'type'  => 'String' );
        
        // For Emails, include the recipient
        if ( $activityTypeInfo['name'] == 'Email' && $activityDAO->targetID ) {
            $activity['fields'][] = array( 'label' => 'Recipient',
                                           'value' => $this->redact(CRM_Core_DAO::getFieldValue( 'CRM_Contact_DAO_Contact',
                                                                                                 $activityDAO->targetID,
                                                                                                 'display_name' )
                                                                    ),
                                           'type'  => 'String' );
        }
        
        if ( $activityDAO->assigneeID ) {
            //allow multiple assignee contacts.CRM-4503.
            require_once 'CRM/Activity/BAO/ActivityAssignment.php';
            $assignee_contact_names = CRM_Activity_BAO_ActivityAssignment::getAssigneeNames( $activityDAO->id, true );
            $assigneeContacts       = implode( ', ', $assignee_contact_names );
            
            $activity['fields'][] = array( 'label' => 'Assigned To',
                                           'value' => $this->redact( $assigneeContacts ), 
                                           'type'  => 'String' );
        }
        
        if ( $activityDAO->medium_id ) {
            $activity['fields'][] = array( 'label' => 'Medium',
                                           'value' => CRM_Core_OptionGroup::getLabel( 'encounter_medium',
                                                                                      $activityDAO->medium_id ),
                                           'type'  => 'String' );
        }
        
        $activity['fields'][] = array( 'label' => 'Location',
                                       'value' => $activityDAO->location,
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Due Date',
                                       'value' => $activityDAO->due_date_time,
                                       'type'  => 'Date' );
        
        $activity['fields'][] = array( 'label' => 'Actual Date',
                                       'value' => $activityDAO->activity_date_time,
                                       'type'  => 'Date' );
        
        $activity['fields'][] = array( 'label' => 'Details',
                                       'value' => $this->redact($activityDAO->details, true, false, 'lookup'),
                                       'type'  => 'Memo' );
        
        // Skip Duration field if empty (to avoid " minutes" output). Might want to do this for all fields at some point. dgg
        if ( $activityDAO->duration ) {
            $activity['fields'][] = array( 'label' => 'Duration',
                                           'value' => $activityDAO->duration . ' ' . ts('minutes'),
                                           'type'  => 'Int' );
        }        
        $activity['fields'][] = array( 'label' => 'Status',
                                       'value' => CRM_Core_OptionGroup::getLabel( 'activity_status',
                                                                                  $activityDAO->status_id ),
                                       'type'  => 'String' );
        
        $activity['fields'][] = array( 'label' => 'Priority',
                                       'value' => CRM_Core_OptionGroup::getLabel( 'priority',
                                                                                  $activityDAO->priority_id ),
                                       'type'  => 'String' );
        
        //for now empty custom groups
        $activity['customGroups'] = $this->getCustomData( $clientID,
                                                          $activityDAO,
                                                          $activityTypeInfo );
        
        //CRM_Core_Error::debug($activity); exit();
        return $activity;
    }
    
    function getCustomData( $clientID,
                            $activityDAO,
                            &$activityTypeInfo ) {
        list( $typeValues, $options, $sql ) = $this->getActivityTypeCustomSQL( $activityTypeInfo['id'], '%Y-%m-%d' );
        
        $params = array( 1 => array( $activityDAO->id, 'Integer' ) );
        
        require_once "CRM/Core/BAO/CustomField.php";
        $customGroups = array( );
        foreach ( $sql as $tableName => $sqlClause ) {
            $dao = CRM_Core_DAO::executeQuery( $sqlClause, $params );
            if ( $dao->fetch( ) ) {
                $customGroup = array( );
                foreach ( $typeValues[$tableName] as $columnName => $typeValue ) {
                    $value = CRM_Core_BAO_CustomField::getDisplayValue( $dao->$columnName,
                                                                        $typeValue['fieldID'],
                                                                        $options );
                    
                    // Note: this is already taken care in getDisplayValue above, but sometimes 
                    // strings like '^A^A' creates problem. So to fix this special case -
                    if ( strstr($value, CRM_Core_BAO_CustomOption::VALUE_SEPERATOR) ) {
                        $value = trim($value, CRM_Core_BAO_CustomOption::VALUE_SEPERATOR);
                    }
                    
                    $customGroup[] = array( 'label'  => $typeValue['label'],
                                            'value'  => $value,
                                            'type'   => $typeValue['type'] );
                }
                $customGroups[$dao->groupTitle] = $customGroup;
            }
        }
        
        return empty( $customGroups ) ? null : $customGroups;
    }
    
    function getActivityTypeCustomSQL( $activityTypeID, $dateFormat = null ) {
        static $cache = array( );
        
        if ( ! isset( $cache[$activityTypeID] ) ) {
            $query = "
SELECT cg.title           as groupTitle, 
       cg.table_name      as tableName ,
       cf.column_name     as columnName,
       cf.label           as label     ,
       cg.id              as groupID   ,
       cf.id              as fieldID   ,
       cf.data_type       as dataType  ,
       cf.html_type       as htmlType  ,
       cf.option_group_id as optionGroupID
FROM   civicrm_custom_group cg,
       civicrm_custom_field cf
WHERE  cf.custom_group_id = cg.id
AND    cg.extends = 'Activity'
AND    cg.extends_entity_column_value = %1
";
            $params = array( 1 => array( $activityTypeID,
                                         'Integer' ) );
            $dao = CRM_Core_DAO::executeQuery( $query, $params );
            
            $result = $options = $sql = $groupTitle = array( );
            while ( $dao->fetch( ) ) {
                if ( ! array_key_exists( $dao->tableName, $result ) ) {
                    $result [$dao->tableName] = array( );
                    $sql    [$dao->tableName] = array( );
                }
                $result[$dao->tableName][$dao->columnName] = array( 'label'   => $dao->label,
                                                                    'type'    => $dao->dataType,
                                                                    'fieldID' => $dao->fieldID );
                
                $options[$dao->fieldID  ] = array( );
                $options[$dao->fieldID]['attributes'] = array( 'label'     => $dao->label,
                                                               'data_type' => $dao->dataType, 
                                                               'html_type' => $dao->htmlType );
                // since we want to add ISO date format.
                if ( $dateFormat && $dao->htmlType == 'Select Date' ) {
                    $options[$dao->fieldID]['attributes']['format'] = $dateFormat;
                }
                if ( $dao->optionGroupID ) {
                    $query = "
SELECT label, value
  FROM civicrm_option_value
 WHERE option_group_id = {$dao->optionGroupID}
";
                    
                    $option =& CRM_Core_DAO::executeQuery( $query );
                    while ( $option->fetch( ) ) {
                        $dataType = $dao->dataType;
                        if ( $dataType == 'Int' || $dataType == 'Float' ) {
                            $num = round($option->value, 2);
                            $options[$dao->fieldID]["$num"] = $option->label;
                        } else {
                            $options[$dao->fieldID][$option->value] = $option->label;
                        }
                    }
                }
                
                $sql[$dao->tableName][] = $dao->columnName;
                $groupTitle[$dao->tableName] = $dao->groupTitle;
            }
            
            foreach ( $sql as $tableName => $values ) {
                $columnNames = implode( ',', $values );
                $sql[$tableName] = "
SELECT '{$groupTitle[$tableName]}' as groupTitle, $columnNames
FROM   $tableName
WHERE  entity_id = %1
";
            }
            
            $cache[$activityTypeID] = array( $result, $options, $sql );
        }
        return $cache[$activityTypeID];
    }
    
    function getCreatedBy( $activityID ) {
        $query = "
SELECT c.display_name
FROM   civicrm_contact c,
       civicrm_log     l
WHERE  l.entity_table = 'civicrm_activity'
AND    l.entity_id    = %1
AND    l.modified_id  = c.id
LIMIT  1
";
        $params = array( 1 => array( $activityID, 'Integer' ) );
        return CRM_Core_DAO::singleValueQuery( $query, $params );
    }
    
	private function redact( $s, $isRedact = false, $printReport = false, $type = 'name')
	{
         if ( $isRedact && $printReport ) {
            if($type =='name') {
                return sha1($s);
            } elseif($type =='lookup') {
                require_once 'CRM/Utils/String.php';
                $s = CRM_Utils_String::redaction( $s, $this->_redactionRegexRules, $this->_redactionStringRules );
                return $s;
            }
        } else if ( !$isRedact && $printReport ) {
            return $s;
        } else if ( $this->_isRedact ) {
            if($type =='name') {
                return sha1($s);
            } elseif($type =='lookup') {
                require_once 'CRM/Utils/String.php';
                $s = CRM_Utils_String::redaction( $s, $this->_redactionRegexRules, $this->_redactionStringRules );
                return $s;
            }
		} else {
			return $s;
		}
	}
    
    function getCaseReport( $clientID, $caseID, $activitySetName, $params, $form ) {
        
        require_once 'CRM/Core/OptionGroup.php';
        require_once 'CRM/Contact/BAO/Contact.php';
        require_once 'CRM/Core/BAO/CustomField.php';
        
        $template =& CRM_Core_Smarty::singleton( );
      
        $template->assign( 'caseId',   $caseID ); 
        $template->assign( 'clientID', $clientID );
        $template->assign( 'activitySetName', $activitySetName );

        if ( CRM_Utils_Array::value( 'is_redact', $params ) ) {
            $form->_isRedact = true;
            $template->assign( '_isRedact', 'true' );
        } else {
            $form->_isRedact = false;
            $template->assign( '_isRedact', 'false' );
        }
        
        // first get all case information
        $case = $form->caseInfo( $clientID, $caseID );
        $template->assign_by_ref( 'case', $case );
        
        if ( $params['include_activities'] == 1 ) {
            $template->assign( 'includeActivities', 'All' );
        } else {
            $template->assign( 'includeActivities', 'Missing activities only' );
        }
		
        $xml = $form->retrieve( $case['caseType'] );
        
        $activityTypes = $form->getActivityTypes( $xml, $activitySetName );
        if ( ! $activityTypes ) {
            return false;
        }        
        
        // next get activity set Informtion
        $activitySet = array( 'label'             => $form->getActivitySetLabel( $xml, $activitySetName ),
                              'includeActivities' => 'All',
                              'redact'            => 'false' );
        $template->assign_by_ref( 'activitySet', $activitySet );
        
        //now collect all the information about activities
        $activities = array( );
        $form->getActivities( $clientID, $caseID, $activityTypes, $activities );
        $template->assign_by_ref( 'activities', $activities );
        
        // now run the template
        $contents = $template->fetch( 'CRM/Case/XMLProcessor/Report.tpl' );
        return $contents;
    }

    function printCaseReport( ) {
        
        $caseID            = CRM_Utils_Request::retrieve( 'caseID' , 'Positive', CRM_Core_DAO::$_nullObject );
        $clientID          = CRM_Utils_Request::retrieve( 'cid'    , 'Positive', CRM_Core_DAO::$_nullObject );
        $activitySetName   = CRM_Utils_Request::retrieve( 'asn'    , 'String'  , CRM_Core_DAO::$_nullObject );
        $isRedact          = CRM_Utils_Request::retrieve( 'redact' , 'Boolean' , CRM_Core_DAO::$_nullObject );
        $includeActivities = CRM_Utils_Request::retrieve( 'all'    , 'Positive', CRM_Core_DAO::$_nullObject );
        $params = array( );
        
        $report = new CRM_Case_XMLProcessor_Report( $isRedact ); 
        
        if ( $includeActivities ) {
            $params['include_activities'] = 1;
        } 
        
        if ( $isRedact ) {
	        $params['is_redact'] = 1; 
        } 
         
        $contents = self::getCaseReport( $clientID,
                                         $caseID,
                                         $activitySetName,
                                         $params,
                                         $report );

        $template = CRM_Core_Smarty::singleton( );
        
        //get case related relationships (Case Role)
        require_once('CRM/Case/BAO/Case.php');
        $caseRelationships = CRM_Case_BAO_Case::getCaseRoles( $clientID, $caseID );
       
        $caseType  = CRM_Case_PseudoConstant::caseTypeName( $caseID );
        require_once ('CRM/Case/XMLProcessor/Process.php');
        $xmlProcessor = new CRM_Case_XMLProcessor_Process( );
        $caseRoles    = $xmlProcessor->get( $caseType['name'], 'CaseRoles' );

        foreach( $caseRelationships as $key => &$value ) {          
            if ( CRM_Utils_Array::value($value['relation_type'], $caseRoles) ) {
                unset( $caseRoles[$value['relation_type']] );
            }

            $value['name']  = self::redact( $value['name'] , $isRedact, true );
            $value['email'] = self::redact( $value['email'], $isRedact, true ); 
        }
        
        $caseRoles['client'] = CRM_Case_BAO_Case::getcontactNames( $caseID );
        $caseRoles['client']['sort_name']  = self::redact( $caseRoles['client']['sort_name']  , $isRedact, true );
        $caseRoles['client']['email'] = self::redact( $caseRoles['client']['email'] , $isRedact, true  );
        
        // Retrieve ALL client relationships
        $relClient = CRM_Contact_BAO_Relationship::getRelationship( $clientID,
                                                                    CRM_Contact_BAO_Relationship::CURRENT,
                                                                    0, 0, 0, null, null, false);
        $otherRelationships = array();
        foreach($relClient as $r) {
            
            $r['name']  = self::redact( $r['name'], $isRedact, true );

            if (CRM_Utils_Array::value('email', $r)) {
                $r['email'] = self::redact( $r['email'], $isRedact, true );
            }
            
            if ( ! array_key_exists( $r['id'], $caseRelationships ) ) {
                $otherRelationships[] = $r;
            }
        }
        
        // Now global contact list that appears on all cases.
        $globalGroupInfo = array();
        $relGlobal = CRM_Case_BAO_Case::getGlobalContacts($globalGroupInfo);
        foreach($relGlobal as &$r) {
         $r['sort_name'] = self::redact( $r['sort_name'], $isRedact, true );
        }
        
        $template->assign( 'caseRelationships', $caseRelationships );
        $template->assign( 'caseRoles', $caseRoles );
        $template->assign( 'otherRelationships', $otherRelationships);
        $template->assign( 'globalRelationships', $relGlobal);
        $template->assign( 'globalGroupInfo', $globalGroupInfo);
        
        require_once 'CRM/Case/Audit/Audit.php';
        $printReport = Audit::run( $contents, $clientID, $caseID, true );
        echo $printReport;
        exit( );
    }    
}


