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
 * Class for activity task actions
 *
 */
class CRM_Activity_Form_Task extends CRM_Core_Form {

  /**
   * the task being performed
   *
   * @var int
   */
  protected $_task;

  /**
   * The additional clause that we restrict the search with
   *
   * @var string
   */
  protected $_componentClause = NULL;

  /**
   * The array that holds all the component ids
   *
   * @var array
   */
  protected $_componentIds;

  /**
   * The array that holds all the contact ids
   *
   * @var array
   */
  public $_contactIds;

  /**
   * The array that holds all the member ids
   *
   * @var array
   */
  public $_activityHolderIds;

  /**
   * build all the data structures needed to build the form
   *
   * @param
   *
   * @return void
   * @access public
   */ function preProcess() {
    self::preProcessCommon($this);
  }

  static
  function preProcessCommon(&$form, $useTable = FALSE) {
    $form->_activityHolderIds = array();

    $values = $form->controller->exportValues($form->get('searchFormName'));

    $form->_task = $values['task'];
    $activityTasks = CRM_Activity_Task::tasks();
    $form->assign('taskName', $activityTasks[$form->_task]);

    $ids = array();
    if ($values['radio_ts'] == 'ts_sel') {
      foreach ($values as $name => $value) {
        if (substr($name, 0, CRM_Core_Form::CB_PREFIX_LEN) == CRM_Core_Form::CB_PREFIX) {
          $ids[] = substr($name, CRM_Core_Form::CB_PREFIX_LEN);
        }
      }
    }
    else {
      $queryParams = $form->get('queryParams');
      $query = new CRM_Contact_BAO_Query($queryParams, NULL, NULL, FALSE, FALSE,
        CRM_Contact_BAO_Query::MODE_ACTIVITY
      );
      $query->_distinctComponentClause = '( civicrm_activity.id )';
      $query->_groupByComponentClause = " GROUP BY civicrm_activity.id ";
      $result = $query->searchQuery(0, 0, NULL);

      while ($result->fetch()) {
        $ids[] = $result->activity_id;
      }
    }

    if (!empty($ids)) {
      $form->_componentClause = ' civicrm_activity.id IN ( ' . implode(',', $ids) . ' ) ';
      $form->assign('totalSelectedActivities', count($ids));
    }

    $form->_activityHolderIds = $form->_componentIds = $ids;

    //set the context for redirection for any task actions
    $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String', $form);
    $urlParams = 'force=1';
    if (CRM_Utils_Rule::qfKey($qfKey)) {
      $urlParams .= "&qfKey=$qfKey";
    }

    $session = CRM_Core_Session::singleton();
    $searchFormName = strtolower($form->get('searchFormName'));
    if ($searchFormName == 'search') {
      $session->replaceUserContext(CRM_Utils_System::url('civicrm/activity/search', $urlParams));
    }
    else {
      $session->replaceUserContext(CRM_Utils_System::url("civicrm/contact/search/$searchFormName",
          $urlParams
        ));
    }
  }

  /**
   * Given the membership id, compute the contact id
   * since its used for things like send email
   */
  public function setContactIDs() {
    $IDs = implode(',', $this->_activityHolderIds);
    $query = "
SELECT source_contact_id
  FROM civicrm_activity
 WHERE id IN ( $IDs )
";
    $dao = CRM_Core_DAO::executeQuery($query);
    while ($dao->fetch()) {
      $contactIDs[] = $dao->source_contact_id;
    }
    $this->_contactIds = $contactIDs;
  }

  /**
   * simple shell that derived classes can call to add buttons to
   * the form with a customized title for the main Submit
   *
   * @param string $title title of the main button
   * @param string $type  button type for the form after processing
   *
   * @return void
   * @access public
   */
  function addDefaultButtons($title, $nextType = 'next', $backType = 'back') {
    $this->addButtons(array(
        array(
          'type' => $nextType,
          'name' => $title,
          'isDefault' => TRUE,
        ),
        array(
          'type' => $backType,
          'name' => ts('Cancel'),
        ),
      )
    );
  }
}

