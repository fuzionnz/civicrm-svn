<?php  
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
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
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */
 
abstract class CRM_Core_Payment {
    /**
     * how are we getting billing information?
     *
     * FORM   - we collect it on the same page
     * BUTTON - the processor collects it and sends it back to us via some protocol
     */
    const
        BILLING_MODE_FORM   = 1,
        BILLING_MODE_BUTTON = 2,
        BILLING_MODE_NOTIFY = 4;

    /**
     * which payment type(s) are we using?
     * 
     * credit card
     * direct debit
     * or both
     * 
     */
    const    
        PAYMENT_TYPE_CREDIT_CARD = 1,
        PAYMENT_TYPE_DIRECT_DEBIT = 2;

    /**
     * Subscription / Recurring payment Status
     * START, END
     * 
     */
    const    
        RECURRING_PAYMENT_START = 'START',
        RECURRING_PAYMENT_END   = 'END';

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    protected $_paymentProcessor;

    protected $_paymentForm = null;

    /**  
     * singleton function used to manage this object  
     *  
     * @param string $mode the mode of operation: live or test
     *  
     * @return object  
     * @static  
     *  
     */  
    static function &singleton( $mode = 'test', &$paymentProcessor, &$paymentForm = null, $force = false ) 
    {
        // make sure paymentProcessor is not empty
        // CRM-7424
        if ( empty( $paymentProcessor ) ) {
            return CRM_Core_DAO::$_nullObject;
        }

        $cacheKey = "{$mode}_{$paymentProcessor['id']}_".(int)isset( $paymentForm );
        if ( !isset( self::$_singleton[$cacheKey] ) || $force ) {
            $config = CRM_Core_Config::singleton( );
            $ext = new CRM_Core_Extensions();
            if ( $ext->isExtensionKey( $paymentProcessor['class_name'] ) ) {
                $paymentClass = $ext->keyToClass( $paymentProcessor['class_name'], 'payment' );
                require_once( $ext->classToPath( $paymentClass ) );
            } else {                
                $paymentClass = 'CRM_Core_' . $paymentProcessor['class_name'];
                require_once( str_replace( '_', DIRECTORY_SEPARATOR , $paymentClass ) . '.php' );
            }
            
            //load the object.
            self::$_singleton[$cacheKey] = eval( 'return ' . $paymentClass . '::singleton( $mode, $paymentProcessor );' );
            
            //load the payment form for required processor.
            if ( $paymentForm !== null ) {
                self::$_singleton[$cacheKey]->setForm( $paymentForm );
            }
        }
        
        return self::$_singleton[$cacheKey];
    }
    
    /**
     * Setter for the payment form that wants to use the processor
     *
     * @param obj $paymentForm
     * 
     */
    function setForm( &$paymentForm ) {
        $this->_paymentForm = $paymentForm;
    }
    
    /**
     * Getter for payment form that is using the processor
     *
     * @return obj  A form object
     */
    function getForm( ) {
        return $this->_paymentForm;
    }

    /**
     * Getter for accessing member vars
     *
     */
    function getVar( $name ) {
        return isset( $this->$name ) ? $this->$name : null;
    }

    /**
     * This function collects all the information from a web/api form and invokes
     * the relevant payment processor specific functions to perform the transaction
     *
     * @param  array $params assoc array of input parameters for this transaction
     *
     * @return array the result in an nice formatted array (or an error object)
     * @abstract
     */
    abstract function doDirectPayment( &$params );

    /**
     * This function checks to see if we have the right config values
     *
     * @param  string $mode the mode we are operating in (live or test)
     *
     * @return string the error message if any
     * @public
     */
    abstract function checkConfig( );

    static function paypalRedirect( &$paymentProcessor ) {
        if ( ! $paymentProcessor ) {
            return false;
        }

        if ( isset( $_GET['payment_date'] )                                       &&
             isset( $_GET['merchant_return_link'] )                               &&
             CRM_Utils_Array::value( 'payment_status', $_GET ) == 'Completed'     &&
             $paymentProcessor['payment_processor_type'] == "PayPal_Standard" ) {
            return true;
        }
        
        return false;
    }

    /**
     * Page callback for civicrm/payment/ipn
     * @public
     */
    static function handleIPN( ) {
        self::handlePaymentMethod( 
            'PaymentNotification', 
            array(
                'processor_name' => @$_GET['processor_name'],
                'mode'           => @$_GET['mode']
            )
        );
    }
    
    /**
     * Payment callback handler
     * Load requested payment processor and call that processor's handle<$method> method
     *
     * @public
     */
    static function handlePaymentMethod( $method, $params = array() ) {
       
        if ( !isset( $params['processor_name'] ) )
            CRM_Core_Error::fatal( "Missing 'processor_name' param for payment callback" );
        
        // Query db for processor ..
        $mode = @$params['mode'];
        
        $dao = CRM_Core_DAO::executeQuery( "
             SELECT ppt.class_name, ppt.name as processor_name, pp.id AS processor_id
               FROM civicrm_payment_processor_type ppt
         INNER JOIN civicrm_payment_processor pp
                 ON pp.payment_processor_type = ppt.name
                AND pp.is_active
                AND pp.is_test = %1
              WHERE ppt.name = %2 
        ",
            array(
                1 => array( $mode == 'test' ? 1 : 0, 'Integer' ),
                2 => array( $params['processor_name'], 'String' )
            )
        );

        // Check whether we found anything at all ..
        if ( !$dao->N ) 
            CRM_Core_Error::fatal( "No active instances of the '{$params['processor_name']}' payment processor were found." );
        
        // In all likelihood, we'll just end up with the one instance returned here. But it's 
        // possible we may get more. Hence, iterate through all instances ..
        
        $extension_instance_found = false;
        
        while ( $dao->fetch() ) {

            // Check pp is extension
            $ext = new CRM_Core_Extensions( );
            if ( $ext->isExtensionKey( $dao->class_name ) ) {
                $extension_instance_found = true;
                $paymentClass = $ext->keyToClass( $dao->class_name, 'payment' );
                require_once $ext->classToPath( $paymentClass );
            } else {                
                // Legacy instance - but there may also be an extension instance, so 
                // continue on to the next instance and check that one. We'll raise an
                // error later on if none are found.
                continue;
            }
            
            $paymentProcessor = CRM_Core_BAO_PaymentProcessor::getPayment( $dao->processor_id, $mode );
            
            // Should never be empty - we already established this processor_id exists and is active. 
            if ( empty( $paymentProcessor ) ) 
                continue;
            
            // Instantiate PP
            eval( '$processorInstance = ' . $paymentClass . '::singleton( $mode, $paymentProcessor );' );
            
            // Does PP implement this method, and can we call it?
            $method = 'handle' . $method;
            if ( ! method_exists( $processorInstance, $method ) || 
                 ! is_callable( array( $processorInstance, $method) ) ) {
                // No? This will be the case in all instances, so let's just die now 
                // and not prolong the agony.
                CRM_Core_Error::fatal( "Payment processor does not implement a '$method' method" );
            }
            
            // Everything, it seems, is ok - execute pp callback handler
            $processorInstance->$method( );
            
        }
        
        if ( !$extension_instance_found )
            CRM_Core_Error::fatal(
                "No extension instances of the '{$params['processor_name']}' payment processor were found.<br />" .
                "$method method is unsupported in legacy payment processors." 
            );
        
        // Exit here on web requests, allowing just the plain text response to be echoed
        if ($method == 'handlePaymentNotification')
            CRM_Utils_System::civiExit( );
        
 
    }

    /**
     * Function to check whether a method is present ( & supported ) by the payment processor object.
     *
     * @param  string $method method to check for.
     * @return boolean                                                                                                                                                
     * @public                                                                                                                                                        
     */
    function isSupported( $method = 'cancelSubscription' )
    {
        return method_exists( CRM_Utils_System::getClassName( $this ), $method );
    }

    function subscriptionURL( $entityID = null, $entity = null, $action = 'cancel' ) {
        if ( $action == 'cancel' ) {
            $url = 'civicrm/contribute/unsubscribe';
        } else if ( $action == 'billing' ) {
            $url = 'civicrm/contribute/updatebilling';
        } else if ( $action == 'update' ) {
            $url = 'civicrm/contribute/update';
        }
        $session = CRM_Core_Session::singleton( );
        $userId  = $session->get( 'userID' );
        $checksumValue = "";

        if ( $entityID && $entity == 'membership' ) {
            if ( !$userId ) {
                $contactID     = CRM_Core_DAO::getFieldValue( "CRM_Member_DAO_Membership", $entityID, "contact_id" );
                $checksumValue = CRM_Contact_BAO_Contact_Utils::generateChecksum( $contactID, null, 'inf' );
                $checksumValue = "&cs={$checksumValue}";
            }
            return CRM_Utils_System::url( $url, "reset=1&mid={$entityID}{$checksumValue}", true, null, false, false );
        }

        if ( $entityID && $entity == 'contribution' ) {
            if ( !$userId ) {
                $contactID = CRM_Core_DAO::getFieldValue( "CRM_Contribute_DAO_Contribution", $entityID, "contact_id" );
                $checksumValue = CRM_Contact_BAO_Contact_Utils::generateChecksum( $contactID, null, 'inf' );
                $checksumValue = "&cs={$checksumValue}";
            }
            return CRM_Utils_System::url( $url, "reset=1&coid={$entityID}{$checksumValue}", true, null, false, false );
        }

        if ( $entityID && $entity == 'recur' ) {
            if ( !$userId ) {
                $sql = " 
    SELECT con.contact_id
      FROM civicrm_contribution_recur rec
INNER JOIN civicrm_contribution con ON ( con.contribution_recur_id = rec.id )
     WHERE rec.id = %1
  GROUP BY rec.id";
                $contactID = CRM_Core_DAO::singleValueQuery( $sql, array(1 => array($entityID, 'Integer')) );
                $checksumValue = CRM_Contact_BAO_Contact_Utils::generateChecksum( $contactID, null, 'inf' );
                $checksumValue = "&cs={$checksumValue}";
            }
            return CRM_Utils_System::url( $url, "reset=1&crid={$entityID}{$checksumValue}", true, null, false, false );
        }

        if ( $this->isSupported('accountLoginURL') ) {
            return $this->accountLoginURL();
        }
        return $this->_paymentProcessor['url_recur'];
    }

}

