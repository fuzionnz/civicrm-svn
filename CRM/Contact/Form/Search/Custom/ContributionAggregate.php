<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Search/Interface.php';

class CRM_Contact_Form_Search_Custom_ContributionAggregate
   implements CRM_Contact_Form_Search_Interface {

    protected $_formValues;

    function __construct( &$formValues ) {     
        $this->_formValues = $formValues;

        /**
         * Define the columns for search result rows
         */
        $this->_columns = array( ts('Contact Id')   => 'contact_id'  ,
                                 ts('Name'      )   => 'sort_name',
                                 ts('Donation Count') => 'donation_count',
                                 ts('Donation Amount') => 'donation_amount' );
    }

    function buildForm( &$form ) {
        /**
         * You can define a custom title for the search form
         */
        $this->setTitle('Find Contributors by Aggregate Totals');

        /**
         * Define the search form fields here
         */
        $form->add( 'text',
                    'min_amount',
                    ts( 'Aggregate Total Between $' ) );
        $form->add( 'text',
                    'max_amount',
                    ts( '...and $' ) );

        $form->add( 'date',
                    'start_date',
                    ts('Contribution Date From'),
                    CRM_Core_SelectValues::date('custom', 10, 3 ) );
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');

        $form->add( 'date',
                    'end_date',
                    ts('...through'),
                    CRM_Core_SelectValues::date('custom', 10, 0 ) );
        $form->addRule('end_date', ts('Select a valid date.'), 'qfDate');

        /**
         * If you are using the sample template, this array tells the template fields to render
         * for the search form.
         */
        $form->assign( 'elements', array( 'min_amount', 'max_amount', 'start_date', 'end_date') );
    }

    /**
     * Define the smarty template used to layout the search form and results listings.
     */
    function templateFile( ) {
       return 'CRM/Contact/Form/Search/Custom/Sample.tpl';
    }
       
    /**
      * Construct the search query
      */       
    function all( $offset = 0, $rowcount = 0, $sort = null,
                  $includeContactIDs = false ) {
        
        // SELECT clause must include contact_id as an alias for civicrm_contact.id
        $select  = "
distinct(contact.id) as contact_id,
contact.sort_name as sort_name,
sum(contrib.total_amount) AS donation_amount,
count(contrib.id) AS donation_count
";

        $from  = $this->from( );

        $where = $this->where( $includeContactIDs );

        $having = $this->having( );
        if ( $having ) {
            $having = " HAVING $having ";
        }

        $sql = "
SELECT $select
FROM   $from
WHERE  $where
GROUP BY contact.id
$having
";
        // Define ORDER BY for query in $sort, with default value
        if ( ! empty( $sort ) ) {
            if ( is_string( $sort ) ) {
                $sql .= " ORDER BY $sort ";
            } else {
                $sql .= " ORDER BY " . trim( $sort->orderBy() );
            }
        } else {
            $sql .= "ORDER BY donation_amount desc";
        }

        return $sql;
    }

    function from( ) {
        return "
civicrm_contribution AS contrib,
civicrm_contact AS contact
";

    }

     /*
      * WHERE clause is an array built from any required JOINS plus conditional filters based on search criteria field values
      *
      */
    function where( $includeContactIDs = false ) {
        $clauses = array( );

        $clauses[] = "contrib.contact_id = contact.id";
        $clauses[] = "contrib.is_test = 0";

        $startDate = CRM_Utils_Date::format( $this->_formValues['start_date'] );
        if ( $startDate ) {
            $clauses[] = "contrib.receive_date >= $startDate";
        }

        $endDate = CRM_Utils_Date::format( $this->_formValues['end_date'] );
        if ( $endDate ) {
            $clauses[] = "contrib.receive_date <= $endDate";
        }

        if ( $includeContactIDs ) {
            $contactIDs = array( );
            foreach ( $this->_formValues as $id => $value ) {
                if ( $value &&
                     substr( $id, 0, CRM_Core_Form::CB_PREFIX_LEN ) == CRM_Core_Form::CB_PREFIX ) {
                    $contactIDs[] = substr( $id, CRM_Core_Form::CB_PREFIX_LEN );
                }
            }
        
            if ( ! empty( $contactIDs ) ) {
                $contactIDs = implode( ', ', $contactIDs );
                $clauses[] = "contact.id IN ( $contactIDs )";
            }
        }

        return implode( ' AND ', $clauses );
    }

    function having( $includeContactIDs = false ) {
        $clauses = array( );
        $min = CRM_Utils_Array::value( 'min_amount', $this->_formValues );
        if ( $min ) {
            $clauses[] = "sum(contrib.total_amount) >= $min";
        }

        $max = CRM_Utils_Array::value( 'max_amount', $this->_formValues );
        if ( $max ) {
            $clauses[] = "sum(contrib.total_amount) <= $max";
        }

        return implode( ' AND ', $clauses );
    }

    /* 
     * Functions below generally don't need to be modified
     */
    function count( ) {
           $sql = $this->all( );
           
           $dao = CRM_Core_DAO::executeQuery( $sql,
                                             CRM_Core_DAO::$_nullArray );
           return $dao->N;
    }
       
    function contactIDs( $offset = 0, $rowcount = 0, $sort = null) { 
        return $this->all( $offset, $rowcount, $sort );
    }
       
    function &columns( ) {
        return $this->_columns;
    }

   function setTitle( $title ) {
       if ( $title ) {
           CRM_Utils_System::setTitle( $title );
       } else {
           CRM_Utils_System::setTitle(ts('Search'));
       }
   }
       
}


