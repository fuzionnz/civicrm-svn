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

require_once 'CRM/Admin/Form.php';

/**
 * This class generates form components for DedupeRules
 * 
 */
class CRM_Admin_Form_DedupeFind extends CRM_Admin_Form
{

    /**
     * Function to pre processing
     *
     * @return None
     * @access public
     */
    function preProcess()
    {
        $this->rgid   = CRM_Utils_Request::retrieve('rgid', 'Positive', $this, false, 0);
    }

    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm()
    {

        $groupList = CRM_Core_PseudoConstant::group( );
        $groupList[''] = ts('- All Contacts -') ;
        asort($groupList);
        
        $this->add('select'  , 'group_id', ts('Select Group'), $groupList );
        
        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Continue'),
                                         'isDefault' => true   ),
                                 )
                           );
    }
    
    function setDefaultValues()
    {
        return $this->_defaults;
    }

    /**
     * Function to process the form
     *
     * @access public
     * @return None
     */
    public function postProcess() 
    {
        $values = $this->exportValues();

        if ( $values['group_id'] ) {
            $url = CRM_Utils_System::url( 'civicrm/admin/dedupefind', "reset=1&action=update&rgid={$this->rgid}&gid={$values['group_id']}" );
        } else {
            $url = CRM_Utils_System::url( 'civicrm/admin/dedupefind', "reset=1&action=update&rgid={$this->rgid}" );
        }
        
        CRM_Utils_System::redirect($url);
    }
    
}


