<?php

/*
  +--------------------------------------------------------------------+
  | CiviCRM version 4.0                                                |
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

/**
 * This class generates form components for Financial Type
 *
 */
class CRM_Financial_Form_BatchTransaction extends CRM_Contribute_Form {
  static $_links = null;
  static $_entityID;

  function preProcess() {
    self::$_entityID = CRM_Utils_Request::retrieve( 'bid' , 'Positive' ) ? CRM_Utils_Request::retrieve( 'bid' , 'Positive' ) : $_POST['batch_id'];
    $this->assign('entityID', self::$_entityID);
    if (isset(self::$_entityID)) {
      $statusID = CRM_Core_DAO::getFieldValue('CRM_Batch_BAO_Batch', self::$_entityID, 'status_id');
      $this->assign('statusID', $statusID);
      $batchTitle = CRM_Core_DAO::getFieldValue('CRM_Batch_BAO_Batch', self::$_entityID, 'title');
      CRM_Utils_System::setTitle(ts('Accounting Batch - %1', array(1 => $batchTitle)));

      $columnHeaders =
        array(
          'created_by' => ts('Created By'),
          'description'=> ts('Description'),
          'payment_instrument' => ts('Payment Instrument'),
          'type' => ts('Type'),
          'item_count' => ts('Entered Transactions'),
          'assigned_item_count' => ts('Assigned Transactions'),
          'total' => ts('Entered Total'),
          'assigned_total' => ts('Assigned Total'),
          'opened_date' => ts('Opened'),
        );
      $this->assign('columnHeaders', $columnHeaders);
    }
  }
  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
    $this->add('submit', 'close_batch', ts('Close Batch'));
    $this->add('submit', 'export_batch', ts('Close & Export'));
    // text for sort_name
    $this->addElement('text',
      'sort_name',
      ts('Contributor Name or Email'),
      CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Contact',
        'sort_name'
      )
    );

    $this->_group = CRM_Core_PseudoConstant::group();
    
    // multiselect for groups
    if ($this->_group) {
      $this->add('select', 'group', ts('Groups'), $this->_group, FALSE,
        array('id' => 'group', 'multiple' => 'multiple', 'title' => ts('- select -'))
      );
    }
    $contactTags = CRM_Core_BAO_Tag::getTags();
    
    if ($contactTags) {
      $this->add('select', 'contact_tags', ts('Tags'), $contactTags, FALSE,
        array('id' => 'contact_tags', 'multiple' => 'multiple', 'title' => ts('- select -'))
      );
    }
    CRM_Contribute_BAO_Query::buildSearchForm($this);
    $this->addElement('checkbox', 'toggleSelects', NULL, NULL);

    $this->add( 'select',
      'trans_remove',
      ts('Task'),
      array( ''  => ts( '- actions -' )) +  array( 'Remove' => ts('Remove from Batch')));

    $this->add('submit','rSubmit', ts('Go'),
      array(
        'class' => 'form-submit',
        'id' => 'GoRemove',
      ));

    self::$_entityID = CRM_Utils_Request::retrieve('bid' , 'Positive');

    $this->addButtons(
      array(
        array('type' => 'submit',
          'name' => ts('Search'),
          'isDefault' => true,
        )
      )
    );
       
    $this->addElement('checkbox', 'toggleSelect', NULL, NULL);
    $this->add( 'select',
      'trans_assign',
      ts('Task'),
      array( ''  => ts( '- actions -' )) + array( 'Assign' => ts( 'Assign to Batch' )));

    $this->add('submit','submit', ts('Go'),
      array(
        'class' => 'form-submit',
        'id' => 'Go',
      ));
    $this->applyFilter('__ALL__', 'trim');

    $this->addElement('hidden', 'batch_id', self::$_entityID);

    $this->add('text', 'name', ts('Batch Name'));
  }

  function setDefaultValues() {
    if (isset(self::$_entityID)) {
      $paymentInstrumentID = CRM_Core_DAO::getFieldValue('CRM_Batch_BAO_Batch', self::$_entityID, 'payment_instrument_id');
      $defaults['contribution_payment_instrument_id'] = $paymentInstrumentID;
      $this->assign('paymentInstrumentID', $paymentInstrumentID);
    }
    return $defaults;
  }

  function &links() {
    if (!(self::$_links)) {
      self::$_links = array(
        'view'  => array(
          'name'  => ts('View'),
          'url'   => 'civicrm/contact/view/contribution',
          'qs'    => 'reset=1&id=%%contid%%&cid=%%cid%%&action=view&context=contribution&selectedChild=contribute',
          'title' => ts('View Contribution'),
        ),
        'assign' => array(
          'name'  => ts('Assign'),
          'ref'   => 'disable-action',
          'title' => ts('Assign Transaction'),
          'extra' => 'onclick = "assignRemove( %%id%%,\'' . 'assign' . '\' );"',
        )
      );
    }
    return self::$_links;
  }
}


