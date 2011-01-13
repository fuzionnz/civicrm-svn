<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Report/Form.php';

class CRM_Report_Form_Contact_LoggingDetail extends CRM_Report_Form
{
    private $loggingDB;

    private $contact_id;
    private $log_conn_id;
    private $log_date;

    function __construct()
    {
        $this->_add2groupSupported = false; // don’t display the ‘Add these Contacts to Group’ button

        $dsn = defined('CIVICRM_LOGGING_DSN') ? DB::parseDSN(CIVICRM_LOGGING_DSN) : DB::parseDSN(CIVICRM_DSN);
        $this->loggingDB = $dsn['database'];

        $this->log_conn_id = CRM_Utils_Request::retrieve('log_conn_id', 'Integer', CRM_Core_DAO::$_nullObject);
        $this->log_date    = CRM_Utils_Request::retrieve('log_date',    'String',  CRM_Core_DAO::$_nullObject);

        // make sure the report works even without the params
        if (!$this->log_conn_id or !$this->log_date) {
            $dao = new CRM_Core_DAO;
            $dao->query("SELECT log_conn_id, log_date FROM `{$this->loggingDB}`.log_civicrm_contact WHERE log_action = 'Update' ORDER BY log_date DESC LIMIT 1");
            $dao->fetch();
            $this->log_conn_id = $dao->log_conn_id;
            $this->log_date    = $dao->log_date;
        }

        $this->_columnHeaders = array(
            'field' => array('title' => ts('Field')),
            'from'  => array('title' => ts('Changed From')),
            'to'    => array('title' => ts('Changed To')),
        );

        parent::__construct();
    }

    function buildRows($sql, &$rows)
    {
        // safeguard for when there aren’t any log entries yet
        if (!$this->log_conn_id or !$this->log_date) return;

        $params = array(
            1 => array($this->log_conn_id, 'Integer'),
            2 => array($this->log_date,    'String'),
        );

        // let the template know who updated whom when
        $sql = "
            SELECT who.id who_id, who.display_name who_name, whom.id whom_id, whom.display_name whom_name, l.is_deleted
            FROM `{$this->loggingDB}`.log_civicrm_contact l
            JOIN civicrm_contact who ON (l.log_user_id = who.id)
            JOIN civicrm_contact whom ON (l.id = whom.id)
            WHERE log_action = 'Update' AND log_conn_id = %1 AND log_date = %2 ORDER BY log_date DESC LIMIT 1
        ";
        $dao =& CRM_Core_DAO::executeQuery($sql, $params);
        $dao->fetch();
        $this->assign('who_url',   CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$dao->who_id}"));
        $this->assign('whom_url',  CRM_Utils_System::url('civicrm/contact/view', "reset=1&cid={$dao->whom_id}"));
        $this->assign('who_name',  $dao->who_name);
        $this->assign('whom_name', $dao->whom_name);
        $this->assign('log_date',  $this->log_date);

        // track who’s changes being monitored
        $this->contact_id = $dao->whom_id;

        // link back to summary report
        require_once 'CRM/Report/Utils/Report.php';
        $this->assign('summaryReportURL', CRM_Report_Utils_Report::getNextUrl('logging/contact/summary', 'reset=1', false, true));

        $rows = $this->diffsInTable('log_civicrm_contact');

        // add custom data changes
        $dao =& CRM_Core_DAO::executeQuery("SHOW TABLES FROM `{$this->loggingDB}` LIKE 'log_civicrm_value_%'");
        while ($dao->fetch()) {
            $table = $dao->toValue("Tables_in_{$this->loggingDB}_(log_civicrm_value_%)");
            $rows  = array_merge($rows, $this->diffsInTable($table));
        }

        // add changes by fetching all ids affected in the ±10 s interval (for the given connection id)
        $tables = array('log_civicrm_email', 'log_civicrm_phone', 'log_civicrm_im', 'log_civicrm_address');
        foreach ($tables as $table) {
            $sql = "SELECT DISTINCT id FROM `{$this->loggingDB}`.`$table` WHERE log_conn_id = %1 AND log_date BETWEEN DATE_SUB(%2, INTERVAL 10 SECOND) AND DATE_ADD(%2, INTERVAL 10 SECOND)";
            $dao =& CRM_Core_DAO::executeQuery($sql, $params);
            while ($dao->fetch()) {
                $rows = array_merge($rows, $this->diffsInTable($table, $dao->id));
            }
        }
    }

    function buildQuery()
    {
    }

    private function diffsInTable($table, $id = null)
    {
        // caches for pretty field titles and value mappings
        static $titles = null;
        static $values = null;

        $params = array(
            1 => array($this->log_conn_id, 'Integer'),
            2 => array($this->log_date,    'String'),
        );

        // we look for the last change in the given connection that happended less than 10 seconds later than log_date to catch multi-query changes
        if ($id) {
            $params[3]  = array($id, 'Integer');
            $changedSQL = "SELECT * FROM `{$this->loggingDB}`.`$table` WHERE log_conn_id = %1 AND log_date < DATE_ADD(%2, INTERVAL 10 SECOND) AND id = %3 ORDER BY log_date DESC LIMIT 1";
        } else {
            $changedSQL = "SELECT * FROM `{$this->loggingDB}`.`$table` WHERE log_conn_id = %1 AND log_date < DATE_ADD(%2, INTERVAL 10 SECOND) ORDER BY log_date DESC LIMIT 1";
        }
        $changed = $this->sqlToArray($changedSQL, $params);

        // return early if nothing found
        if (empty($changed)) return array();

        // seed caches with civicrm_contact titles/values
        if (!isset($titles['log_civicrm_contact']) or !isset($values['log_civicrm_contact'])) {
            // FIXME: these should be populated with pseudo constants as they
            // were at the time of logging rather than their current values
            $values['log_civicrm_contact'] = array(
                'gender_id'                      => CRM_Core_PseudoConstant::gender(),
                'preferred_communication_method' => CRM_Core_PseudoConstant::pcm(),
                'preferred_language'             => CRM_Core_PseudoConstant::languages(),
                'prefix_id'                      => CRM_Core_PseudoConstant::individualPrefix(),
                'suffix_id'                      => CRM_Core_PseudoConstant::individualSuffix(),
            );

            require_once 'CRM/Contact/DAO/Contact.php';
            $dao = new CRM_Contact_DAO_Contact;
            foreach ($dao->fields() as $field) {
                $titles['log_civicrm_contact'][$field['name']] = $field['title'];
                if ($field['type'] == CRM_Utils_Type::T_BOOLEAN) {
                    $values['log_civicrm_contact'][$field['name']] = array('0' => ts('false'), '1' => ts('true'));
                }
            }
        }

        foreach (array('address', 'email', 'im', 'phone') as $type) {
            if (!isset($titles["log_civicrm_$type"]) or !isset($values["log_civicrm_$type"])) {
                // FIXME: these should be populated with pseudo constants as they
                // were at the time of logging rather than their current values
                $values["log_civicrm_$type"] = array(
                    'location_type_id' => CRM_Core_PseudoConstant::locationType(),
                );
                $class = $type == 'im' ? 'IM' : ucfirst($type);
                require_once "CRM/Core/DAO/$class.php";
                eval("\$dao = new CRM_Core_DAO_$class;");
                foreach ($dao->fields() as $field) {
                    $titles["log_civicrm_$type"][$field['name']] = $field['title'];
                    if ($field['type'] == CRM_Utils_Type::T_BOOLEAN) {
                        $values["log_civicrm_$type"][$field['name']] = array('0' => ts('false'), '1' => ts('true'));
                    }
                }
            }
        }

        // FIXME: call this only if we’re actually checking the relevant table
        $values['log_civicrm_address']['country_id']        = CRM_Core_PseudoConstant::country();
        $values['log_civicrm_address']['state_province_id'] = CRM_Core_PseudoConstant::stateProvince();
        $values['log_civicrm_im']['provider_id']            = CRM_Core_PseudoConstant::IMProvider();

        // add custom data titles/values for the given table
        if (substr($table, 0, 18) == 'log_civicrm_value_' and (!isset($titles[$table]) or !isset($values[$table]))) {
            $titles[$table] = array();
            $values[$table] = array();

            $params[3] = array(substr($table, 4), 'String');
            $sql = "SELECT id, title FROM `{$this->loggingDB}`.log_civicrm_custom_group WHERE log_date <= %2 AND table_name = %3 ORDER BY log_date DESC LIMIT 1";
            $cgDao =& CRM_Core_DAO::executeQuery($sql, $params);
            $cgDao->fetch();

            $params[3] = array($cgDao->id, 'Integer');
            $sql = "SELECT column_name, data_type, label, name FROM `{$this->loggingDB}`.log_civicrm_custom_field WHERE log_date <= %2 AND custom_group_id = %3 ORDER BY log_date";
            $cfDao =& CRM_Core_DAO::executeQuery($sql, $params);
            while ($cfDao->fetch()) {
                $titles[$table][$cfDao->column_name] = "{$cgDao->title}: {$cfDao->label}";
                switch ($cfDao->data_type) {
                case 'Boolean':
                    $values[$table][$cfDao->column_name] = array('0' => ts('false'), '1' => ts('true'));
                    break;
                case 'String':
                    $values[$table][$cfDao->column_name] = array();
                    $params[3] = array("custom_{$cfDao->name}", 'String');
                    $sql = "SELECT id FROM `{$this->loggingDB}`.log_civicrm_option_group WHERE log_date <= %2 AND name = %3 ORDER BY log_date DESC LIMIT 1";
                    $ogId = CRM_Core_DAO::singleValueQuery($sql, $params);

                    $params[3] = array($ogId, 'Integer');
                    $sql = "SELECT label, value FROM `{$this->loggingDB}`.log_civicrm_option_value WHERE log_date <= %2 AND option_group_id = %3 ORDER BY log_date";
                    $ovDao =& CRM_Core_DAO::executeQuery($sql, $params);
                    while ($ovDao->fetch()) {
                        $values[$table][$cfDao->column_name][$ovDao->value] = $ovDao->label;
                    }
                    break;
                }
            }
        }

        // we look for the previous state (different log_conn_id) of the found id
        $params[3]   = array($changed['id'], 'Integer');
        $originalSQL = "SELECT * FROM `{$this->loggingDB}`.`$table` WHERE log_conn_id != %1 AND log_date < %2 AND id = %3 ORDER BY log_date DESC LIMIT 1";
        $original    = $this->sqlToArray($originalSQL, $params);

        $rows = array();

        // populate $rows with only the differences between $changed and $original (skipping certain columns and NULL ↔ empty changes)
        // FIXME: explode preferred_communication_method on CRM_Core_DAO::VALUE_SEPARATOR and handle properly somehow
        $skipped = array('contact_id', 'entity_id', 'id', 'log_action', 'log_conn_id', 'log_date', 'log_user_id');
        foreach (array_keys(array_diff_assoc($changed, $original)) as $diff) {
            if (in_array($diff, $skipped))                              continue;
            if ($original[$diff] == $changed[$diff])                    continue;
            if ($original[$diff] == false and $changed[$diff] == false) continue; // only in PHP: '0' == false and null == false but '0' != null
            $field = isset($titles[$table][$diff]) ? $titles[$table][$diff] : substr($table, 4) . ".$diff";
            if ($id) $field .= " (id: $id)";
            $rows[] = array(
                'field' => $field,
                'from'  => isset($values[$table][$diff][$original[$diff]]) ? $values[$table][$diff][$original[$diff]] : $original[$diff],
                'to'    => isset($values[$table][$diff][$changed[$diff]])  ? $values[$table][$diff][$changed[$diff]]  : $changed[$diff],
            );
        }

        return $rows;
    }

    private function sqlToArray($sql, $params)
    {
        $dao =& CRM_Core_DAO::executeQuery($sql, $params);
        $dao->fetch();
        return $dao->toArray();
    }
}
