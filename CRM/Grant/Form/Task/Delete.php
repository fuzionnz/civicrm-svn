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

require_once 'CRM/Grant/Form/Task.php';

/**
 * This class provides the functionality to delete a group of
 * participations. This class provides functionality for the actual
 * deletion.
 */
class CRM_Grant_Form_Task_Delete extends CRM_Grant_Form_Task 
{
    /**
     * Are we operating in "single mode", i.e. deleting one
     * specific participation?
     *
     * @var boolean
     */
    protected $_single = false;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) 
    {
        parent::preProcess( );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        $this->addDefaultButtons( ts( 'Delete Grants' ), 'done' );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $session =& CRM_Core_Session::singleton();
       
        $deletedGrants = 0;
         require_once 'CRM/Grant/BAO/Grant.php';
        foreach ( $this->_grantIds as $grantId ) {
            if ( CRM_Grant_BAO_Grant::del( $grantId ) ) {
                $deletedGrants++;
            }
        }

        $status = array(
                        ts( 'Deleted Grant(s): %1',        array( 1 => $deletedGrants ) ),
                        ts( 'Total Selected Grant(s): %1', array( 1 => count($this->_grantIds ) ) ),
                        );
        CRM_Core_Session::setStatus( $status );
    }
}


