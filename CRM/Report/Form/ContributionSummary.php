<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
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

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_ContributionSummary extends CRM_Report_Form {

    protected $_summary = null;

    function __construct( ) {
        $this->_columns = array( 
                                 'civicrm_contribution' =>
                                 array( 'dao'     => 'CRM_Contribute_DAO_Contribution',
                                        'bao'     => 'CRM_Contribute_BAO_Contribution',
                                        'fields'  =>
                                        array( 'total_amount'  => array( 'title'    => ts( 'Amount' ),
                                                                         'required' => true ),
                                               ),
                                        'filters'  =>             
                                        array( 'receive_date' => 
                                               array( 'default'    => 'this month' ),
                                               'total_amount' => 
                                               array( 'title'      => ts( 'Total  Amount Between' ) ),
                                               ),
                                        'group_bys'=>             
                                        array( 'receive_date' => 
                                               array( 'default'    => 'this month' ),
                                               'contribution_source'  => null,
                                               'contribution_type'    => null,
                                               'contribution_page_id' => null,
                                               ),
                                        'group_bys_freq'=>             
                                        array( 'YEARWEEK' => 'Week',
                                               'MONTH'    => 'Month',
                                               'QUARTER'  => 'Quarter',
                                               'YEAR'     => 'Year',
                                               ),
                                        ),
                                 );

        $this->_options = array( 'include_statistics' => array( 'title' => ts( 'Include Grand Totals' ),
                                                                'type'  => 'checkbox' ),
                                 );
        
        parent::__construct( );
    }

    function preProcess( ) {
        parent::preProcess( );
    }

    function setDefaultValues( ) {
        return parent::setDefaultValues( );
    }

    function select( ) {
        $select = array( );
        $interval = 'month';
        $this->_columnHeaders = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            $once = false;
            foreach ( $table['fields'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( 'required', $field ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$table['grouping']] ) ||
                     CRM_Utils_Array::value( $fieldName, $this->_params['select_columns'][$tableName] ) ) {

                    $select[] = "{$table['alias']}.{$fieldName} as {$tableName}_{$fieldName}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}"] = $field['title'];
                    
                    
                    if ( !$once ) {
                        $select[] = "SUM({$table['alias']}.total_amount) as amount";
                        $select[] = "count({$table['alias']}.total_amount) as count";
                        $select[] = "AVG({$table['alias']}.total_amount) as avg";
                        $once     = true;
                    }
                }
            }

            foreach ( $table['group_bys'] as $fieldName => $field ) {
                if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                    
                    $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                    $this->_columnHeaders["{$tableName}_{$fieldName}"] = $field['title'];
                    
                    switch ( $this->_params['group_bys_freq'] ) {
                    case 'YEARWEEK' :
                        $select[] = "DATE_SUB({$table['alias']}.receive_date, 
INTERVAL WEEKDAY({$table['alias']}.receive_date )DAY) AS {$tableName}_{$fieldName}_start";
                        $select[] = "DATE_ADD({$table['alias']}.receive_date, 
INTERVAL(6 - WEEKDAY({$table['alias']}.receive_date ))DAY) AS {$tableName}_{$fieldName}_end"; 
                        break;

                    case 'YEAR' :
                        $select[] = "MAKEDATE(YEAR({$table['alias']}.receive_date ),1)  
AS {$tableName}_{$fieldName}_start";
                        $select[] = "LAST_DAY(MAKEDATE(YEAR({$table['alias']}.receive_date ),365)) 
AS {$tableName}_{$fieldName}_end"; 
                        break;

                    case 'MONTH':
                        $select[] = "DATE_SUB({$table['alias']}.receive_date, 
INTERVAL (DAYOFMONTH({$table['alias']}.receive_date)-1) DAY) as {$tableName}_{$fieldName}_start";
                        $select[] = "{$table['alias']}.receive_date, 
LAST_DAY({$table['alias']}.receive_date) as {$tableName}_{$fieldName}_end"; 
                        break;

                    case 'QUARTER':
                        $select[] = "STR_TO_DATE(CONCAT( 3 * QUARTER( {$table['alias']}.receive_date ) -2 , '/', '1', '/', YEAR( {$table['alias']}.receive_date ) ), '%m/%d/%Y') AS {$tableName}_{$fieldName}_start";
                        $select[] = "LAST_DAY(STR_TO_DATE(CONCAT( 3 * QUARTER( {$table['alias']}.receive_date ) , '/', '1', '/', YEAR( {$table['alias']}.receive_date ) ), '%m/%d/%Y')) AS {$tableName}_{$fieldName}_end"; 
                        break;

                    }
                    if ( CRM_Utils_Array::value( 'group_bys_freq', $this->_params ) ) {
                        $this->_columnHeaders["{$tableName}_{$fieldName}_start"] = $field['title'] . ' start';
                        $this->_columnHeaders["{$tableName}_{$fieldName}_end"]   = $field['title'] . ' end';
                    }
                }
            }
        }

        $this->_select = "SELECT " . implode( ', ', $select ) . " ";
    }

    function from( ) {
        $this->_from = "
FROM       civicrm_contribution {$this->_aliases['civicrm_contribution']}
";
    }

    function where( ) {
        $clauses = array( );
        foreach ( $this->_columns as $tableName => $table ) {
            if ( array_key_exists('filters', $table) ) {
                foreach ( $table['filters'] as $fieldName => $field ) {
                    $clause = null;
                    if ( $field['type'] & CRM_Utils_Type::T_DATE ) {
                        $relative = CRM_Utils_Array::value( "{$fieldName}_relative", $this->_params );
                        $from     = CRM_Utils_Array::value( "{$fieldName}_from"    , $this->_params );
                        $to       = CRM_Utils_Array::value( "{$fieldName}_to"      , $this->_params );
                        
                        if ( $relative || $from || $to ) {
                            $clause = $this->dateClause( $field, $relative, $from, $to );
                        }
                    } else {
                        $op = CRM_Utils_Array::value( "{$fieldName}_op", $this->_params );
                        if ( $op ) {
                            $clause = 
                                $this->whereClause( $field,
                                                    $op,
                                                    CRM_Utils_Array::value( "{$fieldName}_value", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_min", $this->_params ),
                                                    CRM_Utils_Array::value( "{$fieldName}_max", $this->_params ) );
                        }
                    }
                    
                    if ( ! empty( $clause ) ) {
                        $clauses[] = $clause;
                    }
                }
            }
        }

        if ( empty( $clauses ) ) {
            $this->_where = "WHERE ( 1 ) ";
        } else {
            $this->_where = "WHERE " . implode( ' AND ', $clauses );
        }
    }

    function groupBy( ) {
        $this->_groupBy = "";
        if ( ! empty($this->_params['group_bys']) ) {
            foreach ( $this->_columns as $tableName => $table ) {
                foreach ( $table['group_bys'] as $fieldName => $field ) {
                    if ( CRM_Utils_Array::value( $fieldName, $this->_params['group_bys'] ) ) {
                        $this->_groupBy[] = $field['dbAlias'];
                    }
                }
            }
            $this->_groupBy = "GROUP BY " . implode( ', ', $this->_groupBy ) . " ";
        }
    }

    function statistics( ) {
        $select = "
SELECT COUNT( contribution.total_amount ) as count,
       SUM(   contribution.total_amount ) as amount,
       AVG(   contribution.total_amount ) as avg
";

        $sql = "{$select} {$this->_from} {$this->_where}";
        $dao = CRM_Core_DAO::executeQuery( $sql );

        $statistics = null;
        if ( $dao->fetch( ) ) {
            $statistics = array( 'count'  => $dao->count,
                                 'amount' => $dao->amount,
                                 'avg'    => $dao->avg );
        }
        
        return $statistics;
    }

    function postProcess( ) {
        $this->_params = $this->controller->exportValues( $this->_name );

        $this->select  ( );
        $this->from    ( );
        $this->where   ( );
        $this->groupBy ( );

        $sql = "{$this->_select} {$this->_from} {$this->_where} {$this->_groupBy}";
/*         CRM_Core_Error::debug( '$sql', $sql ); */

        $dao  = CRM_Core_DAO::executeQuery( $sql );
        $rows = array( );
        while ( $dao->fetch( ) ) {
            $row = array( );
            foreach ( $this->_columnHeaders as $key => $value ) {
                $row[$key] = $dao->$key;
            }
            $rows[] = $row;
        }

        $this->assign_by_ref( 'columnHeaders', $this->_columnHeaders );
        $this->assign_by_ref( 'rows', $rows );
        
/*         CRM_Core_Error::debug( '$this->_columnHeaders', $this->_columnHeaders  ); */
/*         CRM_Core_Error::debug( '$rows', $rows ); */
/*         CRM_Core_Error::debug( 'statistics', $this->statistics( ) ); */

        if ( CRM_Utils_Array::value( 'include_statistics', $this->_params ) ) {
            $this->assign( 'statistics',
                           $this->statistics( ) );
        }

/*         CRM_Core_Error::debug( '$sql', $sql ); */
/*         exit( ); */
    }

}
