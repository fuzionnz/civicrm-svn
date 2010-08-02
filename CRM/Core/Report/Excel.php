<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
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


require_once 'CRM/Utils/String.php';

class CRM_Core_Report_Excel {
    /**
     * Code copied from phpMyAdmin (v2.6.1-pl3)
     * File: PHPMYADMIN/libraries/export/csv.php
     * Function: PMA_exportData
     *
     * Outputs a result set with a given header
     * in the string buffer result
     *
     * @param   string   $header (reference ) column headers
     * @param   string   $rows   (reference ) result set rows
     * @param   boolean  $print should the output be printed
     *
     * @return  mixed    empty if output is printed, else output
     *
     * @access  public
     */
    function makeCSVTable( &$header, &$rows, $titleHeader = null, $print = true, $outputHeader = true )
    {
        if ( $titleHeader ) {
            echo $titleHeader;
        }
        
        $result = '';

        $config = CRM_Core_Config::singleton( );
        $seperator     = $config->fieldSeparator;
        $enclosed      = '"';
        $escaped       = $enclosed;
        $add_character = "\015\012";

        $schema_insert = '';
        foreach ( $header as $field ) {
            if ($enclosed == '') {
                $schema_insert .= stripslashes($field);
            } else {
                $schema_insert .=
                      $enclosed
                    . str_replace($enclosed, $escaped . $enclosed, stripslashes($field))
                    . $enclosed;
            }
            $schema_insert     .= $seperator;
        } // end while

        if ( $outputHeader ) {
            // need to add PMA_exportOutputHandler functionality out here, rather than
            // doing it the moronic way of assembling a buffer
            $out = trim(substr($schema_insert, 0, -1)) . $add_character;
            if ( $print ) {
                echo $out;
            } else {
                $result .= $out;
            }
        }

        $i = 0;
        $fields_cnt = count($header);

        foreach ( $rows as $row ) {
            $schema_insert = '';
            $colNo = 0;
            
            foreach ( $row as $j => $value ) {
                if (!isset($value) || is_null($value)) {
                    $schema_insert .= '';
                } else if ($value == '0' || $value != '') {
                    // loic1 : always enclose fields
                    //$value = ereg_replace("\015(\012)?", "\012", $value);
                    $value = preg_replace("/\015(\012)?/", "\012", $value);
                    if ($enclosed == '') {
                        $schema_insert .= $value;
                    } else {
                        if ( ( substr( $value, 0, 1 ) == CRM_Core_BAO_CustomOption::VALUE_SEPERATOR )&& 
                             ( substr( $value, -1, 1 ) == CRM_Core_BAO_CustomOption::VALUE_SEPERATOR ) ) {
                            
                            $strArray = explode( CRM_Core_BAO_CustomOption::VALUE_SEPERATOR, $value );
                            
                            foreach( $strArray as $key => $val ) {
                                if ( trim( $val ) == '' ) {
                                    unset( $strArray[$key] );
                                }
                            }
                            
                            $str = implode( $seperator, $strArray );
                            $value = &$str;
                        }
                        
                        $schema_insert .=
                              $enclosed
                            . str_replace($enclosed, $escaped . $enclosed, $value)
                            . $enclosed;
                    }
                } else {
                    $schema_insert .= '';
                }
         
                if ($colNo < $fields_cnt-1) {
                    $schema_insert .= $seperator;
                }
                $colNo++;
            } // end for

            $out = $schema_insert . $add_character;
            if ( $print ) {
                echo $out;
            } else {
                $result .= $out;
            }

            ++$i;

        } // end for
        if ( $print ) {
            return;
        } else {
            return $result;
        }
    } // end of the 'getTableCsv()' function

    function writeCSVFile( $fileName, &$header, &$rows, $titleHeader = null, $outputHeader = true ) {
        if ( $outputHeader ) {
            require_once 'CRM/Utils/System.php';
            CRM_Utils_System::download( CRM_Utils_String::munge( $fileName ),
                                        'text/x-csv',
                                        CRM_Core_DAO::$_nullObject,
                                        'csv',
                                        false );
        }

        if ( ! empty( $rows ) ) {
            self::makeCSVTable( $header, $rows, $titleHeader, true, $outputHeader );
        }
    }
}