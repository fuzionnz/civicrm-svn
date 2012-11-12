<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Financial/Form/Task.php';
require_once 'CRM/Core/BAO/Batch.php';

/**
 * This class provides the functionality to delete a group of
 * contributions. This class provides functionality for the actual
 * deletion.
 */
class CRM_Financial_Form_Task_Export extends CRM_Financial_Form_Task {

    /**
     * Are we operating in "single mode", i.e. deleting one
     * specific contribution?
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
    function preProcess() {
        //check for delete
        if ( !CRM_Core_Permission::checkActionPermission( 'CiviContribute', CRM_Core_Action::UPDATE ) ) {
            CRM_Core_Error::fatal( ts( 'You do not have permission to access this page' ) );  
        }
        parent::preProcess();
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm() {
        
        $this->addDefaultButtons(ts('Export Batch(s)'), 'done');
        foreach ( $this->_financialBatchIds as $financialBatchId )
            $batchNames[] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_Batch', $financialBatchId, 'name' );
        $this->assign( 'batchNames', $batchNames );
        $this->assign( 'batchCount', count( $this->_financialBatchIds ) );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess( ) 
    {
        $ids =array();
        CRM_Core_BAO_Batch::exportBatch( $this->_financialBatchIds, $ids );
        

    }


}

