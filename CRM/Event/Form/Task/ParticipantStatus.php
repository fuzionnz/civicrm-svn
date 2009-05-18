<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
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
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Event/Form/Task/Batch.php';

class CRM_Event_Form_Task_ParticipantStatus extends CRM_Event_Form_Task_Batch
{
    function buildQuickForm()
    {
        // CRM_Event_Form_Task_Batch::buildQuickForm() gets ufGroupId 
        // from the form, so set it here to the id of the reserved profile
        require_once 'CRM/Core/DAO/UFGroup.php';
        $dao = new CRM_Core_DAO_UFGroup;
        $dao->name = 'participant_status';
        $dao->find(true);
        $this->set('ufGroupId', $dao->id);

        require_once 'CRM/Event/PseudoConstant.php';
        $statuses =& CRM_Event_PseudoConstant::participantStatus();
        $changeText = ts('Change all participant statuses to:');
        $changeLinks = array();
        foreach ($statuses as $statusId => $statusName) {
            $changeLinks[] = "<a href='#' onClick='setStatusesTo($statusId); return false;'>$statusName</a>";
        }
        $changeText .= ' ' . implode(', ', $changeLinks) . '.';
        $this->assign('statusChangeText', $changeText);

        parent::buildQuickForm();
    }
}
