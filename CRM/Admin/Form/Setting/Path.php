<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for File System Path
 * 
 */
class CRM_Admin_Form_Setting_Path extends CRM_Admin_Form_Setting
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        CRM_Utils_System::setTitle(ts('Settings - Upload Directories'));

        $directories = array( 'uploadDir'           => ts( 'Temporary Files'  ),
                              'imageUploadDir'      => ts( 'Images'           ),
                              'customFileUploadDir' => ts( 'Custom Files'     ),
                              'customTemplateDir'   => ts( 'Custom Templates' ),
                              'customPHPPathDir'    => ts( 'Custom PHP Path Directory' )  );
        foreach ( $directories as $name => $title ) {
            $this->add('text', $name, $title );
            $this->addRule( $name,
                            ts( "'%1' directory does not exist",
                                array( 1 => $title ) ),
                            'fileExists' );
        }
        
        parent::buildQuickForm();
    }

    public function postProcess( ) {
        parent::postProcess( );

        parent::rebuildMenu( );
    }

}


