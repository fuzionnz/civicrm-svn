<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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

require_once "CRM/Core/Form.php";
/**
 * This class generates form components for case report
 * 
 */
class CRM_Case_Form_ActivityView extends CRM_Core_Form
{
    /**
     * Function to process the view
     *
     * @access public
     * @return None
     */
    public function preProcess() 
    {
        $contactID  = CRM_Utils_Request::retrieve( 'cid', 'Integer', CRM_Core_DAO::$_nullObject );
        $activityID = CRM_Utils_Request::retrieve( 'aid', 'Integer', CRM_Core_DAO::$_nullObject );
       
        require_once 'CRM/Case/XMLProcessor/Report.php';
        $xmlProcessor = new CRM_Case_XMLProcessor_Report( );
        $report = $xmlProcessor->getActivityInfo( $contactID, $activityID );
        $this->assign('report', $report );
        require_once "CRM/Activity/BAO/Activity.php";
        $parentId =  CRM_Activity_BAO_Activity::getParentActivity( $activityID );
        if ( $parentId ) { 
            $parentURL = CRM_Utils_System::url( 'civicrm/case/activity/view','reset=1&aid={$activityID}', false, null, false );
            $this->assign( 'parentURL', $parentURL );
        }
    }
}
