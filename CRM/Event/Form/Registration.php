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
 *
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration extends CRM_Core_Form
{
    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;

    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /**
     * the mode that we are in
     * 
     * @var string
     * @protect
     */
    public $_mode;

    /**
     * the values for the contribution db object
     *
     * @var array
     * @protected
     */
    public $_values;

    /**
     * The params submitted by the form and computed by the app
     *
     * @var array
     * @protected
     */
    protected $_params;

    /** 
     * The fields involved in this contribution page
     * 
     * @var array 
     * @protected 
     */ 
    protected $_fields;

    /**
     * The billing location id for this contribiution page
     *
     * @var int
     * @protected
     */
    protected $_bltID;

    /**
     * Price Set ID, if the new price set method is used
     *
     * @var int
     * @protected
     */
    protected $_priceSetId;

    /**
     * Array of fields for the price set
     *
     * @var array
     * @protected
     */
    protected $_priceSet;

    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String', $this, false );
        
        // current mode
        $this->_mode = ( $this->_action == 1024 ) ? 'test' : 'live';
        
        $this->_values = $this->get( 'values' );
        $this->_fields = $this->get( 'fields' );
        $this->_bltID  = $this->get( 'bltID'  );
        $this->_priceSetId = $this->get( 'priceSetId' );
        $this->_priceSet = $this->get( 'priceSet' ) ;

        $config  =& CRM_Core_Config::singleton( );

        if ( ! $this->_values ) {
            // get all the values from the dao object
            $this->_values = array( );

            //retrieve event information
            $params = array( 'id' => $this->_id );
            $ids = array();
            
            require_once 'CRM/Event/BAO/Participant.php';
            $eventFull = CRM_Event_BAO_Participant::eventFull( $this->_id );
            if ( $eventFull ) {
                CRM_Utils_System::fatal( $eventFull );
            }

            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $this->_values['event']);

            $now = time( );

            $startDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value('registration_start_date',$this->_values['event']) );
            if ( $startDate &&
                 $startDate >= $now ) {
                CRM_Core_Error::fatal( ts( 'You cannot register for this event currently' ) );
            }

            $endDate = CRM_Utils_Date::unixTime( CRM_Utils_Array::value('registration_end_date',$this->_values['event']) );
            if ( $endDate &&
                 $endDate < $now ) {
                CRM_Core_Error::fatal( ts( 'You cannot register for this event currently' ) );
            }

            //retrieve custom information
            $eventPageID = CRM_Core_DAO::getFieldValue( 'CRM_Event_DAO_EventPage',
                                                        $this->_id,
                                                        'id',
                                                        'event_id' );
            
            // get price info
            require_once 'CRM/Core/BAO/PriceSet.php';
            $priceSetId = CRM_Core_BAO_PriceSet::getFor( 'civicrm_event_page', $eventPageID );
            if ( $priceSetId ) {
                $this->_priceSetId = $priceSetId;
                $priceSet = CRM_Core_BAO_PriceSet::getSetDetail($priceSetId);
                require_once 'CRM/Core/BAO/PriceField.php';
                if ( isset($priceSet[$priceSetId]['fields']) ) {
                    foreach ( array_keys($priceSet[$priceSetId]['fields']) as $fieldId ) {
                        $priceSet[$priceSetId]['fields'][$fieldId]['options'] = CRM_Core_BAO_PriceField::getOptions($fieldId, false);
                          }
                     }
                $this->_priceSet = CRM_Utils_Array::value($priceSetId,$priceSet);
                $this->_values['custom'] = CRM_Utils_Array::value($priceSetId,$priceSet);
                $this->set('priceSetId', $this->_priceSetId);
                $this->set('priceSet', $this->_priceSet);
            } else {
                require_once 'CRM/Core/BAO/CustomOption.php'; 
                CRM_Core_BAO_CustomOption::getAssoc( 'civicrm_event_page', $eventPageID, $this->_values['custom'] );
            }

            // get the profile ids
            require_once 'CRM/Core/BAO/UFJoin.php'; 
            $ufJoinParams = array( 'entity_table' => 'civicrm_event',   
                                   'entity_id'    => $this->_id,   
                                   'weight'       => 1 ); 
            $this->_values['custom_pre_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams ); 
            $ufJoinParams['weight'] = 2; 
            $this->_values['custom_post_id'] = CRM_Core_BAO_UFJoin::findUFGroupId( $ufJoinParams );
    
            $params = array( 'event_id' => $this->_id );
            require_once 'CRM/Event/BAO/EventPage.php';
            CRM_Event_BAO_EventPage::retrieve($params, $this->_values['event_page']);
            
            // get the billing location type
            $locationTypes =& CRM_Core_PseudoConstant::locationType( );
            $this->_bltID = array_search( ts('Billing'),  $locationTypes );
            if ( ! $this->_bltID ) {
                CRM_Core_Error::fatal( ts( 'Please set a location type of %1', array( 1 => 'Billing' ) ) );
            }
            $this->set( 'bltID', $this->_bltID );

            if ( ( $config->paymentBillingMode & CRM_Core_Payment::BILLING_MODE_FORM ) && $this->_values['event']['is_monetary'] ) {
                $this->setCreditCardFields( );
            }

            $params = array( 'entity_id' => $this->_id ,'entity_table' => 'civicrm_event');
            require_once 'CRM/Core/BAO/Location.php';
            $location = CRM_Core_BAO_Location::getValues($params, $this->_values, $ids, self::LOCATION_BLOCKS);

            $this->set( 'values', $this->_values );
            $this->set( 'fields', $this->_fields );
        }

        // check if form is active
        if ( ! $this->_values['event']['is_active'] ) {
            // form is inactive, die a fatal death
            CRM_Core_Error::fatal( ts( 'The page you requested is currently unavailable.' ) );
        }
      
        // make sure we have a valid payment class, else abort
        if ( $this->_values['event']['is_monetary'] && ! $config->paymentFile ) {
            CRM_Core_Error::fatal( ts( 'CIVICRM_CONTRIBUTE_PAYMENT_PROCESSOR is not set.' ) );
        }

        $this->_contributeMode = $this->get( 'contributeMode' );
        $this->assign( 'contributeMode', $this->_contributeMode );

        // setting CMS page title
        CRM_Utils_System::setTitle($this->_values['event']['title']);  
        $this->assign( 'title', $this->_values['event']['title'] );

        $this->assign('paidEvent', $this->_values['event']['is_monetary']);

        // we do not want to display recently viewed items on Registration pages
        $this->assign( 'displayRecent' , false );

        // assign all event properties so wizard templates can display event info.
        $this->assign('event', $this->_values['event']);
        $this->assign('eventPage', $this->_values['event_page']);
        $this->assign('location',$this->_values['location']);
        $this->assign( 'bltID', $this->_bltID );
        $showLocation = CRM_Utils_Array::value('show_location',$this->_values['event_page'])  ;
        $this->assign( 'showLocation',$showLocation );
    }

    /** 
     * create all fields needed for a credit card transaction
     *                                                           
     * @return void 
     * @access public 
     */ 
    function setCreditCardFields( ) 
    {
        $bltID = $this->_bltID;

        $this->_fields['billing_first_name']         = array( 'htmlType'   => 'text', 
                                                              'name'       => 'billing_first_name', 
                                                              'title'      => ts('Billing First Name'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ),
                                                              'is_required'=> true );
        
        $this->_fields['billing_middle_name']        = array( 'htmlType'   => 'text', 
                                                              'name'       => 'billing_middle_name', 
                                                              'title'      => ts('Billing Middle Name'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                              'is_required'=> false );
        
        $this->_fields['billing_last_name']          = array( 'htmlType'   => 'text', 
                                                              'name'       => 'billing_last_name', 
                                                              'title'      => ts('Billing Last Name'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                              'is_required'=> true );
        
        $this->_fields["street_address-{$bltID}"]    = array( 'htmlType'   => 'text', 
                                                              'name'       => "street_address-{$bltID}",
                                                              'title'      => ts('Street Address'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                              'is_required'=> true );
        
        $this->_fields["city-{$bltID}"]              = array( 'htmlType'   => 'text', 
                                                              'name'       => "city-{$bltID}",
                                                              'title'      => ts('City'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                              'is_required'=> true );
        
        $this->_fields["state_province_id-{$bltID}"] = array( 'htmlType'   => 'select', 
                                                              'name'       => "state_province_id-{$bltID}",
                                                              'title'      => ts('State / Province'), 
                                                              'attributes' => array( '' => ts( '- select -' ) ) +
                                                              CRM_Core_PseudoConstant::stateProvince( ),
                                                              'is_required'=> true );
        
        $this->_fields["postal_code-{$bltID}"]       = array( 'htmlType'   => 'text', 
                                                              'name'       => "postal_code-{$bltID}",
                                                              'title'      => ts('Postal Code'), 
                                                              'attributes' => array( 'size' => 30, 'maxlength' => 60 ), 
                                                              'is_required'=> true );
        
        $this->_fields["country_id-{$bltID}"]        = array( 'htmlType'   => 'select', 
                                                              'name'       => "country_id-{$bltID}", 
                                                              'title'      => ts('Country'), 
                                                              'attributes' => array( '' => ts( '- select -' ) ) + 
                                                              CRM_Core_PseudoConstant::country( ),
                                                              'is_required'=> true );
        
        $this->_fields['credit_card_number']         = array( 'htmlType'   => 'text', 
                                                              'name'       => 'credit_card_number', 
                                                              'title'      => ts('Card Number'), 
                                                              'attributes' => array( 'size' => 20, 'maxlength' => 20 ), 
                                                              'is_required'=> true );
        
        $this->_fields['cvv2']                       = array( 'htmlType'   => 'text', 
                                                              'name'       => 'cvv2', 
                                                              'title'      => ts('Security Code'), 
                                                              'attributes' => array( 'size' => 5, 'maxlength' => 10 ), 
                                                              'is_required'=> true );
        
        $this->_fields['credit_card_exp_date']       = array( 'htmlType'   => 'date', 
                                                              'name'       => 'credit_card_exp_date', 
                                                              'title'      => ts('Expiration Date'), 
                                                              'attributes' => CRM_Core_SelectValues::date( 'creditCard' ),
                                                              'is_required'=> true );
        
        require_once 'CRM/Contribute/PseudoConstant.php';
        $creditCardType = array( ''           => '- select -') + CRM_Contribute_PseudoConstant::creditCard( );
        $this->_fields['credit_card_type'] = array( 'htmlType'   => 'select', 
                                                    'name'       => 'credit_card_type', 
                                                    'title'      => ts('Card Type'), 
                                                    'attributes' => $creditCardType,
                                                    'is_required'=> true );
    }
    
    /** 
     * assign the minimal set of variables to the template
     *                                                           
     * @return void 
     * @access public 
     */ 
    function assignToTemplate( ) 
    {
        if ( CRM_Utils_Array::value( 'billing_first_name', $this->_params ) ) {
            $name = $this->_params['billing_first_name'];
        }
        
        if ( CRM_Utils_Array::value( 'billing_middle_name', $this->_params ) ) {
            $name .= " {$this->_params['billing_middle_name']}";
        }

        if ( CRM_Utils_Array::value( 'billing_last_name', $this->_params ) ) {
            $name .= " {$this->_params['billing_last_name']}";
            
            $this->assign( 'name', $name );
            $this->set( 'name', $name );
        }       
        
        $vars = array( 'amount', 'currencyID', 'credit_card_type', 
                       'trxn_id', 'amount_level', 'receive_date' );
        
        foreach ( $vars as $v ) {
            if ( CRM_Utils_Array::value( $v, $this->_params ) ) {
                if ( $v == 'receive_date' ) {
                     $this->assign( $v,  CRM_Utils_Date::mysqlToIso( $this->_params[$v] ) );
                } else {
                    $this->assign( $v, $this->_params[$v] );
                }
            }
        }

        // assign the address formatted up for display
        $addressParts  = array( "street_address-{$this->_bltID}",
                                "city-{$this->_bltID}",
                                "postal_code-{$this->_bltID}",
                                "state_province-{$this->_bltID}",
                                "country-{$this->_bltID}");
        $addressFields = array();
        foreach ($addressParts as $part) {
            list( $n, $id ) = explode( '-', $part );
            if ( isset ( $this->_params[$part] ) ) {
                $addressFields[$n] = $this->_params[$part];
            }
        }
        require_once 'CRM/Utils/Address.php';
        $this->assign('address', CRM_Utils_Address::format($addressFields));

        if ( $this->_contributeMode == 'direct' ) {
            $date = CRM_Utils_Date::format( $this->_params['credit_card_exp_date'] );
            $date = CRM_Utils_Date::mysqlToIso( $date );
            $this->assign( 'credit_card_exp_date', $date );
            $this->assign( 'credit_card_number',
                           CRM_Utils_System::mungeCreditCard( $this->_params['credit_card_number'] ) );
        }

        $this->assign( 'email', $this->controller->exportValue( 'Register', "email-{$this->_bltID}" ) );

        // assign is_email_confirm to templates
        if ( isset ($this->_values['event_page']['is_email_confirm'] ) ) {
            $this->assign( 'is_email_confirm', $this->_values['event_page']['is_email_confirm'] );
        }
    }
    /**  
     * Function to add the custom fields
     *  
     * @return None  
     * @access public  
     */ 
    function buildCustom( $id, $name ) 
    {
        if ( $id ) {
            require_once 'CRM/Core/BAO/UFGroup.php';
            require_once 'CRM/Profile/Form.php';
            $session =& CRM_Core_Session::singleton( );
            $contactID = $session->get( 'userID' );
            if ( $contactID ) {
                if ( CRM_Core_BAO_UFGroup::filterUFGroups($id)  ) {
                    $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                    $this->assign( $name, $fields );
                    foreach($fields as $key => $field) {
                        CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                        $this->_fields[$key] = $field;
                    }
                }
            } else {
                $fields = CRM_Core_BAO_UFGroup::getFields( $id, false,CRM_Core_Action::ADD ); 
                $this->assign( $name, $fields );
                foreach($fields as $key => $field) {
                    CRM_Core_BAO_UFGroup::buildProfile($this, $field,CRM_Profile_Form::MODE_CREATE);
                    $this->_fields[$key] = $field;
                }
            }
        }
    }

}

?>
