<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.7                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org. If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class generates form components for processing Event  
 * 
 */
class CRM_Event_Form_ManageEvent extends CRM_Core_Form
{

    /**
     * the id of the event we are proceessing
     *
     * @var int
     * @protected
     */
    protected $_id;
    
    /** 
     * Function to set variables up before form is built 
     *                                                           
     * @return void 
     * @access public 
     */ 
    function preProcess( ) {
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, false);
        
        if ( $this->_action & CRM_Core_Action::UPDATE ) {
            $this->_id = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );
            $this->set( 'eventID', $this->_id );
        } elseif( $this->_action & CRM_Core_Action::MAP ) {
            $this->_id = $this->get( 'eid' );
            $this->set( 'eventID', CRM_Utils_Request::retrieve( 'id', 'Positive', $this ));
        } else {
            $this->_id = $this->get( 'eid' );
        }
    }
    
    /**
     * This function sets the default values for the form. For edit/view mode
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    function setDefaultValues( )
    {
        $eventID = $this->get('eventID');
        $defaults = array( );
        if ( isset( $eventID ) ) {
            $params = array( 'id' => $eventID );
            require_once 'CRM/Event/BAO/Event.php';
            CRM_Event_BAO_Event::retrieve($params, $defaults);
            
        } else {
            $defaults['is_active'] = 1;
            $defaults['style']     = 'Inline';
        }
        
        if ($this->_action & ( CRM_Core_Action::VIEW | CRM_Core_Action::BROWSE ) ) {
            $inactiveNeeded = true;
            $viewMode = true;
        } else {
            $viewMode = false;
            $inactiveNeeded = false;
        }
        
        $subType = CRM_Utils_Request::retrieve( 'subType', 'Positive', $this );
        if ( $subType ) {
            $defaults["event_type_id"] = $subType;
        }
        return $defaults;
    }

    /** 
     * Function to build the form 
     * 
     * @return None 
     * @access public 
     */ 
    public function buildQuickForm( )  
    { 
        $this->addButtons(array(
                                array ( 'type'      => 'next',
                                        'name'      => ts('Save'),
                                        'spacing'   => '&nbsp;&nbsp;&nbsp;&nbsp;',
                                        'isDefault' => true   ),
                                array ( 'type'      => 'cancel',
                                        'name'      => ts('Cancel') ),
                                )
                          );

    }
}
?>
