<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Registration.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_Registration_AdditionalParticipant extends CRM_Event_Form_Registration
{
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        parent::preProcess( );
        CRM_Utils_System::setTitle( 'Register Additional Participant' );
        $this->_lineItem = $this->get( 'lineItem' );
        //lineItem isn't set until Register postProcess
    }
    
    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( ) 
    {  
        $config =& CRM_Core_Config::singleton( );
        $this->add( 'text',
                    "email-{$this->_bltID}",
                    ts( 'Email Address' ),
                    array( 'size' => 30, 'maxlength' => 60 ) );
        
        if ( $this->_values['event']['is_monetary'] ) {
            require_once 'CRM/Event/Form/Registration/Register.php';
            CRM_Event_Form_Registration_Register::buildAmount( $this );
        }
        
        $this->buildCustom( $this->_values['custom_pre_id'] , 'customPre'  );
        $this->buildCustom( $this->_values['custom_post_id'], 'customPost' );
        //add buttons
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => 'Continue >>',
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true
                                        ),
                                array ( 'type'      => 'back',
                                        'name'      => ts('<< Go Back')
                                        ),
                                array ( 'type'       => 'next',
                                        'name'       => ts('Skip Participant >>'),
                                        'subName'    => 'skip' ),
                                )
                          );
        
        $this->addFormRule( array( 'CRM_Event_Form_Registration_AdditionalParticipant', 'formRule' ),
                            $this );
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
    static function formRule(&$fields, &$files, &$self) 
    {
        $errors = array( );
        //get the button name.
        $button = substr( $self->controller->getButtonName(), -4 );
        if ( $button != 'skip' ) {
            if ( empty( $fields["email-{$self->_bltID}"] ) ) {
                $errors["email-{$self->_bltID}"] = ts( 'Email Address is a required field.' );
            }
        }
        
        //get the complete params.
        $params = $self->get('params');
        //take the participant instance.
        $addParticipantNum = 1 + substr( $self->_name, 12 );
        if ( is_array( $params ) ) {
            foreach ( $params as $key => $value ) {
                if ( ( $value["email-{$self->_bltID}"] == $fields["email-{$self->_bltID}"] ) && $key != $addParticipantNum  ) {
                    $errors["email-{$self->_bltID}"] = ts( 'The Email Address should be unique for Additional Participant' );
                    break;
                }
            }
        }
        
        return $errors; 
    }
    
    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        //get the button name.
        $button = substr( $this->controller->getButtonName(), -4 );
        $this->_params  = array( );
        $this->_params =  $this->get( 'params' );
        //take the participant instance.
        $addParticipantNum = 1 + substr( $this->_name, 12 );
        if ( $button == 'skip' ) {
            $this->_params[$addParticipantNum] = 'skip';
        } else {
            $params = $this->controller->exportValues( $this->_name );  
            if ( $this->_values['event']['is_monetary'] ) {
                if ( empty( $params['priceSetId'] ) ) {
                    $params['amount_level'] = $this->_values['custom']['label'][array_search( $params['amount'], 
                                                                                              $this->_values['custom']['amount_id'])];
                    
                    $params['amount']       = $this->_values['custom']['value'][array_search( $params['amount'], 
                                                                                              $this->_values['custom']['amount_id'])];
                } else {
                    $lineItem = array( );
                    require_once 'CRM/Event/Form/Registration/Register.php';
                    CRM_Event_Form_Registration_Register::processPriceSetAmount( $this->_values['custom']['fields'], 
                                                                                 $params, $lineItem );
                    $this->_lineItem[] = $lineItem;
                    $this->set( 'lineItem', $this->_lineItem );
                }
            } else {
                if ( $this->_values['event']['default_role_id'] ) {
                    $params['participant_role_id'] = $this->_values['event']['default_role_id'];
                }
            }
            if ( ! isset( $params['participant_role_id'] ) && $this->_values['event']['default_role_id'] ) {
                $params['participant_role_id'] = $this->_values['event']['default_role_id'];
            }
            
            if ( CRM_Utils_Array::value( 'is_pay_later', $this->_params[0] ) ) {
                $params['is_pay_later']  = 1;
            }
            //build the params array.
            if ( array_key_exists( $addParticipantNum, $this->_params ) ) {
                $this->_params[$addParticipantNum] = $params;
            } else {
                $this->_params[] = $params; 
            }
        }
        //finally set the params.
        $this->set( 'params', $this->_params );
        
        //to check whether call processRegistration() 
        if ( !$this->_values['event']['is_monetary'] && CRM_Utils_Array::value( 'additional_participants', $this->_params[0] ) ) {
            $participant =  $this->_params[0]['additional_participants'] + 1;
            if ( count($this->_params) == $participant ) {
                require_once 'CRM/Event/Form/Registration/Register.php';
                CRM_Event_Form_Registration_Register::processRegistration(  $this->_params,  null );
            }
        }
    }
    
    function &getPages( &$controller )
    {
        $details = array( );
        $i = 0;
        $session =& CRM_Core_Session::singleton( );
        $additional = $session->get('addParticipant');
        
        for ( ; $i < $additional; $i++ ) {
            $details["Participant-{$i}"] = array( 'className' => 'CRM_Event_Form_Registration_AdditionalParticipant', 
                                                  'title'   => "Participant $i"
                                                  );
        }
                
        if ( ! $details ) {
            $details = array( );
        }
        return $details;
    } 

}
?>