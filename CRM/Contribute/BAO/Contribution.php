<?php
  /*
   +--------------------------------------------------------------------+
   | CiviCRM version 1.3                                                |
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
   | at http://www.openngo.org/faqs/licensing.html                      |
   +--------------------------------------------------------------------+
  */

  /**
   *
   *
   * @package CRM
   * @author Donald A. Lobo <lobo@yahoo.com>
   * @copyright Donald A. Lobo (c) 2005
   * $Id$
   *
   */

require_once 'CRM/Contribute/DAO/Contribution.php';

require_once 'CRM/Core/BAO/CustomField.php';
require_once 'CRM/Core/BAO/CustomValue.php';

class CRM_Contribute_BAO_Contribution extends CRM_Contribute_DAO_Contribution
{
    /**
     * static field for all the contribution information that we can potentially import
     *
     * @var array
     * @static
     */
    static $_importableFields = null;

    function __construct()
    {
        parent::__construct();
    }
    

    /**
     * takes an associative array and creates a contribution object
     *
     * the function extract all the params it needs to initialize the create a
     * contribution object. the params array could contain additional unused name/value
     * pairs
     *
     * @param array  $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object
     * @access public
     * @static
     */
    static function add(&$params, &$ids) {
        $contribution =& new CRM_Contribute_BAO_Contribution();
        
        $contribution->copyValues($params);
        $contribution->domain_id = CRM_Utils_Array::value( 'domain' , $ids, CRM_Core_Config::domainID( ) );
        
        $contribution->id        = CRM_Utils_Array::value( 'contribution', $ids );

        require_once 'CRM/Utils/Rule.php';
        if (!CRM_Utils_Rule::currencyCode($contribution->currency)) {
            require_once 'CRM/Core/Config.php';
            $config =& CRM_Core_Config::singleton();
            $contribution->currency = $config->defaultCurrency;
        }
        
        return $contribution->save();
    }

    /**
     * Given the list of params in the params array, fetch the object
     * and store the values in the values array
     *
     * @param array $params input parameters to find object
     * @param array $values output values of the object
     * @param array $ids    the array that holds all the db ids
     *
     * @return CRM_Contribute_BAO_Contribution|null the found object or null
     * @access public
     * @static
     */
    static function &getValues( &$params, &$values, &$ids ) {

        $contribution =& new CRM_Contribute_BAO_Contribution( );

        $contribution->copyValues( $params );

        if ( $contribution->find(true) ) {
            $ids['contribution'] = $contribution->id;
            $ids['domain' ] = $contribution->domain_id;

            CRM_Core_DAO::storeValues( $contribution, $values );

            return $contribution;
        }
        return null;
    }

    /**
     * takes an associative array and creates a contribution object
     *
     * This function is invoked from within the web form layer and also from the api layer
     *
     * @param array $params (reference ) an assoc array of name/value pairs
     * @param array $ids    the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object 
     * @access public
     * @static
     */
    static function &create(&$params, &$ids) {
        require_once 'CRM/Utils/Hook.php';
        require_once 'CRM/Utils/Money.php';

        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::pre( 'edit', 'Contribution', $ids['contribution'], $params );
        } else {
            CRM_Utils_Hook::pre( 'create', 'Contribution', null, $params ); 
        }

        CRM_Core_DAO::transaction('BEGIN');

        $contribution = self::add($params, $ids);

        $params['contribution_id'] = $contribution->id;

        // add custom field values
        if (CRM_Utils_Array::value('custom', $params)) {
            foreach ($params['custom'] as $customValue) {
                $cvParams = array(
                                  'entity_table'    => 'civicrm_contribution',
                                  'entity_id'       => $contribution->id,
                                  'value'           => $customValue['value'],
                                  'type'            => $customValue['type'],
                                  'custom_field_id' => $customValue['custom_field_id'],
                                  );
                
                if ($customValue['id']) {
                    $cvParams['id'] = $customValue['id'];
                }
                CRM_Core_BAO_CustomValue::create($cvParams);
            }
        }

        CRM_Core_DAO::transaction('COMMIT');

        // let's create an (or update the relevant) Acitivity History record
        $contributionType = CRM_Contribute_PseudoConstant::contributionType($contribution->contribution_type_id);
        if (!$contributionType) $contributionType = ts('Contribution');

        static $insertDate = null;
        if (!$insertDate) $insertDate = CRM_Utils_Date::customFormat(date('Y-m-d H:i'));
        $activitySummary = ts(
            '%1 - %2 (from import on %3)',
            array(
                1 => CRM_Utils_Money::format($contribution->total_amount, $contribution->currency),
                2 => $contributionType,
                3 => $insertDate
            )
        );

        $historyParams = array(
            'entity_table'     => 'civicrm_contact',
            'entity_id'        => $contribution->contact_id,
            'activity_type'    => $contributionType,
            'module'           => 'CiviContribute',
            'callback'         => 'CRM_Contribute_Page_Contribution::details',
            'activity_id'      => $contribution->id,
            'activity_summary' => $activitySummary,
            'activity_date'    => $contribution->receive_date
        );

        if (CRM_Utils_Array::value('contribution', $ids)) {
            // this contribution should have an Activity History record already
            $getHistoryParams = array('module' => 'CiviContribute', 'activity_id' => $contribution->id);
            $getHistoryValues =& CRM_Core_BAO_History::getHistory($getHistoryParams, 0, 1, null, 'Activity');
            $ids['activity_history'] = CRM_Utils_Array::value('id', $getHistoryValues);
        }

        $historyDAO =& CRM_Core_BAO_History::create($historyParams, $ids, 'Activity');
        if (is_a($historyDAO, 'CRM_Core_Error')) {
            CRM_Core_Error::fatal("Failed creating Activity History for contribution of id {$contribution->id}");
        }


        if ( CRM_Utils_Array::value( 'contribution', $ids ) ) {
            CRM_Utils_Hook::post( 'edit', 'Contribution', $contribution->id, $contribution );
        } else {
            CRM_Utils_Hook::post( 'create', 'Contribution', $contribution->id, $contribution );
        }

        return $contribution;
    }

    /**
     * Get the values for pseudoconstants for name->value and reverse.
     *
     * @param array   $defaults (reference) the default values, some of which need to be resolved.
     * @param boolean $reverse  true if we want to resolve the values in the reverse direction (value -> name)
     *
     * @return void
     * @access public
     * @static
     */
    static function resolveDefaults(&$defaults, $reverse = false)
    {
        require_once 'CRM/Contribute/PseudoConstant.php';

        self::lookupValue($defaults, 'contribution_type', CRM_Contribute_PseudoConstant::contributionType(), $reverse);
        self::lookupValue($defaults, 'payment_instrument', CRM_Contribute_PseudoConstant::paymentInstrument(), $reverse);
    }

    /**
     * This function is used to convert associative array names to values
     * and vice-versa.
     *
     * This function is used by both the web form layer and the api. Note that
     * the api needs the name => value conversion, also the view layer typically
     * requires value => name conversion
     */
    static function lookupValue(&$defaults, $property, &$lookup, $reverse)
    {
        $id = $property . '_id';

        $src = $reverse ? $property : $id;
        $dst = $reverse ? $id       : $property;

        if (!array_key_exists($src, $defaults)) {
            return false;
        }

        $look = $reverse ? array_flip($lookup) : $lookup;
        
        if(is_array($look)) {
            if (!array_key_exists($defaults[$src], $look)) {
                return false;
            }
        }
        $defaults[$dst] = $look[$defaults[$src]];
        return true;
    }

    /**
     * Takes a bunch of params that are needed to match certain criteria and
     * retrieves the relevant objects. We'll tweak this function to be more
     * full featured over a period of time. This is the inverse function of
     * create.  It also stores all the retrieved values in the default array
     *
     * @param array $params   (reference ) an assoc array of name/value pairs
     * @param array $defaults (reference ) an assoc array to hold the name / value pairs
     *                        in a hierarchical manner
     * @param array $ids      (reference) the array that holds all the db ids
     *
     * @return object CRM_Contribute_BAO_Contribution object
     * @access public
     * @static
     */
    static function retrieve( &$params, &$defaults, &$ids ) {
        $contribution = CRM_Contribute_BAO_Contribution::getValues( $params, $defaults, $ids );
        return $contribution;
    }

    /**
     * combine all the importable fields from the lower levels object
     *
     * The ordering is important, since currently we do not have a weight
     * scheme. Adding weight is super important and should be done in the
     * next week or so, before this can be called complete.
     *
     * @return array array of importable Fields
     * @access public
     */
    function &importableFields( ) {
        if ( ! self::$_importableFields ) {
            if ( ! self::$_importableFields ) {
                self::$_importableFields = array();
            }
            if (!$status) {
                $fields = array( '' => array( 'title' => ts('- do not import -') ) );
            } else {
                $fields = array( '' => array( 'title' => ts('- Contribution Fields -') ) );
            }

            $tmpFields = CRM_Contribute_DAO_Contribution::import( );
            $fields = array_merge($fields, $tmpFields);

            $fields = array_merge($fields, CRM_Core_BAO_CustomField::getFieldsForImport('Contribution'));

            self::$_importableFields = $fields;
        }
        return self::$_importableFields;
    }

    function &exportableFields( ) {
        return self::importableFields( );
    }

    function getTotalAmountAndCount( $status = null, $startDate = null, $endDate = null ) {
        
        $where = array( );
        switch ( $status ) {
        case 'Valid':
            $where[] = 'cancel_date is null';
            break;

        case 'Cancelled':
            $where[] = 'cancel_date is not null';
            break;
        }

        if ( $startDate ) {
            $where[] = "receive_date >= '" . CRM_Utils_Type::escape( $startDate, 'Timestamp' ) . "'";
        }
        if ( $endDate ) {
            $where[] = "receive_date <= '" . CRM_Utils_Type::escape( $endDate, 'Timestamp' ) . "'";
        }

        $whereCond = implode( ' AND ', $where );
        $domainID  = CRM_Core_Config::domainID( );

        $query = "
SELECT sum( total_amount ) as total_amount, count( id ) as total_count
FROM   civicrm_contribution
WHERE  domain_id = $domainID AND $whereCond
";

        $dao = CRM_Core_DAO::executeQuery( $query );
        if ( $dao->fetch( ) ) {
            return array( 'amount' => $dao->total_amount,
                          'count'  => $dao->total_count );
        }
        return null;
    }

    /**                                                           
     * Delete the object records that are associated with this contact 
     *                    
     * @param  int  $contactId id of the contact to delete                                                                           
     * 
     * @return void 
     * @access public 
     * @static 
     */ 
    static function deleteContact( $contactId ) {
        $contribution =& new CRM_Contribute_DAO_Contribution( );
        $contribution->contact_id = $contactId;
        $contribution->find( );

        require_once 'CRM/Contribute/DAO/FinancialTrxn.php';
        while ( $contribution->fetch( ) ) {
            $trxn =& new CRM_Contribute_DAO_FinancialTrxn( ); 
            $trxn->entity_table = 'civicrm_contribution'; 
            $trxn->entity_id    = $contribution->id;
            $trxn->delete( );
            $contribution->delete( );
        }
    }

    static function deleteContribution( $id ) {
        $contribution =& new CRM_Contribute_DAO_Contribution( ); 
        $contribution->id = $id;
        if ( $contribution->find( true ) ) {
            require_once 'CRM/Contribute/DAO/FinancialTrxn.php'; 
            $trxn =& new CRM_Contribute_DAO_FinancialTrxn( );  
            $trxn->entity_table = 'civicrm_contribution';  
            $trxn->entity_id    = $contribution->id; 
            if ( $trxn->find( true ) ) {
                $trxn->delete( ); 
            }

            $contribution->delete( ); 
        }
    }

}

?>
