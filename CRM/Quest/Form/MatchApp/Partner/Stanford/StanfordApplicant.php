<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
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
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Columbia Applicant
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for the Stanford applicant
 * 
 */
class CRM_Quest_Form_MatchApp_Partner_Stanford_StanfordApplicant extends CRM_Quest_Form_App
{
    
    protected $_fields;

    /**
     * Function to set variables up before form is built
     *
     * @return void
     * @access public
     */
    public function preProcess()
    {
        parent::preProcess();
         
    }
    
    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $this->_locationIds = array();
        require_once 'CRM/Quest/Partner/DAO/Stanford.php';
        $dao  = &new CRM_Quest_Partner_DAO_Stanford();
        $dao->contact_id = $this->_contactID;
        if ( $dao->find( true ) ) {
            CRM_Core_DAO::storeValues( $dao , $defaults);
            $locParams = array('entity_id' => $dao->id, 'entity_table' => 'quest_stanford');
            CRM_Core_BAO_Location::getValues( $locParams, $defaults, $ids, 3);
            $this->_locationIds = $ids;     
        }
        
        return $defaults;
    }

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_Partner_DAO_Stanford');
        $this->buildAddressBlock( 1, ts( 'Location' ), null, null, null, null, null, "location" );
       

        $this->addYesNo( 'is_enrolled_full_time', ts( 'Have you been enrolled full-time in college/university (other than summer session)?' ) ,null,true);
        for ($i=1; $i<=3; $i++ ) {
            $this->addElement('select' ,'area_of_major_'.$i, ts("Please select possible area of major, in order of preference") , array("- Select -") + CRM_Core_OptionGroup::values('stanford_area_of_major_id') );
        }
        
        $this->addYesNo( 'is_parent_employed', ts( 'Is either parent or step-parent currently employed by Stanford University?' ) ,null,false);
        $this->addYesNo( 'is_sibling_applying', ts( 'Are any siblings or step-siblings applying to the undergraduate program at Stanford this year? ' ) ,null,true);
       
        require_once 'CRM/Quest/DAO/Person.php';
        $siblings = array();
        $dao = & new CRM_Quest_DAO_Person();
        $dao->contact_id = $cid;
        $dao->is_sibling = true;
        $dao->find();
        while ( $dao->fetch() ) {
            $siblings[$dao->id] =  $dao->first_name . " " . $dao->last_name ; 
        }
        $count = count($siblings) ;
        $this->assign("totalSibligs" , $count);
        for ( $i=1; $i<=$count; $i++ ) {
            $this->addElement('select' ,'sibling_id_'.$i , null, array("- Select -") + $siblings  );
            $choice = array( );
            $choice[] = $this->createElement( 'radio', null, '11', ts( 'Freshman' ), '1', null );
            $choice[] = $this->createElement( 'radio', null, '11', ts( 'Transfer' ) , '0', null );
            $this->addGroup( $choice, "sibling_application_status_".$i, null );
        }

        parent::buildQuickForm( );
                
    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
         return ts('Applicant Information');
    }

    /** 
     * process the form after the input has been submitted and validated 
     * 
     * @access public 
     * @return void 
     */ 
    public function postProcess() {
        if ( $this->_action &  CRM_Core_Action::VIEW ) {
            return;
        }
        
        $params = $this->controller->exportValues( $this->_name );
        require_once 'CRM/Quest/Partner/DAO/Stanford.php';
        $dao  = &new CRM_Quest_Partner_DAO_Stanford();
        $dao->contact_id = $this->_contactID;
        $dao->find( true );
            
        $dao->copyValues($params);
        $dao->save( );
               
        $params['entity_id'] = $dao->id;
        $params['entity_table'] = 'quest_stanford';
        $params['location']['1']['location_type_id'] = 1;
        
        require_once 'CRM/Core/BAO/Location.php';
        CRM_Core_BAO_Location::add($params, $this->_locationIds, 1);
        
        parent::postProcess( );
        
    }

}

?>