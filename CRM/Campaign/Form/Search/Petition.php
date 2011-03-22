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
require_once 'CRM/Campaign/BAO/Survey.php';
require_once 'CRM/Campaign/BAO/Petition.php';
require_once 'CRM/Campaign/BAO/Campaign.php';

class CRM_Campaign_Form_Search_Petition extends CRM_Core_Form 
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
        $this->_searchTab = CRM_Utils_Request::retrieve( 'type',  'String',  $this, false, 'survey' );
        
        //when we do load tab, lets load the default objects.
        $this->assign( 'force',             ($this->_force||$this->_searchTab) ? true : false );
        $this->assign( 'searchParams',      json_encode( $this->get( 'searchParams' ) ) );
        $this->assign( 'buildSelector',     $this->_search );
        $this->assign( 'searchFor',         $this->_searchTab );
        $this->assign( 'petitionTypes',     json_encode( $this->get( 'petitionTypes' ) ) );
        $this->assign( 'petitionCampaigns', json_encode( $this->get( 'petitionCampaigns' ) ) );
        
        //set the form title.
        CRM_Utils_System::setTitle( ts( 'Find Petition' ) );
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
        
        $attributes = CRM_Core_DAO::getAttribute('CRM_Campaign_DAO_Survey');
        $this->add( 'text', 'title', ts( 'Title' ), $attributes['title'] );
        
        //activity Type id
        $petitionTypes = CRM_Campaign_BAO_Survey::getSurveyActivityType( );
        $this->add( 'select', 'activity_type_id', 
                    ts('Activity Type'), array( '' => ts('- select -') ) + $petitionTypes );
        $this->set( 'petitionTypes', $petitionTypes );
        $this->assign( 'surveyTypes', json_encode( $petitionTypes ) );
        
        //campaigns
        require_once 'CRM/Campaign/BAO/Campaign.php';
        $campaigns = CRM_Campaign_BAO_Campaign::getCampaigns( null, null, false, false, false, true );
        $this->add('select', 'campaign_id', ts('Campaign'), array( '' => ts('- select -') ) + $campaigns );
        $this->set( 'petitionCampaigns', $campaigns );
        $this->assign( 'petitionCampaigns', json_encode( $campaigns ) );
        
        //build the array of all search params.
        $this->_searchParams = array( );
        foreach  ( $this->_elements as $element ) {
            $name  = $element->_attributes['name'];
            $label = $element->_label;
            if ( $name == 'qfKey' ) continue;
            $this->_searchParams[$name] = ($label)?$label:$name;
        }
        $this->set( 'searchParams',    $this->_searchParams );
        $this->assign( 'searchParams', json_encode( $this->_searchParams ) );
    }
    
}
