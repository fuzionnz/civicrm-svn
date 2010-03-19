<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to support Proximity Searches
 */
class CRM_Contact_Form_Task_ProximityCommon extends CRM_Contact_Form_Task {
    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     */
    protected $_id;

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( $form, $proxSearch ) {
        // is proximity search required (2) or optional (1)?
        $proxRequired  = ( $proxSearch == 2 ? true : false);
        $form->assign('proximity_search', true);

        $form->add( 'text', 'prox_street_address', ts( 'Street Address' ), null, $proxRequired );

        $form->add( 'text', 'prox_city', ts( 'City' ), null, $proxRequired );

        $form->add( 'text', 'prox_postal_code', ts( 'Postal Code' ), null, $proxRequired );

        self::setDefaultValues( $form );
        if ( $defaults['prox_country_id'] ) {
            $stateProvince = array( '' => ts('- select -') ) + CRM_Core_PseudoConstant::stateProvinceForCountry( $defaults['prox_country_id'] );
        } else {
            $stateProvince = array( '' => ts('- select -') ) + CRM_Core_PseudoConstant::stateProvince( );
        }
        $form->add('select', 'prox_state_province_id', ts('State/Province'), $stateProvince, $proxRequired);        
        
        $country = array( '' => ts('- select -') ) + CRM_Core_PseudoConstant::country( );
        $form->add( 'select', 'prox_country_id', ts('Country'), $country, $proxRequired );
        
        $form->add( 'text', 'prox_distance', ts( 'Distance (in km)' ), null, $proxRequired );

        // state country js, CRM-5233
        require_once 'CRM/Core/BAO/Address.php';
        $stateCountryMap   = array( );
        $stateCountryMap[] = array( 'state_province' => 'prox_state_province_id',
                                    'country'        => 'prox_country_id' );
        CRM_Core_BAO_Address::addStateCountryMap( $stateCountryMap ); 
        CRM_Core_BAO_Address::fixAllStateSelects( $this, $defaults );   
        $form->addFormRule( array( 'CRM_Contact_Form_Task_ProximityCommon',  'formRule' ), $form );
    }
    
    /**
     * global form rule
     *
     * @param array $fields  the input form values
     * @param array $files   the uploaded files if any
     * @param array $options additional user data
     *
     * @return true if no errors, else array of errors
     * @access public
     * @static
     */
     static function formRule( $fields, $files, $form ) 
     {
         $errors = array( );
         // if use_household_address option is checked, make sure 'valid household_name' is also present.
         if ( CRM_Utils_Array::value('prox_distance',$fields ) && 
              !CRM_Utils_Array::value( 'prox_postal_code', $fields ) ) {
             $errors["prox_distance"] = ts("If you want to search by distance from an address, please enter a postal code.");
         }
         return empty($errors) ? true : $errors;
     }     
    
    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues( $form ) {
        $defaults = array();
        require_once 'CRM/Core/Config.php';
    	$config = CRM_Core_Config::singleton( );
    	$countryDefault = $config->defaultContactCountry;
    	
    	if ($countryDefault) {
    		$defaults['prox_country_id'] = $countryDefault;
    	}
        $form->setDefaults( $defaults );
    }

}


