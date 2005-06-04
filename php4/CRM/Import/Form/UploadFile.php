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
require_once 'CRM/Import/Parser/Contact.php';
require_once 'CRM/Core/Form.php';

/**
 * This class gets the name of the file to upload
 */
class CRM_Import_Form_UploadFile extends CRM_Core_Form {
   
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */
     function buildQuickForm( ) {
        $this->addElement( 'file', 'uploadFile', ts('Import Data File'), 'size=30 maxlength=60' );

        $this->addRule( 'uploadFile', ts('File size should be less than 1 MByte'), 'maxfilesize', 1024 * 1024 );
        $this->setMaxFileSize( 1024 * 1024 );
        $this->addRule( 'uploadFile', ts('Input file must be either CSV or XML format'), 'asciiFile' );

        $this->addElement( 'checkbox', 'skipColumnHeader', ts('First row contains column headers') );

        $this->addButtons( array(
                                 array ( 'type'      => 'upload',
                                         'name'      => ts('Continue >>'),
                                         'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                         'isDefault' => true   ),
                                 array ( 'type'      => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * Process the uploaded file
     *
     * @return void
     * @access public
     */
     function postProcess( ) {
        $fileName         = $this->controller->exportValue( $this->_name, 'uploadFile' );
        $skipColumnHeader = $this->controller->exportValue( $this->_name, 'skipColumnHeader' );

        $seperator = ',';
        $mapper = array( );

        $parser = new CRM_Import_Parser_Contact( $mapper );
        $parser->setMaxLinesToProcess( 100 );
        $parser->run( $fileName, $seperator,
                      $mapper,
                      $skipColumnHeader,
                      CRM_IMPORT_PARSER_MODE_PREVIEW );

        // add all the necessary variables to the form
        $parser->set( $this );
    }

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
     function getTitle( ) {
        return ts('Upload Data');
    }

}

?>
