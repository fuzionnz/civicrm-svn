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
 * Used for displaying results
 *
 *
 */
class CRM_Contact_Form_Task_Result extends CRM_Contact_Form_Task {

    /**
     * class constructor
     *
     */
    function __construct( $name, $state, $mode = self::MODE_NONE ) {
        parent::__construct($name, $state, $mode);
    }


/**
* Function to actually build the form
 *
 * @return None
 * @access public
 */
public function buildQuickForm( ) {
    if ( $this->get( 'context' ) == 'smog' ) {
        $session = CRM_Core_Session::singleton( );
        $session->replaceUserContext( CRM_Utils_System::url( 'civicrm/group/search', 'reset=1&force=1&context=smog&gid=' . $this->get( 'gid' ) ) );
    }
    
    $this->addButtons( array(
                             array ( 'type'      => 'next',
                                     'name'      => 'Done',
                                     'isDefault' => true   ),
                             )
                       );
}

}
?>