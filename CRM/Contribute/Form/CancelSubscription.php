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

/**
 * This class provides support for canceling recurring subscriptions
 *
 */
class CRM_Contribute_Form_CancelSubscription extends CRM_Core_Form {
  protected $_paymentProcessorObj = NULL;

  protected $_userContext = NULL;

  protected $_mode = NULL;

  protected $_mid = NULL;

  protected $_coid = NULL;

  protected $_crid = NULL;

  /**
   * Function to set variables up before form is built
   *
   * @return void
   * @access public
   */
  public function preProcess() {
    $this->_mid = CRM_Utils_Request::retrieve('mid', 'Integer', $this, FALSE);
    if ($this->_mid) {
      if (CRM_Member_BAO_Membership::isSubscriptionCancelled($this->_mid)) {
        CRM_Core_Error::fatal(ts('The auto renewal option for this membership looks to have been cancelled already.'));
      }
      $this->_mode = 'auto_renew';
      $this->_paymentProcessorObj = CRM_Core_BAO_PaymentProcessor::getProcessorForEntity($this->_mid, 'membership', 'obj');
      $this->_subscriptionDetails = CRM_Contribute_BAO_ContributionRecur::getSubscriptionDetails($this->_mid, 'membership');

      $membershipTypes = CRM_Member_PseudoConstant::membershipType();
      $membershipTypeId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $this->_mid, 'membership_type_id');
      $this->assign('membershipType', CRM_Utils_Array::value($membershipTypeId, $membershipTypes));
    }

    $this->_crid = CRM_Utils_Request::retrieve('crid', 'Integer', $this, FALSE);
    if ($this->_crid) {
      $this->_paymentProcessorObj = CRM_Core_BAO_PaymentProcessor::getProcessorForEntity($this->_crid, 'recur', 'obj');
      $this->_subscriptionDetails = CRM_Contribute_BAO_ContributionRecur::getSubscriptionDetails($this->_crid);
      // Are we cancelling a recurring contribution that is linked to an auto-renew membership?
      if ($this->_subscriptionDetails->membership_id) {
        $this->_mode = 'auto_renew';
        $this->_mid = $this->_subscriptionDetails->membership_id;
        $membershipTypes = CRM_Member_PseudoConstant::membershipType();
        $membershipTypeId = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_Membership', $this->_mid, 'membership_type_id');
        $this->assign('membershipType', CRM_Utils_Array::value($membershipTypeId, $membershipTypes));
      }
    }

    $this->_coid = CRM_Utils_Request::retrieve('coid', 'Integer', $this, FALSE);
    if ($this->_coid) {
      if (CRM_Contribute_BAO_Contribution::isSubscriptionCancelled($this->_coid)) {
        CRM_Core_Error::fatal(ts('The recurring contribution looks to have been cancelled already.'));
      }
      $this->_paymentProcessorObj = CRM_Core_BAO_PaymentProcessor::getProcessorForEntity($this->_coid, 'contribute', 'obj');
      $this->_subscriptionDetails = CRM_Contribute_BAO_ContributionRecur::getSubscriptionDetails($this->_coid, 'contribution');
    }

    if ((!$this->_crid && !$this->_coid && !$this->_mid) ||
      ($this->_subscriptionDetails == CRM_Core_DAO::$_nullObject)
    ) {
      CRM_Core_Error::fatal('Required information missing.');
    }

    if (!CRM_Core_Permission::check('edit contributions')) {
      $userChecksum = CRM_Utils_Request::retrieve('cs', 'String', $this, FALSE);
      if (!CRM_Contact_BAO_Contact_Utils::validChecksum($this->_subscriptionDetails->contact_id, $userChecksum)) {
        CRM_Core_Error::fatal(ts('You do not have permission to cancel subscription.'));
      }
    }

    // handle context redirection
    CRM_Contribute_BAO_ContributionRecur::setSubscriptionContext();

    CRM_Utils_System::setTitle($this->_mid ? ts('Cancel Auto-renewal') : ts('Cancel Recurring Contribution'));
    $this->assign('mode', $this->_mode);

    if ($this->_subscriptionDetails->contact_id) {
      list($this->_donorDisplayName, $this->_donorEmail) = CRM_Contact_BAO_Contact::getContactDetails($this->_subscriptionDetails->contact_id);
    }
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    if ($this->_paymentProcessorObj->isSupported('cancelSubscription')) {
      $searchRange   = array();
      $searchRange[] = $this->createElement('radio', NULL, NULL, ts('Yes'), '1');
      $searchRange[] = $this->createElement('radio', NULL, NULL, ts('No'), '0');

      $this->addGroup($searchRange, 'send_cancel_request', ts('Send cancellation request to %1 ?', array(1 => $this->_paymentProcessorObj->_processorName)));
    }
    if ($this->_donorEmail) {
      $this->add('checkbox', 'is_notify', ts('Notify Contributor?'));
    }
    if ($this->_mid) {
      $cancelButton = ts('Cancel Automatic Membership Renewal');
    }
    else {
      $cancelButton = ts('Cancel Recurring Contribution');
    }

    $this->addButtons(array(
        array(
          'type' => 'next',
          'name' => $cancelButton,
          'spacing' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
          'isDefault' => TRUE,
        ),
        array(
          'type' => 'cancel',
          'name' => ts('Not Now'),
        ),
      )
    );
  }

  /**
   * This function sets the default values for the form. Note that in edit/view mode
   * the default values are retrieved from the database
   *
   * @param null
   *
   * @return array    array of default values
   * @access public
   */
  function setDefaultValues() {
    $defaults = array();
    $defaults['is_notify'] = 1;
    return $defaults;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    $status             = $message = NULL;
    $cancelSubscription = TRUE;
    $params             = $this->controller->exportValues($this->_name);

    if ($params['send_cancel_request'] == 1) {
      $cancelParams = array('subscriptionId' => $this->_subscriptionDetails->subscription_id);
      $cancelSubscription = $this->_paymentProcessorObj->cancelSubscription($message, $cancelParams);
    }

    if (is_a($cancelSubscription, 'CRM_Core_Error')) {
      CRM_Core_Error::displaySessionError($cancelSubscription);
    }
    elseif ($cancelSubscription) {
      $activityParams = array('subject' => $this->_mid ? ts('Auto-renewal membership cancelled') : ts('Recurring contribution cancelled'),
        'details' => $message,
      );
      $cancelStatus = CRM_Contribute_BAO_ContributionRecur::cancelRecurContribution($this->_subscriptionDetails->recur_id,
        CRM_Core_DAO::$_nullObject, $activityParams
      );
      if ($cancelStatus) {
        $tplParams = array();
        if ($this->_mid) {
          $inputParams = array('id' => $this->_mid);
          CRM_Member_BAO_Membership::getValues($inputParams, $tplParams);
          $tplParams = $tplParams[$this->_mid];
          $tplParams['membership_status'] = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipStatus', $tplParams['status_id']);
          $tplParams['membershipType'] = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $tplParams['membership_type_id']);
          $status = ts('The automatic renewal of your %1 membership has been cancelled as requested. This does not affect the status of your membership - you will receive a separate notification when your membership is up for renewal.', array(1 => $tplParams['membershipType']));
        }
        else {
          $tplParams['recur_frequency_interval'] = $this->_subscriptionDetails->frequency_interval;
          $tplParams['recur_frequency_unit'] = $this->_subscriptionDetails->frequency_unit;
          $tplParams['amount'] = $this->_subscriptionDetails->amount;
          $tplParams['contact'] = array('display_name' => $this->_donorDisplayName);
          $status = ts('The recurring contribution of %1, every %2 %3 has been cancelled.',
            array(
              1 => $this->_subscriptionDetails->amount,
              2 => $this->_subscriptionDetails->frequency_interval,
              3 => $this->_subscriptionDetails->frequency_unit,
            )
          );
        }

        if ($this->_subscriptionDetails->contribution_page_id) {
          CRM_Core_DAO::commonRetrieveAll('CRM_Contribute_DAO_ContributionPage', 'id',
            $this->_subscriptionDetails->contribution_page_id, $value, array(
              'title',
              'receipt_from_name',
              'receipt_from_email',
            )
          );
          $receiptFrom = '"' . CRM_Utils_Array::value('receipt_from_name', $value[$this->_subscriptionDetails->contribution_page_id]) . '" <' . $value[$this->_subscriptionDetails->contribution_page_id]['receipt_from_email'] . '>';
        }
        else {
          $domainValues = CRM_Core_BAO_Domain::getNameAndEmail();
          $receiptFrom = "$domainValues[0] <$domainValues[1]>";
        }

        // send notification
        $sendTemplateParams = array(
          'groupName' => $this->_mode == 'auto_renew' ? 'msg_tpl_workflow_membership' : 'msg_tpl_workflow_contribution',
          'valueName' => $this->_mode == 'auto_renew' ? 'membership_autorenew_cancelled' : 'contribution_recurring_cancelled',
          'contactId' => $this->_subscriptionDetails->contact_id,
          'tplParams' => $tplParams,
          //'isTest'    => $isTest, set this from _objects
          'PDFFilename' => 'receipt.pdf',
          'from' => $receiptFrom,
          'toName' => $this->_donorDisplayName,
          'toEmail' => $this->_donorEmail,
        );
        list($sent) = CRM_Core_BAO_MessageTemplates::sendTemplate($sendTemplateParams);
      }
      else {
        if ($params['send_cancel_request'] == 1) {
          $status = ts('Recurring contribution was cancelled successfully by the processor, but could not be marked as cancelled in the database.');
        }
        else $status = ts('Recurring contribution could not be cancelled in the database.');
      }
    }
    else {
      $status = ts('The recurring contribution could not be cancelled.');
    }

    if ($status) {
      $session = CRM_Core_Session::singleton();
      if ($session->get('userID')) {
        CRM_Core_Session::setStatus($status);
      }
      else {
        CRM_Utils_System::setUFMessage($message);
      }
    }
  }
}

