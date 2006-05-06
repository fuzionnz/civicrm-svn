<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

/** 
 *  this file contains functions for Student
 */


require_once 'CRM/Quest/DAO/Student.php';

class CRM_Quest_BAO_Student extends CRM_Quest_DAO_Student {

    
    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * function to add/update student Information
     *
     * @param array $params reference array contains the values submitted by the form
     * @param array $ids    reference array contains the id
     * 
     * @access public
     * @static 
     * @return object
     */
    static function &create(&$params, &$ids) {
        
        $dao = & new CRM_Quest_DAO_Student();
        $dao->copyValues($params);
        if( $ids['id'] ) {
            $dao->id = $ids['id'];
        }
        $dao->save();
        return $dao;
    }

    static function retrieve( &$params, &$defaults, &$ids ) {
        $dao = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $params['contact_id'];
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao, $defaults );
            $ids['student_id'] = $dao->id;
            $names = array( 'citizenship_status_id' => array( 'newName' => 'citizenship_status',
                                                              'groupName' => 'citizenship_status' ),
                            'gpa_id'                => array( 'newName' => 'gpa', 
                                                              'groupName' => 'gpa' ),
                            'ethnicity_id_1'        => array( 'newName' => 'ethnicity_1', 
                                                              'groupName' => 'ethnicity' ),
                            'ethnicity_id_2'        => array( 'newName' => 'ethnicity_2', 
                                                              'groupName' => 'ethnicity' ),
                            'parent_grad_college_id'=> array( 'newName' => 'parent_grad_college', 
                                                              'groupName' => 'parent_grad_college' ),
                            'educational_interest'  => array( 'newName' => 'educational_interest_display', 
                                                              'groupName' => 'educational_interest' ),
                            'college_interest'      => array( 'newName' => 'college_interest_display', 
                                                              'groupName' => 'college_interest' ),
                            'fed_lunch_id'          => array( 'newName' => 'fed_lunch', 
                                                              'groupName' => 'fed_lunch' )
                            );
            require_once 'CRM/Core/OptionGroup.php';
            CRM_Core_OptionGroup::lookupValues( $defaults, $names, false );
        }
     
    }

    static function exportableFields( ) {
        $fields = CRM_Quest_DAO_Student::export( );
        return $fields;
    }

   
     /**
     * function retrieve student Information
     *
     * @param Int $contactId conatct id
     * @param array $defaults   reference array contains all student Information
     * 
     * @access public
     * @static 
     * @return void
     */
    static function studentDetails( $contactID ,&$defaults ) {
        
        require_once 'CRM/Quest/DAO/Student.php';

        self::individual( $contactID, $defaults );
        
        self::student( $contactID, $defaults );

        self::guardian( $contactID, $defaults, true );

        self::guardian( $contactID, $defaults, false );

        self::income( $contactID, $defaults );

        //highschool & ohereschool details 
        
        $highschoolDetials  = array();
        $otherschoolDetails = array();
        $organization      = array(); 
        require_once 'CRM/Contact/BAO/Relationship.php';
        require_once  'CRM/Core/BAO/CustomGroup.php';
        $relationship  = CRM_Contact_BAO_Relationship::getRelationship( $contactID );
       
        foreach( $relationship as $key => $value ) {
            if ($value['relation'] == 'Student of' ) {
                $params = array( 'contact_id' => $value['cid'],
                                 'id'         => $value['cid']);
                $ids = array();
                $orgDetails = array();
                CRM_Contact_BAO_Contact::retrieve( &$params, &$orgDetails, &$ids );
                $groupTree = & CRM_Core_BAO_CustomGroup::getTree('Organization',$value['cid'],0);
                CRM_Core_BAO_CustomGroup::setDefaults( $groupTree,$orgDetails, true, false );
                $organization[$key] = $orgDetails;
            }
        }
        
        foreach( $organization as $key => $value ) {
            if ( $value['custom_4'] == 'Highschool' ){
                $highschoolDetials[] = $value;
            } else {
                $otherschoolDetails[] = $value;
            }
        }
        $defaults['HighSchool' ]  = $highschoolDetials;
        $defaults['OtherSchool'] = $otherschoolDetails;

        //test details
        $testDetails = array();
        
        require_once 'CRM/Quest/DAO/Test.php';
        $testDAO = & new CRM_Quest_DAO_Test();
        $testDAO->contact_id = $contactID;
        $testDAO->find( );
        while( $testDAO->fetch() ) {
            CRM_Core_DAO::storeValues( $testDAO, $testDetails[$testDAO->id]);
            $names = array('test_id'         => array( 'newName' => 'test',
                                                       'groupName' => 'test' ),
                           'score_composite' => array( 'newName' => 'score',
                                                       'groupName' => 'ap_score' ),
                           );
            CRM_Core_OptionGroup::lookupValues( $testDetails[$testDAO->id] , $names, false);
        }
        $defaults['Test'] = $testDetails;
        
        //essay 
        require_once 'CRM/Quest/DAO/Essay.php';
        $essay = array();
        $essayDAO = & new CRM_Quest_DAO_Essay();
        $essayDAO->contact_id;
        if ( $essayDAO->find(true) ) {
            $essay[] = $essayDAO->essay;
        }
        $defaults['Essay'] = $essay;
    }

    static function individual( $contactID, &$details ) {
        $params = array( 'contact_id' => $contactID,
                         'id'         => $contactID );
        $ids = array();
        $individual = array();

        CRM_Contact_BAO_Contact::retrieve( &$params, &$individual, &$ids );
        CRM_Contact_BAO_Contact::resolveDefaults( $individual );
        // CRM_Core_Error::debug( 'i', $individual );

        $details['Individual'] = array( );
        
        $properties = array( 'sort_name', 'display_name', 'nick_name',
                             'prefix', 'suffix',
                             'first_name', 'middle_name', 'last_name',
                             'gender', 'birth_date', );
        foreach ( $properties as $key ) {
            $details['Individual'][$key] = $individual[$key];
        }

        // get address information
        $properties = array( 'street_address', 'city', 'state_province', 'postal_code', 'postal_code_suffix', 'country' );
        for ( $i = 1; $i <= 2; $i++ ) {
            $details["Address_$i"] = array( );
            foreach ( $properties as $key ) {
                $details["Address_$i"][$key] = $individual['location'][$i]['address'][$key];
            }
            $details["Email_$i"] = $individual['location'][$i]['email'][1]['email'];
            $details["Phone_$i"] = $individual['location'][$i]['phone'][1]['phone'];
        }
    }

    static function student( $contactID, &$details ) {
        $dao             = & new CRM_Quest_DAO_Student();
        $dao->contact_id = $contactID;
        if ( ! $dao->find(true) ) {
            CRM_Core_Error::fatal( ts( "Student with id %1 does not exist", array( 1 => $contactID ) ) );
        }

        $studentDetails    = array();
        CRM_Core_DAO::storeValues( $dao, $studentDetails);

        $names = array( 'citizenship_status_id'  => array( 'newName' => 'citizenship_status',
                                                           'groupName' => 'citizenship_status' ),
                        'home_area_id'           => array( 'newName' => 'home_area', 
                                                           'groupName' => 'home_area' ),
                        'parent_grad_college_id' => array( 'newName' => 'parent_grad_college', 
                                                           'groupName' => 'parent_grad_college' ),
                        'internet_access_id'     => array( 'newName' => 'internet_access', 
                                                           'groupName' => 'internet_access' ),
                        'study_method_id'        => array( 'newName' => 'study_method', 
                                                           'groupName' => 'study_method' ),
                        'educational_interest'   => array( 'newName' => 'educational_interest_display', 
                                                           'groupName' => 'educational_interest' ),
                        'college_type'           => array( 'newName' => 'college_type_display', 
                                                           'groupName' => 'college_type' ),
                        'college_interest'       => array( 'newName' => 'college_interest_display', 
                                                           'groupName' => 'college_interest' ),
                        'gpa_id'                => array( 'newName' => 'gpa', 
                                                          'groupName' => 'gpa' ),
                        'ethnicity_id_1'        => array( 'newName' => 'ethnicity_1', 
                                                          'groupName' => 'ethnicity' ),
                        'ethnicity_id_2'        => array( 'newName' => 'ethnicity_2', 
                                                          'groupName' => 'ethnicity' ),
                        'fed_lunch_id'          => array( 'newName' => 'fed_lunch', 
                                                          'groupName' => 'fed_lunch' ),
                        'class_rank_percent_id' => array( 'newName' => 'class_rank_percent', 
                                                          'groupName' => 'class_rank_percent' ),
                        'class_rank_percent_id' => array( 'newName' => 'class_rank_percent', 
                                                          'groupName' => 'class_rank_percent' ),
                        'award_ranking_1_id'    => array( 'newName' => 'award_ranking_1', 
                                                          'groupName' => 'award_ranking' ),
                        'award_ranking_2_id'    => array( 'newName' => 'award_ranking_2', 
                                                          'groupName' => 'award_ranking' ),
                        'award_ranking_3_id'    => array( 'newName' => 'award_ranking_3', 
                                                          'groupName' => 'award_ranking' ),
                        'test_tutoring'         => array( 'newName' => 'test_tutoring_display', 
                                                          'groupName' => 'test' ), 
                        );

        require_once 'CRM/Core/OptionGroup.php';
        CRM_Core_OptionGroup::lookupValues( $studentDetails, $names, false);

        $details['Student']    = array( );
        foreach ( $names as $key => $value ) {
            $details['Student'][$value['newName']] = $studentDetails[$value['newName']];
        }

        //fix for country
        $countryIds = array( 'citizenship_country' => 'citizenship_country_id',
                             'growup_country'      => 'growup_country_id',
                             'nationality_country' => 'nationality_country_id' );

        require_once 'CRM/Core/PseudoConstant.php'; 
        foreach( $countryIds as $key => $val ) {
            if( $studentDetails[$val] ) {
                $details['Student'][$key] = CRM_Core_PseudoConstant::country($studentDetails[$val]);
            } else {
                $details['Student'][$key] = null;
            }
        }

        // add other student details
        $properties = array( 'years_in_us', 'first_language', 'primary_language', 
                             'internet_access_other',
                             'is_home_computer', 'is_home_internet', 'is_take_SAT_ACT',
                             'educational_interest_other', 'college_interest_other',
                             'is_class_ranking', 'class_rank', 'class_num_students',
                             'gpa_explanation', 'test_tutoring', 'household_income_total',
                             'number_siblings', 'financial_aid_applicant',
                             'register_standarized_tests' );
        foreach ( $properties as $key ) {
            $details['Student'][$key] = $studentDetails[$key];
        }

        // BUG: we need to deal with this
        $multiSelectElements = array( 'educational_interest', 'college_type', 'college_interest' );
                                      
    }


    static function guardian( $contactID, &$details, $isGuardian ) {

        require_once 'CRM/Quest/DAO/Person.php';
        $person             =& new CRM_Quest_DAO_Person();
        $person->contact_id =  $contactID;
        $person->is_sibling =  $isGuardian ? 0 : 1;
        $person->orderby( 'relationship_id asc' );
        $person->find();

        $prefix = $isGuardian ? 'Guardian' : 'Sibling';

        $properties = array( 'first_name', 'last_name', 'is_deceased',
                             'job_organization', 'job_occupation', 'job_current_years',
                             'college_name', 'college_grad_year', 'college_major',
                             'prof_school_name' );
        $dates = array( 'birth_date', 'deceased_year', 'college_grad_year', 'prof_grad_year' );

        $names = array('relationship_id'                => array( 'newName' => 'relationship',
                                                                  'groupName' => 'relationship' ),
                       'marital_status_id'              => array( 'newName' => 'marital_status',
                                                                  'groupName' => 'marital_status' ),               
                       'industry_id'                    => array( 'newName' => 'industry',
                                                                  'groupName' => 'industry_id' ),
                       'highest_school_level_id'        => array( 'newName' => 'highest_school_level',
                                                                  'groupName' => 'highest_school_level' ),
                       );
        if ( ! $isGuardian ) {
            $names['relationship_id']['groupName'] = 'sibling_relationship';
        }


        $count = 1;
        while( $person->fetch() ) {
            $personDetails = array( );
            CRM_Core_DAO::storeValues( $person, $personDetails );

            CRM_Core_OptionGroup::lookupValues( $personDetails, $names, false);
            $details["{$prefix}_$count"] = array( );

            foreach ( $properties as $key ) {
                $details["{$prefix}_$count"][$key] = $personDetails[$key];
            }

            foreach ( $dates as $date ) {
                $details["{$prefix}_$count"][$key] = CRM_Utils_Date::format( $personDetails[$key], '-' );
            }

            if ( $personDetails['college_country_id'] ) {
                $details["{$prefix}_$count"]['college_country'] = CRM_Core_PseudoConstant::country($personDetails['college_country_id']);
            } else {
                $details["{$prefix}_$count"]['college_country'] = null;
            }

            foreach ( $names as $key => $value ) {
                $details["{$prefix}_$count"][$value['newName']] = $personDetails[$value['newName']];
            }

            $count++;
        }
    }

    static function income( $contactID, &$details ) {

        require_once 'CRM/Quest/DAO/Income.php';

        $dao =& new CRM_Quest_DAO_Person( );
        $dao->contact_id = $contactID;
        $dao->is_income_source   = true;
        $dao->find( );
        
        $names = array('source_1_id' => array( 'newName'   => 'source_1',
                                               'groupName' => 'type_of_income' ),
                       'source_2_id' => array( 'newName'   => 'source_2',
                                               'groupName' => 'type_of_income' ),
                       'source_3_id' => array( 'newName'   => 'source_3',
                                               'groupName' => 'type_of_income' ),
                       );

        $properties = array( 'job_1', 'job_2', 'job_3', 'amount_1', 'amount_2', 'amount_3' );

        $count = 1;
        while( $dao->fetch() ) {
            $incomeDAO =& new CRM_Quest_DAO_Income( );
            $incomeDAO->person_id = $dao->id;
            $incomeDAO->find(true);
            
            $incomeDetails = array( );
            CRM_Core_DAO::storeValues( $incomeDAO, $incomeDetails );

            $details["Income_$count"]['first_name'] = $dao->first_name;
            $details["Income_$count"]['last_name']  = $dao->last_name;

            foreach ( $properties as $key ) {
                $details["Income_$count"][$key] = $incomeDetails[$key];
            }

            CRM_Core_OptionGroup::lookupValues( $incomeDetails, $names, false);
            foreach ( $names as $key => $value ) {
                $details["Income_$count"][$value['newName']] = $incomeDetails[$value['newName']];
            }
            $count++;
        }
    }

}
    
?>