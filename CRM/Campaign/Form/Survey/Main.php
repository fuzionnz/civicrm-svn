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
 * This class generates form components for processing a survey
 *
 */
class CRM_Campaign_Form_Survey_Main extends CRM_Campaign_Form_Survey {

  /* values
     *
     * @var array
     */

  public $_values;

  /**
   * context
   *
   * @var string
   */
  protected $_context;

  protected $_reportId;

  protected $_reportTitle;

  public function preProcess() {
    parent::preProcess();

    $this->_context = CRM_Utils_Request::retrieve('context', 'String', $this);

    $this->assign('context', $this->_context);

    $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this);

    if ($this->_action & (CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE)) {
      $this->_surveyId = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);

      if ($this->_action & CRM_Core_Action::UPDATE) {
        CRM_Utils_System::setTitle(ts('Edit Survey'));
      }
      else {
        CRM_Utils_System::setTitle(ts('Delete Survey'));
      }
    }

    $this->_cdType = CRM_Utils_Array::value('type', $_GET);
    $this->assign('cdType', FALSE);
    if ($this->_cdType) {
      $this->assign('cdType', TRUE);
      return CRM_Custom_Form_CustomData::preProcess($this);
    }

    // when custom data is included in this page
    if (CRM_Utils_Array::value('hidden_custom', $_POST)) {
      CRM_Custom_Form_CustomData::preProcess($this);
      CRM_Custom_Form_CustomData::buildQuickForm($this);
    }

    $session = CRM_Core_Session::singleton();
    $url = CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey');
    $session->pushUserContext($url);

    if ($this->_name != 'Petition') {
      CRM_Utils_System::appendBreadCrumb(array(array('title' => ts('Survey Dashboard'), 'url' => $url)));
    }

    $this->_values = $this->get('values');
    if (!is_array($this->_values)) {
      $this->_values = array();
      if ($this->_surveyId) {
        $params = array('id' => $this->_surveyId);
        CRM_Campaign_BAO_Survey::retrieve($params, $this->_values);
      }
      $this->set('values', $this->_values);
    }

    if ($this->_surveyId) {
      $query  = "SELECT MAX(id) as id, title FROM civicrm_report_instance WHERE name = %1";
      $params = array( 1 => array("survey_{$this->_surveyId}",'String') );
      $result = CRM_Core_DAO::executeQuery($query, $params);
      if ( $result->fetch() ) {
        $this->_reportId = $result->id;
        $this->_reportTitle = $result->title;
      }
    }

    $this->assign('action', $this->_action);
    $this->assign('surveyId', $this->_surveyId);
    // for custom data
    $this->assign('entityID', $this->_surveyId);
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
    if ($this->_cdType) {
      return CRM_Custom_Form_CustomData::setDefaultValues($this);
    }

    $defaults = $this->_values;

    if ($this->_surveyId) {

      if (CRM_Utils_Array::value('result_id', $defaults) &&
        CRM_Utils_Array::value('recontact_interval', $defaults)
      ) {

        $resultId = $defaults['result_id'];
        $recontactInterval = unserialize($defaults['recontact_interval']);

        unset($defaults['recontact_interval']);
        $defaults['option_group_id'] = $resultId;
      }

      $ufJoinParams = array(
        'entity_table' => 'civicrm_survey',
        'entity_id' => $this->_surveyId,
        'weight' => 1,
      );

      if ($ufGroupId = CRM_Core_BAO_UFJoin::findUFGroupId($ufJoinParams)) {
        $defaults['profile_id'] = $ufGroupId;
      }
    }

    if (!isset($defaults['is_active'])) {
      $defaults['is_active'] = 1;
    }

    $defaultSurveys = CRM_Campaign_BAO_Survey::getSurveys(TRUE, TRUE);
    if (!isset($defaults['is_default']) && empty($defaultSurveys)) {
      $defaults['is_default'] = 1;
    }

    $defaults['create_report'] = 1;
    if ($this->_reportId) {
      $defaults['report_title'] = $this->_reportTitle;
    }
    return $defaults;
  }

  /**
   * Function to actually build the form
   *
   * @param null
   *
   * @return void
   * @access public
   */
  public function buildQuickForm() {
    if ($this->_cdType) {
      return CRM_Custom_Form_CustomData::buildQuickForm($this);
    }

    $this->add('text', 'title', ts('Title'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'title'), TRUE);

    $surveyActivityTypes = CRM_Campaign_BAO_Survey::getSurveyActivityType();
    // Activity Type id
    $this->add('select', 'activity_type_id', ts('Activity Type'), array('' => ts('- select -')) + $surveyActivityTypes, TRUE);

    // Campaign id
    $campaigns = CRM_Campaign_BAO_Campaign::getCampaigns(CRM_Utils_Array::value('campaign_id', $this->_values));
    $this->add('select', 'campaign_id', ts('Campaign'), array('' => ts('- select -')) + $campaigns);

    $customProfiles = CRM_Core_BAO_UFGroup::getProfiles(CRM_Campaign_BAO_Survey::surveyProfileTypes());
    // custom group id
    $this->add('select', 'profile_id', ts('Profile'),
      array(
        '' => ts('- select -')) + $customProfiles
    );

    $this->addElement('checkbox', 'create_report', ts('Create Report'));
    $this->addElement('text', 'report_title', ts('Report Title'));
   
    if( $this->_reportId){
      $this->freeze('create_report');
      $this->freeze('report_title');
    }

    // script / instructions
    $this->addWysiwyg('instructions', ts('Instructions for interviewers'), array('rows' => 5, 'cols' => 40));

    // release frequency
    $this->add('text', 'release_frequency', ts('Release frequency'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'release_frequency'));

    $this->addRule('release_frequency', ts('Release Frequency interval should be a positive number.'), 'positiveInteger');

    // max reserved contacts at a time
    $this->add('text', 'default_number_of_contacts', ts('Maximum reserved at one time'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'default_number_of_contacts'));
    $this->addRule('default_number_of_contacts', ts('Maximum reserved at one time should be a positive number'), 'positiveInteger');

    // total reserved per interviewer
    $this->add('text', 'max_number_of_contacts', ts('Total reserved per interviewer'), CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey', 'max_number_of_contacts'));
    $this->addRule('max_number_of_contacts', ts('Total reserved contacts should be a positive number'), 'positiveInteger');

    // is active ?
    $this->add('checkbox', 'is_active', ts('Active?'));

    // is default ?
    $this->add('checkbox', 'is_default', ts('Default?'));

    parent::buildQuickForm();
  }

  /**
   * Process the form
   *
   * @param null
   *
   * @return void
   * @access public
   */
  public function postProcess() {
    // store the submitted values in an array
    $params = $this->controller->exportValues($this->_name);

    $session = CRM_Core_Session::singleton();

    $params['last_modified_id'] = $session->get('userID');
    $params['last_modified_date'] = date('YmdHis');

    if ($this->_surveyId) {
      if ($this->_action & CRM_Core_Action::DELETE) {
        CRM_Campaign_BAO_Survey::del($this->_surveyId);
        CRM_Core_Session::setStatus('', ts('Survey Deleted.'), 'success');
        $session->replaceUserContext(CRM_Utils_System::url('civicrm/campaign', 'reset=1&subPage=survey'));
        return;
      }

      $params['id'] = $this->_surveyId;
    }
    else {
      $params['created_id'] = $session->get('userID');
      $params['created_date'] = date('YmdHis');
    }

    $params['is_active'] = CRM_Utils_Array::value('is_active', $params, 0);
    $params['is_default'] = CRM_Utils_Array::value('is_default', $params, 0);

    $params['custom'] = CRM_Core_BAO_CustomField::postProcess($params,
      $customFields,
      $this->_surveyId,
      'Survey'
    );
    $survey = CRM_Campaign_BAO_Survey::create($params);

    $status = false;
    if (!is_a($survey, 'CRM_Core_Error')) {
      $status = ts('Survey %1 has been saved.', array(1 => $params['title']));
    }
    $this->_surveyId = $survey->id;

    if (CRM_Utils_Array::value('result_id', $this->_values)) {
      $query = "SELECT COUNT(*) FROM civicrm_survey WHERE result_id = %1";
      $countSurvey = (int)CRM_Core_DAO::singleValueQuery($query,
        array(
          1 => array($this->_values['result_id'],
            'Positive',
          ))
      );
      // delete option group if no any survey is using it.
      if (!$countSurvey) {
        CRM_Core_BAO_OptionGroup::del($this->_values['result_id']);
      }
    }

    // create report if required.
    if ( !$this->_reportId && $survey->id && $params['create_report'] ) {
      $activityStatus = CRM_Core_PseudoConstant::activityStatus('name');
      $activityStatus = array_flip($activityStatus);
      $this->_params = 
        array( 'name'  => "survey_{$survey->id}",
               'title' => $params['report_title'] ? $params['report_title'] : $params['title'], 
               'status_id_op'    => 'eq',
               'status_id_value' => $activityStatus['Scheduled'], // reserved status
               'survey_id_value' => array($survey->id), 
               'description'     => ts('Detailed report for canvassing, phone-banking, walk lists or other surveys.'),
               );
      //Default value of order by
      $this->_params['order_bys'] =
        array(
              1 =>
              array(
                    'column' => 'sort_name',
                    'order' => 'ASC'
                    ),
              );
      // for WalkList or default
      $displayFields = array('id', 'sort_name', 'result', 'street_number','street_name','street_unit','survey_response');
      if ( CRM_Core_OptionGroup::getValue('activity_type','WalkList') == $params['activity_type_id'] ) {
        $this->_params['order_bys'] =
          array(
                1 =>
                array(
                      'column' => 'street_name',
                      'order'  => 'ASC'
                      ),
                2 =>
                array(
                      'column' => 'street_number_odd_even',
                      'order' => 'ASC'
                      ),
                3 =>
                array(
                      'column' => 'street_number',
                      'order' => 'ASC'
                      ),
                4 =>
                array(
                      'column' => 'sort_name',
                      'order' => 'ASC'
                      ),
                );
      }
      elseif ( CRM_Core_OptionGroup::getValue('activity_type','PhoneBank') == $params['activity_type_id'] ) {
        array_push($displayFields, 'phone');
      }
      elseif ((CRM_Core_OptionGroup::getValue('activity_type','Survey')  == $params['activity_type_id']) || 
              (CRM_Core_OptionGroup::getValue('activity_type','Canvass') == $params['activity_type_id']) ) {
        array_push($displayFields, 'phone','city','state_province_id','postal_code','email');
      }
      foreach($displayFields as $key){
        $this->_params['fields'][$key] = 1;
      } 
      $this->_createNew = TRUE;
      $this->_id = CRM_Report_Utils_Report::getInstanceIDForValue('survey/detail');
      CRM_Report_Form_Instance::postProcess($this, FALSE);
      
      $query = "SELECT MAX(id) FROM civicrm_report_instance WHERE name = %1";
      $reportID = CRM_Core_DAO::singleValueQuery($query, array(1 => array("survey_{$survey->id}",'String')));
      if ($reportID) {
        $url = CRM_Utils_System::url("civicrm/report/instance/{$reportID}",'reset=1');
        $status .= ts(" A Survey Detail Report <a href='%1'>%2</a> has been created.", 
                      array(1 => $url, 2 => $this->_params['title']));
      }
    }

    if ($status) {
      // reset status as we don't want status set by Instance::postProcess
      $session = CRM_Core_Session::singleton();
      $session->getStatus(TRUE);
      // set new status
      CRM_Core_Session::setStatus($status, ts('Saved'), 'success');
    }

    // also update the ProfileModule tables
    $ufJoinParams = array(
      'is_active' => 1,
      'module' => 'CiviCampaign',
      'entity_table' => 'civicrm_survey',
      'entity_id' => $survey->id,
    );

    // first delete all past entries
    if ($this->_surveyId) {
      CRM_Core_BAO_UFJoin::deleteAll($ufJoinParams);
    }
    if (CRM_Utils_Array::value('profile_id', $params)) {

      $ufJoinParams['weight'] = 1;
      $ufJoinParams['uf_group_id'] = $params['profile_id'];
      CRM_Core_BAO_UFJoin::create($ufJoinParams);
    }

    parent::endPostProcess();
  }
}

