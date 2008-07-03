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
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

class CRM_Pledge_BAO_Query 
{
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/Pledge/DAO/Pledge.php';
        $fields = array_merge( $fields, CRM_Pledge_DAO_Pledge::import( ) );
        return $fields;
    }

    /** 
     * build select for Pledge
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {  
        if ( ( $query->_mode & CRM_Contact_BAO_Query::MODE_PLEDGE ) ||
             CRM_Utils_Array::value( 'pledge_id', $query->_returnProperties ) ) {
            $query->_select['pledge_id'] = "civicrm_pledge.id as pledge_id";
            $query->_element['pledge_id'] = 1;
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
        }
        
        //add pledge select
        if ( CRM_Utils_Array::value( 'pledge_amount', $query->_returnProperties ) ) {
            $query->_select['pledge_amount'] = "civicrm_pledge.amount as pledge_amount";
            $query->_element['pledge_amount'] = 1;
            $query->_tables['civicrm_pledge'] = 1;
            $query->_whereTables['civicrm_pledge'] = 1;
        }
        
        if ( CRM_Utils_Array::value( 'pledge_create_date', $query->_returnProperties ) ) {
            $query->_select['pledge_create_date']  = "civicrm_pledge.create_date as pledge_create_date";
            $query->_element['pledge_create_date'] = 1;
            $query->_tables['civicrm_pledge'] = 1;
            $query->_whereTables['civicrm_pledge'] = 1;
        }
        
        if ( CRM_Utils_Array::value( 'pledge_start_date', $query->_returnProperties ) ) {
            $query->_select['pledge_start_date']  = "civicrm_pledge.start_date as pledge_start_date";
            $query->_element['pledge_start_date'] = 1;
            $query->_tables['civicrm_pledge'] = 1;
            $query->_whereTables['civicrm_pledge'] = 1;
        }
         
        if ( CRM_Utils_Array::value( 'pledge_frequency_interval', $query->_returnProperties ) ) {
            $query->_select['pledge_frequency_interval']  = "civicrm_pledge.frequency_interval as pledge_frequency_interval";
            $query->_element['pledge_frequency_interval'] = 1;
            $query->_tables['civicrm_pledge']             = 1;
        }
        
        if ( CRM_Utils_Array::value( 'pledge_frequency_unit', $query->_returnProperties ) ) {
            $query->_select['pledge_frequency_unit']  = "civicrm_pledge.frequency_unit as pledge_frequency_unit";
            $query->_element['pledge_frequency_unit'] = 1;
            $query->_tables['civicrm_pledge']         = 1;
        }
        
        if ( CRM_Utils_Array::value( 'pledge_status_id', $query->_returnProperties ) ) {
            $query->_select['pledge_status_id']  = "pledge_status.name as pledge_status_id";
            $query->_element['pledge_status'] = 1;
            $query->_tables['pledge_status'] = 1;
            $query->_tables['civicrm_pledge'] = 1;
            $query->_whereTables['civicrm_pledge'] = 1;
            $query->_whereTables['pledge_status'] = 1;
        }
    }
    
    static function where( &$query ) 
    {
        $isTest   = false;
        $grouping = null;
        foreach ( array_keys( $query->_params ) as $id ) {
            if ( substr( $query->_params[$id][0], 0, 7) == 'pledge_' || substr( $query->_params[$id][0], 0, 13 ) == 'contribution_' ) {
                if ( $query->_mode == CRM_Contact_BAO_QUERY::MODE_CONTACTS ) {
                    $query->_useDistinct = true;
                }
                if ( $query->_params[$id][0] == 'pledge_test' ) {
                    $isTest = true;
                }
                $grouping = $query->_params[$id][3];
                self::whereClauseSingle( $query->_params[$id], $query );
            }
        }
        
        if ( $grouping !== null &&
             ! $isTest ) {
            $values = array( 'pledge_test', '=', 0, $grouping, 0 );
            self::whereClauseSingle( $values, $query );
        }  
    }
    
    static function whereClauseSingle( &$values, &$query ) 
    {
        list( $name, $op, $value, $grouping, $wildcard ) = $values;
             
        switch( $name ) {
        
        case 'pledge_start_date_low':
        case 'pledge_start_date_high':
            // process to / from date
            $query->dateQueryBuilder( $values,
                                      'civicrm_pledge', 'pledge_start_date', 'start_date', 'Pledge Start Date' );
            return;

        case 'pledge_end_date_low':
        case 'pledge_end_date_high':
            // process to / from date
            $query->dateQueryBuilder( $values,
                                      'civicrm_pledge', 'pledge_end_date', 'end_date', 'Pledge End Date' );
            return;
    
        case 'pledge_amount':
        case 'pledge_amount_low':
        case 'pledge_amount_high':
            // process min/max amount
            $query->numberRangeBuilder( $values,
                                        'civicrm_pledge', 'pledge_amount', 'amount', 'Pledge Amount' );
            return;
         
            
        case 'pledge_status_id':
           
            if ( is_array( $value ) ) {
                foreach ($value as $k => $v) {
                    if ( $v ) {
                        $val[$k] = $k;
                    }
                } 
               
                $status = implode (',' ,$val);
                                
                if ( count($val) > 1 ) {
                    $op = 'IN';
                    $status = "({$status})";
                }     
            } else {
                $status = $value;
            }

            require_once "CRM/Core/OptionGroup.php";
            $statusValues = CRM_Core_OptionGroup::values("contribution_status");
         
            $names = array( );
            if ( is_array( $val ) ) {
                foreach ( $val as $id => $dontCare ) {
                    $names[] = $statusValues[ $id ];
                }
            } else {
                $names[] = $statusValues[ $value ];
            }
           
            $query->_qill[$grouping][]  = ts('Pledge Status %1', array( 1 => $op ) ) . ' ' . implode( ' ' . ts('or') . ' ', $names );
            $query->_where[$grouping][] = "civicrm_pledge.status_id {$op} {$status}";
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
            return;

        case 'pledge_test':
            $query->_where[$grouping][] = " civicrm_pledge.is_test $op '$value'";
            if ( $value ) {
                $query->_qill[$grouping][]  = "Find Test Pledges";
            }
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
            
            return;
        
        case 'pledge_contribution_type_id':
        case 'contribution_type':
            require_once 'CRM/Contribute/PseudoConstant.php';
            $cType = $value;
            $types = CRM_Contribute_PseudoConstant::contributionType( );
            $query->_where[$grouping][] = "civicrm_pledge.contribution_type_id = $cType";
            $query->_qill[$grouping ][] = ts( 'Contribution Type - %1', array( 1 => $types[$cType] ) );
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
            return;
            
        case 'pledge_contribution_page_id':
            require_once 'CRM/Contribute/PseudoConstant.php';
            $cPage = $value;
            $pages = CRM_Contribute_PseudoConstant::contributionPage( );
            $query->_where[$grouping][] = "civicrm_pledge.contribution_page_id = $cPage";
            $query->_qill[$grouping ][] = ts( 'Contribution Page - %1', array( 1 => $pages[$cPage] ) );
            $query->_tables['civicrm_pledge'] = $query->_whereTables['civicrm_pledge'] = 1;
            return;
            
        }
    }

    static function from( $name, $mode, $side ) 
    {
        $from = null;
        
        switch ( $name ) {
       
        case 'civicrm_pledge':
            $from = " $side JOIN civicrm_pledge ON civicrm_pledge.contact_id = contact_a.id ";
            break;
            
        case 'pledge_status':
            $from .= " $side JOIN civicrm_option_group option_group_pledge_status ON (option_group_pledge_status.name = 'contribution_status')";
            $from .= " $side JOIN civicrm_option_value pledge_status ON (civicrm_pledge.status_id = pledge_status.value AND option_group_pledge_status.id = pledge_status.option_group_id ) ";
            break;
            
        }
        return $from;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    static function defaultReturnProperties( $mode ) 
    {
        $properties = null;
     
        if ( $mode & CRM_Contact_BAO_Query::MODE_PLEDGE ) {
            $properties = array(  
                                'contact_id'                  => 1,
                                'contact_type'                => 1, 
                                'sort_name'                   => 1, 
                                'display_name'                => 1,
                                'pledge_id'                   => 1,
                                'pledge_amount'               => 1,
                                'pledge_frequency_unit'       => 1,
                                'pledge_frequency_interval'   => 1,
                                'pledge_create_date'          => 1,
                                'pledge_start_date'           => 1,
                                'pledge_status_id'            => 1,
                                'pledge_is_test'              => 1 
                                );
        }
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {
        
        // Date selects for date 
        $form->add('date', 'pledge_start_date_low', ts('Pledge Start Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_start_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->add('date', 'pledge_start_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_start_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->add('date', 'pledge_end_date_low', ts('Pledge End Date - From'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_end_date_low', ts('Select a valid date.'), 'qfDate'); 
        
        $form->add('date', 'pledge_end_date_high', ts('To'), CRM_Core_SelectValues::date('relative')); 
        $form->addRule('pledge_end_date_high', ts('Select a valid date.'), 'qfDate'); 

        $form->addElement( 'checkbox', 'pledge_test' , ts( 'Find Test Pledge?' ) );

        $form->add('text', 'pledge_amount_low', ts('From'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'pledge_amount_low', ts( 'Please enter a valid money value (e.g. 9.99).' ), 'money' );
        
        $form->add('text', 'pledge_amount_high', ts('To'), array( 'size' => 8, 'maxlength' => 8 ) ); 
        $form->addRule( 'pledge_amount_high', ts( 'Please enter a valid money value (e.g. 99.99).' ), 'money' );

        require_once "CRM/Core/OptionGroup.php";
        $statusValues = CRM_Core_OptionGroup::values("contribution_status");
        // Remove status values that are only used for recurring contributions for now (Failed and In Progress).
        unset( $statusValues['4']);
        unset( $statusValues['5']);
 
        foreach ( $statusValues as $key => $val ) {
            $status[] =  $form->createElement('advcheckbox',$key, null, $val );
        }
        
        $form->addGroup( $status, 'pledge_status_id', ts( 'Pledge Status' ) );

        require_once 'CRM/Contribute/PseudoConstant.php';
        $form->add('select', 'pledge_contribution_type_id', 
                   ts( 'Contribution Type' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionType( ) );
       
        $form->add('select', 'pledge_contribution_page_id', 
                   ts( 'Contribution Page' ),
                   array( '' => ts( '- select -' ) ) +
                   CRM_Contribute_PseudoConstant::contributionPage( ) );
        
        //add fields for honor search
        $form->addElement( 'text', 'pledge_in_honor_of', ts( "In Honor Of" ) );
        
        $form->assign( 'validPledge', true );
    }
    
    static function searchAction( &$row, $id ) 
    {
    }

    static function tableNames( &$tables ) 
    {
        //add status table 
        if ( CRM_Utils_Array::value( 'contribution_status', $tables ) ) {
            $tables = array_merge( array( 'civicrm_contribution' => 1), $tables );
        }
    }
  
}


