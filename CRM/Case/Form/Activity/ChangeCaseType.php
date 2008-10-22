<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
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
 * This class generates form components for OpenCase Activity
 * 
 */
class CRM_Case_Form_Activity_ChangeCaseType
{

    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     * 
     * @access public
     * @return None
     */
    function setDefaultValues( &$form ) 
    {
        $defaults = array( );
        $today_date = getDate();
        $defaults['is_reset_timeline'] = 1;
        $defaults['start_date']['M']             = $today_date['mon'];
        $defaults['start_date']['d']             = $today_date['mday'];
        $defaults['start_date']['Y']             = $today_date['year'];

        return $defaults;
    }

    static function buildQuickForm( &$form ) 
    { 
        require_once 'CRM/Core/OptionGroup.php';        
        $caseType = CRM_Core_OptionGroup::values('case_type');
        $form->add('select', 'case_type_id',  ts( 'New Case Type' ),  
                   $caseType , true);

        // case selector
        $form->assign( 'dojoIncludes', "dojo.require('dojox.data.QueryReadStore'); dojo.require('dojo.parser');" );
        $caseAttributes = array( 'dojoType'       => 'civicrm.FilteringSelect',
                                 'mode'           => 'remote',
                                 'store'          => 'caseStore');
        $caseUrl = CRM_Utils_System::url( "civicrm/ajax/caseSubject",
                                          "c={$form->_clientId}",
                                          false, null, false );
        $form->assign( 'caseUrl', $caseUrl );
        $form->add( 'text','case_id', ts('Case'), $caseAttributes, true );
        
        // timeline
        $form->addYesNo( 'is_reset_timeline', ts( 'Reset Case Timeline?' ),null, true, array('onclick' =>"return showHideByValue('is_reset_timeline','','resetTimeline','table-row','radio',false);") );
        $form->add( 'date', 'start_date', ts('Case Timeline'),
                    CRM_Core_SelectValues::date('activityDate' ), false );   
        $form->addRule('start_date', ts('Select a valid date.'), 'qfDate');
    }

    /**
     * global validation rules for the form
     *
     * @param array $values posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$values, $files, &$form ) 
    {
        return true;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function beginPostProcess( &$form, &$params ) 
    {
        $params['id'] = $params['case_id'];

        if ( CRM_Utils_Array::value('is_reset_timeline', $params ) == 0 ) {
            unset($params['start_date']);
        }
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function endPostProcess( &$form, &$params ) 
    {
        // status msg
        $params['statusMsg'] = ts('Case Type changed successfully.');
    }
}
