<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 *
 */
class CRM_Financial_Form_Search extends CRM_Core_Form {

  public $_batchStatus;

  function preProcess() {
    $batchStatus = CRM_Utils_Request::retrieve('batchStatus', 'Positive', CRM_Core_DAO::$_nullObject, FALSE, 1);
    $this->_batchStatus = $batchStatus;
    $this->assign("batchStatus",$batchStatus);
  }

  function setDefaultValues() {
    $defaults = array();
    $status = CRM_Utils_Request::retrieve('status', 'Positive', CRM_Core_DAO::$_nullObject, FALSE, 1);
    $defaults['batch_status'] = $status;
    return $defaults;
  }

  public function buildQuickForm() {
    $attributes = CRM_Core_DAO::getAttribute('CRM_Batch_DAO_Batch');
    $this->add('text', 'title', ts('Batch Name'), $attributes['title']);

    $this->add(
      'select',
      'payment_instrument_id',
      ts('Payment Instrument'),
      array('' => ts('- any -' )) + CRM_Contribute_PseudoConstant::paymentInstrument(),
      false
    );

    $this->add('text', 'total', ts('Total Amount'), $attributes['total']);

    $this->add('text', 'item_count', ts('Number of Transactions'), $attributes['item_count']);
    $this->add('text', 'sort_name', ts('Created By'), CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact', 'sort_name'));

    $this->assign('elements', array('title', 'sort_name', 'payment_instrument_id', 'item_count', 'total'));
    $this->addElement('checkbox', 'toggleSelect', NULL, NULL);
    $batchAction = array(
      'Open' => ts('ReOpen Batch'),
      'Closed' => ts('Close Batch'),
      'Exported' => ts('Export Batch')
    );

    if ($this->_batchStatus == 1) {
      unset($batchAction['Open']);
    }
    elseif ($this->_batchStatus == 2) {
      unset($batchAction['Closed']);
    }

    $this->add('select',
      'batch_status',
      ts('Task' ),
      array('' => ts('- actions -')) + $batchAction);

    $this->add('submit','submit', ts('Go'),
      array(
        'class' => 'form-submit',
        'id' => 'Go'
      ));

    $this->addButtons(
      array(
        array(
          'type' => 'refresh',
          'name' => ts('Search'),
          'isDefault' => TRUE
        )
      )
    );

    parent::buildQuickForm();
  }

  function postProcess() {
    $batchIds = array();
    foreach ($_POST as $key => $value) {
      if (substr($key,0,6) == "check_") {
        $batch = explode("_",$key);
        $batchIds[] = $batch[1];
      }
    }
    if (CRM_Utils_Array::value('batch_status', $_POST)) {
      CRM_Batch_BAO_Batch::closeReOpen($batchIds, $_POST['batch_status']);
    }
  }
}

