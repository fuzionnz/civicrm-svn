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

require_once 'CRM/StateMachine.php';

class CRM_Import_StateMachine extends CRM_StateMachine {

    static $_pages = array(
                           'CRM_Import_Form_UploadFile',
                           'CRM_Import_Form_MapField',
                           'CRM_Import_Form_Preview',
                           'CRM_Import_Form_Summary'
                           );

    /**
     * class constructor
     */
    function __construct( $controller, $mode = CRM_Form::MODE_NONE ) {
        parent::__construct( $controller, $mode );

        $this->addSequentialPages( self::$_pages, $mode );
    }

    function wizardHeader( ) {
        $header = array( );
        foreach ( self::$_pages as &$page ) {
            $info = array( 'name' => '
        }
    }

}

?>

