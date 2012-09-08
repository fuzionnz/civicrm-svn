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

/**
 * This class generates form components for Membership Type
 *
 */
class CRM_Member_Form_MembershipType extends CRM_Member_Form {

  /**
   * max number of contacts we will display for membership-organisation
   */
  CONST MAX_CONTACTS = 50;

  /**
   * This function sets the default values for the form. MobileProvider that in edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return None
   */
  public function setDefaultValues() {
    $defaults = array();
    $defaults = parent::setDefaultValues();

    //finding default weight to be put
    if (!isset($defaults['weight']) || (!$defaults['weight'])) {
      $defaults['weight'] = CRM_Utils_Weight::getDefaultWeight('CRM_Member_DAO_MembershipType');
    }
    //setting default relationshipType
    if (isset($defaults['relationship_type_id'])) {
      //$defaults['relationship_type_id'] = $defaults['relationship_type_id'].'_a_b';
      // Set values for relation type select box
      $relTypeIds = explode(CRM_Core_DAO::VALUE_SEPARATOR, $defaults['relationship_type_id']);
      $relDirections = explode(CRM_Core_DAO::VALUE_SEPARATOR, $defaults['relationship_direction']);
      $defaults['relationship_type_id'] = array();
      foreach ($relTypeIds as $key => $value) {
        $defaults['relationship_type_id'][] = $value . '_' . $relDirections[$key];
      }
    }

    $config = CRM_Core_Config::singleton();
    //setting default fixed_period_start_day & fixed_period_rollover_day
    $periods = array('fixed_period_start_day', 'fixed_period_rollover_day');
    foreach ($periods as $per) {
      if (isset($defaults[$per])) {
        $dat                 = $defaults[$per];
        $dat                 = ($dat < 999) ? '0' . $dat : $dat;
        $defaults[$per]      = array();
        $defaults[$per]['M'] = substr($dat, 0, 2);
        $defaults[$per]['d'] = substr($dat, 2, 3);
      }
    }

    return $defaults;
  }

  /**
   * Function to build the form
   *
   * @return None
   * @access public
   */
  public function buildQuickForm() {
    parent::buildQuickForm();

    if ($this->_action & CRM_Core_Action::DELETE) {
      return;
    }

    $this->applyFilter('__ALL__', 'trim');
    $this->add('text', 'name', ts('Name'), CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipType', 'name'), TRUE);

    $this->addRule('name', ts('A membership type with this name already exists. Please select another name.'),
      'objectExists', array('CRM_Member_DAO_MembershipType', $this->_id)
    );
    $this->add('text', 'description', ts('Description'),
      CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipType', 'description')
    );
    $this->add('text', 'minimum_fee', ts('Minimum Fee'),
      CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipType', 'minimum_fee')
    );
    $this->addRule('minimum_fee', ts('Please enter a monetary value for the Minimum Fee.'), 'money');

    $this->addElement('select', 'duration_unit', ts('Duration') . ' ',
      CRM_Core_SelectValues::unitList('duration'), array('onchange' => 'showHidePeriodSettings()')
    );
    //period type
    $this->addElement('select', 'period_type', ts('Period Type'),
      CRM_Core_SelectValues::periodType(), array('onchange' => 'showHidePeriodSettings()')
    );

    $this->add('text', 'duration_interval', ts('Duration Interval'),
      CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipType', 'duration_interval')
    );

    $memberOrg = &$this->add('text', 'member_org', ts('Membership Organization'), 'size=30 maxlength=120');
    //start day
    $this->add('date', 'fixed_period_start_day', ts('Fixed Period Start Day'),
      CRM_Core_SelectValues::date(NULL, 'M d'), FALSE
    );

    //Auto-renew Option
    $paymentProcessor  = CRM_Core_PseudoConstant::paymentProcessor(FALSE, FALSE, 'is_recur = 1');
    $isAuthorize       = FALSE;
    $options           = array();
    if (is_array($paymentProcessor) && !empty($paymentProcessor)) {
      $isAuthorize = TRUE;
      $options = array(ts('No auto-renew option'), ts('Give option, but not required'), ts('Auto-renew required '));
    }
    $this->addRadio('auto_renew', ts('Auto-renew Option'), $options);
    $this->assign('authorize', $isAuthorize);

    //rollover day
    $this->add('date', 'fixed_period_rollover_day', ts('Fixed Period Rollover Day'),
      CRM_Core_SelectValues::date(NULL, 'M d'), FALSE
    );

    // required in form rule
    $this->add('hidden', 'action', $this->_action);

    $this->add('select', 'contribution_type_id', ts('Contribution Type'),
      array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::contributionType()
    );

    $relTypeInd = CRM_Contact_BAO_Relationship::getContactRelationshipType(NULL, NULL, NULL, NULL, TRUE);
    if (is_array($relTypeInd)) {
      asort($relTypeInd);
    }
    $memberRel = &$this->add('select', 'relationship_type_id', ts('Relationship Type'), array('' => ts('- select -')) + $relTypeInd);
    $memberRel->setMultiple(TRUE);

    $this->add('select', 'visibility', ts('Visibility'), CRM_Core_SelectValues::memberVisibility());
    $this->add('text', 'weight', ts('Order'),
      CRM_Core_DAO::getAttribute('CRM_Member_DAO_MembershipType', 'weight')
    );
    $this->add('checkbox', 'is_active', ts('Enabled?'));

    $searchRows  = $this->get('searchRows');
    $searchCount = $this->get('searchCount');
    $searchDone  = $this->get('searchDone');

    if ($searchRows) {
      $checkBoxes = array();
      $chekFlag = 0;
      foreach ($searchRows as $id => $row) {
        $checked = '';
        if (!$chekFlag) {
          $checked = array('checked' => NULL);
          $chekFlag++;
        }

        $checkBoxes[$id] = $this->createElement('radio', NULL, NULL, NULL, $id, $checked);
      }

      $this->addGroup($checkBoxes, 'contact_check');
      $this->assign('searchRows', $searchRows);
    }

    $this->assign('searchCount', $searchCount);
    $this->assign('searchDone', $searchDone);

    if ($searchDone) {
      $searchBtn = ts('Search Again');
    }
    elseif ($this->_action & CRM_Core_Action::UPDATE) {
      $searchBtn = ts('Change');
    }
    else {
      $searchBtn = ts('Search');
    }
    $membershipRecords = FALSE;
    if ($this->_action & CRM_Core_Action::UPDATE) {
      $membershipType = new CRM_Member_BAO_Membership();
      $membershipType->membership_type_id = $this->_id;
      if ($membershipType->find(TRUE)) {
        $membershipRecords = TRUE;
        $memberRel->freeze();
      }
      $memberOrg->freeze();
      if ($searchDone) {
        $memberOrg->unfreeze();
      }
    }

    $this->assign('membershipRecordsExists', $membershipRecords);

    $this->addElement('submit', $this->getButtonName('refresh'), $searchBtn, array('class' => 'form-submit'));

    $this->addFormRule(array('CRM_Member_Form_MembershipType', 'formRule'));

    $this->assign('membershipTypeId', $this->_id);
  }

  /**
   * Function for validation
   *
   * @param array $params (ref.) an assoc array of name/value pairs
   *
   * @return mixed true or array of errors
   * @access public
   * @static
   */
  static function formRule($params) {
    $errors = array();
    if (!isset($params['_qf_MembershipType_refresh']) || !$params['_qf_MembershipType_refresh']) {
      if (!$params['name']) {
        $errors['name'] = ts('Please enter a membership type name.');
      }

      if (!CRM_Utils_Array::value('contact_check', $params) && $params['action'] != CRM_Core_Action::UPDATE) {
        $errors['member_org'] = ts('Please select the membership organization');
      }

      if (empty($params['contribution_type_id'])) {
        $errors['contribution_type_id'] = ts('Please enter a contribution type.');
      }

      if (($params['minimum_fee'] > 0) && !$params['contribution_type_id']) {
        $errors['contribution_type_id'] = ts('Please enter the contribution type.');
      }

      if (empty($params['duration_unit'])) {
        $errors['duration_unit'] = ts('Please enter a duration unit.');
      }

      if (empty($params['duration_interval']) and $params['duration_unit'] != 'lifetime') {
        $errors['duration_interval'] = ts('Please enter a duration interval.');
      }

      if (in_array(CRM_Utils_Array::value('auto_renew', $params), array(
        1, 2))) {
        if (($params['duration_interval'] > 1 && $params['duration_unit'] == 'year') ||
          ($params['duration_interval'] > 12 && $params['duration_unit'] == 'month')
        ) {
          $errors['duration_unit'] = ts('Automatic renewals are not supported by the currently available payment processors when the membership duration is greater than 1 year / 12 months.');
        }
      }

      if (empty($params['period_type'])) {
        $errors['period_type'] = ts('Please select a period type.');
      }

      if ($params['period_type'] == 'fixed' &&
        $params['duration_unit'] == 'day'
      ) {
        $errors['period_type'] = ts('Period type should be Rolling when duration unit is Day');
      }

      $config = CRM_Core_Config::singleton();
      if (($params['period_type'] == 'fixed') &&
        ($params['duration_unit'] == 'year')
      ) {
        $periods = array('fixed_period_start_day', 'fixed_period_rollover_day');
        foreach ($periods as $period) {
          $month = $params[$period]['M'];
          $date = $params[$period]['d'];
          if (!$month || !$date) {
            switch ($period) {
              case 'fixed_period_start_day':
                $errors[$period] = ts('Please enter a valid fixed period start day');
                break;

              case 'fixed_period_rollover_day':
                $errors[$period] = ts('Please enter a valid fixed period rollover day');
                break;
            }
          }
        }
      }
    }

    if ($params['fixed_period_start_day'] && !empty($params['fixed_period_start_day'])) {
      $params['fixed_period_start_day']['Y'] = date('Y');
      if (!CRM_Utils_Rule::qfDate($params['fixed_period_start_day'])) {
        $errors['fixed_period_start_day'] = ts('Please enter valid Fixed Period Start Day');
      }
    }

    if ($params['fixed_period_rollover_day'] && !empty($params['fixed_period_rollover_day'])) {
      $params['fixed_period_rollover_day']['Y'] = date('Y');
      if (!CRM_Utils_Rule::qfDate($params['fixed_period_rollover_day'])) {
        $errors['fixed_period_rollover_day'] = ts('Please enter valid Fixed Period Rollover Day');
      }
    }

    return empty($errors) ? TRUE : $errors;
  }

  /**
   * Function to process the form
   *
   * @access public
   *
   * @return None
   */
  public function postProcess() {
    if ($this->_action & CRM_Core_Action::DELETE) {
      CRM_Utils_Weight::delWeight('CRM_Member_DAO_MembershipType', $this->_id);
      CRM_Member_BAO_MembershipType::del($this->_id);
      CRM_Core_Session::setStatus(ts('Selected membership type has been deleted.'));
    }
    else {
      $buttonName = $this->controller->getButtonName();
      $submitted = $this->controller->exportValues($this->_name);

      $this->set('searchDone', 0);
      if ($buttonName == '_qf_MembershipType_refresh') {
        $this->search($submitted);
        $this->set('searchDone', 1);
        return;
      }

      $fields = array(
        'name',
        'weight',
        'is_active',
        'member_org',
        'visibility',
        'period_type',
        'minimum_fee',
        'description',
        'auto_renew',
        'duration_unit',
        'duration_interval',
        'contribution_type_id',
        'fixed_period_start_day',
        'fixed_period_rollover_day',
      );

      $params = $ids = array();
      foreach ($fields as $fld) {
        $params[$fld] = CRM_Utils_Array::value($fld, $submitted, 'NULL');
      }

      //clean money.
      if ($params['minimum_fee']) {
        $params['minimum_fee'] = CRM_Utils_Rule::cleanMoney($params['minimum_fee']);
      }

      $hasRelTypeVal = FALSE;
      if (!CRM_Utils_System::isNull($submitted['relationship_type_id'])) {
        // To insert relation ids and directions with value separator
        $relTypeDirs = $submitted['relationship_type_id'];
        $relIds = $relDirection = array();
        foreach ($relTypeDirs as $key => $value) {
          $relationId = explode('_', $value);
          if (count($relationId) == 3 &&
            is_numeric($relationId[0])
          ) {
            $relIds[] = $relationId[0];
            $relDirection[] = $relationId[1] . '_' . $relationId[2];
          }
        }
        if (!empty($relIds)) {
          $hasRelTypeVal = TRUE;
          $params['relationship_type_id'] = implode(CRM_Core_DAO::VALUE_SEPARATOR, $relIds);
          $params['relationship_direction'] = implode(CRM_Core_DAO::VALUE_SEPARATOR, $relDirection);
        }
      }
      if (!$hasRelTypeVal) {
        $params['relationship_type_id'] = $params['relationship_direction'] = 'NULL';
      }

      if ($params['duration_unit'] == 'lifetime' &&
        empty($params['duration_interval'])
      ) {
        $params['duration_interval'] = 1;
      }

      $config = CRM_Core_Config::singleton();
      $periods = array('fixed_period_start_day', 'fixed_period_rollover_day');
      foreach ($periods as $per) {
        if (CRM_Utils_Array::value('M', $params[$per]) &&
          CRM_Utils_Array::value('d', $params[$per])
        ) {
          $mon          = $params[$per]['M'];
          $dat          = $params[$per]['d'];
          $mon          = ($mon < 9) ? '0' . $mon : $mon;
          $dat          = ($dat < 9) ? '0' . $dat : $dat;
          $params[$per] = $mon . $dat;
        }
        else {
          $params[$per] = 'NULL';
        }
      }
      $oldWeight = NULL;
      $ids['memberOfContact'] = CRM_Utils_Array::value('contact_check', $submitted);

      if ($this->_id) {
        $oldWeight = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType',
          $this->_id, 'weight', 'id'
        );
      }
      $params['weight'] = CRM_Utils_Weight::updateOtherWeights('CRM_Member_DAO_MembershipType',
        $oldWeight, $params['weight']
      );

      if ($this->_action & CRM_Core_Action::UPDATE) {
        $ids['membershipType'] = $this->_id;
      }

      // $previousID is the old organization id for memberhip type i.e 'member_of_contact_id'. This is used when an oganization is changed.
      $previousID = NULL;
      if ($this->_id) {
        $previousID = CRM_Core_DAO::getFieldValue('CRM_Member_DAO_MembershipType', $this->_id, 'member_of_contact_id');
      }

      $membershipType = CRM_Member_BAO_MembershipType::add($params, $ids);
      $priceSetId = CRM_Core_DAO::getFieldValue('CRM_Price_DAO_Set', 'default_membership_type_amount', 'id', 'name');

      if (CRM_Utils_Array::value('contact_check', $submitted)) {
        $fieldName = $submitted['contact_check'];
      }
      else {
        $fieldName = $previousID;
      }
      $fieldLabel  = 'Membership Amount';
      $optionsIds  = NULL;
      $fieldParams = array(
        'price_set_id ' => $priceSetId,
        'name' => $fieldName,
      );
      $results = array();
      CRM_Price_BAO_Field::retrieve(&$fieldParams, &$results);
      if (empty($results)) {
        $fieldParams = array();
        $fieldParams['label'] = $fieldLabel;
        $fieldParams['name'] = $fieldName;
        $fieldParams['price_set_id'] = $priceSetId;
        $fieldParams['html_type'] = 'Radio';
        $fieldParams['is_display_amounts'] = $fieldParams['is_required'] = 0;
        $fieldParams['weight'] = $fieldParams['option_weight'][1] = 1;
        $fieldParams['option_label'][1] = $submitted['name'];
        $fieldParams['membership_type_id'][1] = $membershipType->id;
        $fieldParams['option_amount'][1] = empty($submitted['minimum_fee']) ? 0 : $submitted['minimum_fee'];

        if ($previousID) {
          $this->checkPreviousPriceField($previousID, $priceSetId, $membershipType->id, $optionsIds);
          $fieldParams['option_id'] = CRM_Utils_Array::value('option_id', $optionsIds);
        }
        $priceField = CRM_Price_BAO_Field::create($fieldParams);
      }
      else {
        $fieldID = $results['id'];
        $fieldValueParams = array(
          'price_field_id' => $fieldID,
          'membership_type_id' => $membershipType->id,
        );
        $results = array();
        CRM_Price_BAO_FieldValue::retrieve(&$fieldValueParams, &$results);
        if (!empty($results)) {
          $results['label']  = $results['name'] = $submitted['name'];
          $results['amount'] = empty($submitted['minimum_fee']) ? 0 : $submitted['minimum_fee'];
          $optionsIds['id']  = $results['id'];
        }
        else {
          $results = array(
            'price_field_id' => $fieldID,
            'name' => $submitted['name'],
            'label' => $submitted['name'],
            'amount' => empty($submitted['minimum_fee']) ? 0 : $submitted['minimum_fee'],
            'membership_type_id' => $membershipType->id,
            'is_active' => 1,
          );
        }

        if ($previousID) {
          $this->checkPreviousPriceField($previousID, $priceSetId, $membershipType->id, $optionsIds);
          if (CRM_Utils_Array::value('option_id', $optionsIds)) {
            $optionsIds['id'] = current(CRM_Utils_Array::value('option_id', $optionsIds));
          }
        }
        CRM_Price_BAO_FieldValue::add($results, $optionsIds);
      }

      CRM_Core_Session::setStatus(ts('The membership type \'%1\' has been saved.',
          array(1 => $membershipType->name)
        ));
      $session = CRM_Core_Session::singleton();
      if ($buttonName == $this->getButtonName('upload', 'new')) {
        CRM_Core_Session::setStatus(ts(' You can add another membership type.'));
        $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin/member/membershipType',
            'action=add&reset=1'
          ));
      }
    }
  }

  function checkPreviousPriceField($previousID, $priceSetId, $membershipTypeId, &$optionsIds) {
    if ($previousID) {
      $editedFieldParams = array(
        'price_set_id ' => $priceSetId,
        'name' => $previousID,
      );
      $editedResults = array();
      CRM_Price_BAO_Field::retrieve(&$editedFieldParams, &$editedResults);
      if (!empty($editedResults)) {
        $editedFieldParams = array(
          'price_field_id' => $editedResults['id'],
          'membership_type_id' => $membershipTypeId,
        );
        $editedResults = array();
        CRM_Price_BAO_FieldValue::retrieve(&$editedFieldParams, &$editedResults);
        $optionsIds['option_id'][1] = CRM_Utils_Array::value('id', $editedResults);
      }
    }
  }

  /**
   * This function is to get the result of the search for membership organisation.
   *
   * @param  array $params  This contains elements for search criteria
   *
   * @access public
   *
   * @return None
   *
   */
  function search(&$params) {
    //max records that will be listed
    $searchValues = array();
    if (!empty($params['member_org'])) {
      $searchValues[] = array('sort_name', 'LIKE', $params['member_org'], 0, 1);
    }
    $searchValues[] = array('contact_type', '=', 'organization', 0, 0);

    // get the count of contact
    $contactBAO  = new CRM_Contact_BAO_Contact();
    $query       = new CRM_Contact_BAO_Query($searchValues);
    $searchCount = $query->searchQuery(0, 0, NULL, TRUE);
    $this->set('searchCount', $searchCount);
    if ($searchCount <= self::MAX_CONTACTS) {
      // get the result of the search
      $result = $query->searchQuery(0, self::MAX_CONTACTS, NULL);

      $config = CRM_Core_Config::singleton();
      $searchRows = array();

      while ($result->fetch()) {
        $contactID = $result->contact_id;

        $searchRows[$contactID]['id'] = $contactID;
        $searchRows[$contactID]['name'] = $result->sort_name;
        $searchRows[$contactID]['city'] = $result->city;
        $searchRows[$contactID]['state'] = $result->state_province;
        $searchRows[$contactID]['email'] = $result->email;
        $searchRows[$contactID]['phone'] = $result->phone;

        $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';

        $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';

        $searchRows[$contactID]['type'] = $contact_type;
      }
      $this->set('searchRows', $searchRows);
    }
    else {
      // resetting the session variables if many records are found
      $this->set('searchRows', NULL);
    }
  }
}

