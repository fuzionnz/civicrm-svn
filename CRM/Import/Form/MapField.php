<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_MapField extends CRM_Core_Form {

    /**
     * cache of preview data values
     *
     * @var array
     * @access protected
     */
    protected $_dataValues;

    /**
     * mapper fields
     *
     * @var array
     * @access protected
     */
    protected $_mapperFields;

    /**
     * number of columns in import file
     *
     * @var int
     * @access protected
     */
    protected $_columnCount;


    /**
     * column headers, if we have them
     *
     * @var array
     * @access protected
     */
    protected $_columnHeaders;

    /**
     * Attempt to resolve the header with our mapper fields
     *
     * @param header
     * @param mapperFields
     * @return string
     * @access public
     */
    public static function defaultFromHeader($header, &$patterns) {
        foreach ($patterns as $key => $re) {
            /* Skip the first (empty) key/pattern */
            if (empty($re)) continue;

            /* Scan through the headerPatterns defined in the schema for a
             * match */
            if (preg_match($re, $header)) {
                return $key;
            }
        }
        return 0;
    }

    /**
     * Guess at the field names given the data and patterns from the schema
     *
     * @param patterns
     * @param index
     * @return string
     * @access public
     */
    public function defaultFromData(&$patterns, $index) {


    }

    /**
     * Function to set variables up before form is built
     *
     * @param none
     * @return void
     * @access public
     */
    public function preProcess()
    {
        $this->_mapperFields = $this->get( 'fields' );
        
        $this->_columnCount = $this->get( 'columnCount' );
        $this->assign( 'columnCount' , $this->_columnCount );
        $this->_dataValues = $this->get( 'dataValues' );
        $this->assign( 'dataValues'  , $this->_dataValues );
        
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        if ( $skipColumnHeader ) {
            $this->assign( 'skipColumnHeader' , $skipColumnHeader );
            $this->assign( 'rowDisplayCount', 3 );
            /* if we had a column header to skip, stash it for later */
            $this->_columnHeaders = $this->_dataValues[0];
        } else {
            $this->assign( 'rowDisplayCount', 2 );
        }
        
    }

    /**
     * Function to actually build the form
     *
     * @param none
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {
        $this->_defaults = array( );
        $mapperKeys      = array_keys( $this->_mapperFields );
        $hasHeaders      = !empty($this->_columnHeaders);
        $headerPatterns  = $this->get( 'headerPatterns' );
        $dataPatterns    = $this->get( 'dataPatterns' );
        
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $this->add( 'select', "mapper[$i]", ts('Mapper for Field %1', array(1 => $i)), $this->_mapperFields );
//             $this->_defaults["mapper[$i]"] = $mapperKeys[$i];
            if ($hasHeaders) {
                // Infer the default from the skipped headers if we have them
                $this->_defaults["mapper[$i]"] = 
                    self::defaultFromHeader($this->_columnHeaders[$i], 
                                            $headerPatterns);

            } else {
                // Otherwise guess the default from the form of the data
                $this->_defaults["mapper[$i]"] = 
                    $this->defaultFromData($dataPatterns, $i);
            }
        }
        $this->setDefaults( $this->_defaults );

        $this->addButtons( array(
                                 array ( 'type'      => 'back',
                                         'name'      => ts('<< Previous') ),
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the mapped fields and map it into the uploaded file
     * preview the file and extract some summary statistics
     *
     * @param none
     * @return void
     * @access public
     */
    public function postProcess()
    {
        $fileName         = $this->controller->exportValue( 'UploadFile', 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( 'UploadFile', 'skipColumnHeader' );

        $seperator = ',';

        $mapperKeys = array( );
        $mapper     = array( );
        $mapperKeys = $this->controller->exportValue( $this->_name, 'mapper' );
        for ( $i = 0; $i < $this->_columnCount; $i++ ) {
            $mapper[$i]     = $this->_mapperFields[$mapperKeys[$i]];
        }

        $this->set( 'mapper'    , $mapper     );

        $parser =& new CRM_Import_Parser_Contact( $mapperKeys );
        $parser->run( $fileName, $seperator,
                      $mapper, 
                      $skipColumnHeader,
                      CRM_Import_Parser::MODE_SUMMARY );

        // add all the necessary variables to the form
        $parser->set( $this );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @param none
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Match Fields');
    }

    
}

?>
