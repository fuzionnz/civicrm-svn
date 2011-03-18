<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
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
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

/**
 * Files required
 */
require_once 'CRM/Core/Form.php';
require_once 'CRM/Campaign/BAO/Campaign.php';

class CRM_Campaign_Form_Search_Campaign extends CRM_Core_Form 
{
    /** 
     * Are we forced to run a search 
     * 
     * @var int 
     * @access protected 
     */ 
    protected $_force; 
    
    /** 
     * processing needed for buildForm and later 
     * 
     * @return void 
     * @access public 
     */ 
    function preProcess( ) 
    {
        $this->_search    = CRM_Utils_Array::value( 'search', $_GET );
        $this->_force     = CRM_Utils_Request::retrieve( 'force', 'Boolean', $this, false,  false );
        $this->_searchTab = CRM_Utils_Request::retrieve( 'type',  'String',  $this, false, 'campaign' );
        
        //when we do load tab, lets load the default objects.
        $this->assign( 'force',          ($this->_force||$this->_searchTab) ? true : false );
        $this->assign( 'searchParams',   json_encode( $this->get( 'searchParams' ) ) );
        $this->assign( 'buildSelector',  $this->_search );
        $this->assign( 'searchFor',      $this->_searchTab );
        
        //set the form title.
        CRM_Utils_System::setTitle( ts( 'Find Campaigns' ) );
    }
    
    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) 
    {
        if ( $this->_search ) return;
        
        $this->add( 'text', 'title', ts( 'Title' ) );
        
        //build the array of all search params.
        $this->_searchParams = array( );
        foreach  ( $this->_elements as $element ) {
            $name = $element->_attributes['name'];
            if ( $name == 'qfKey' ) continue;
            $this->_searchParams[$name] = $name;
        }
        $this->set( 'searchParams',    $this->_searchParams );
        $this->assign( 'searchParams', json_encode( $this->_searchParams ) );
    }
    
}
