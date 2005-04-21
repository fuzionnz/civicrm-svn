<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Pager.php';
require_once 'CRM/Core/Sort.php';
require_once 'CRM/Selector/Base.php';
require_once 'CRM/Selector/API.php';
require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Contact_Selector extends CRM_Selector_Base implements CRM_Selector_API 
{

    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     */
    static $_links = array(
                           CRM_Action::VIEW => array(
                                                     'name'     => 'View Contact',
                                                     'link'     => '/crm/contact?action=view&id=%%id%%',
                                                     'linkName' => 'View Contact',
                                                     'menuName' => 'View Contact Details'
                                                     ),
                           CRM_Action::EDIT => array(
                                                     'name'     => 'Edit Contact',
                                                     'link'     => '/crm/contact?action=edit&id=%%id%%',
                                                     'linkName' => 'Edit Contact',
                                                     'menuName' => 'Edit Contact Details'
                                                     ),
                           );

    static $_columnHeaders = array(
                                   array('name' => ''),
                                   array('name' => ''),
                                   array(
                                         'name'      => 'Name',
                                             'sort'      => 'sort_name',
                                             'direction' => CRM_Sort::ASCENDING,
                                             ),
                                       array('name' => 'Address'),
                                       array(
                                             'name'      => 'City',
                                             'sort'      => 'city',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'State',
                                             'sort'      => 'state',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Postal',
                                             'sort'      => 'postal_code',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Country',
                                             'sort'      => 'country',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array(
                                             'name'      => 'Email',
                                             'sort'      => 'email',
                                             'direction' => CRM_Sort::DONTCARE,
                                             ),
                                       array('name' => 'Phone'),
                                       array('name' => ''),
                                       );
    


    /**
     * This caches the content for the display system.
     *
     * @var string
     * @access protected
     */
    protected $_contact;

    /**
     * formValues is the array returned by exportValues called on
     * the HTML_QuickForm_Controller for that page.
     *
     * @var array
     * @access protected
     */
    protected $_formValues;

    /**
     * represent the type of selector
     *
     * @var int
     * @access protected
     */
    protected $_type;


    /**
     * Class constructor
     *
     * @param array $formValues array of parameters for query
     * @param int   $type - type of search basic or advanced.
     *
     * @return CRM_Contact_AdvancedSelector
     * @access public
     */
    function __construct(&$formValues, $type=CRM_Form::MODE_NONE) 
    {
        
        //CRM_Error::le_method();

        //object of BAO_Contact_Individual for fetching the records from db
        $this->_contact = new CRM_Contact_BAO_Contact();

        // submitted form values
        $this->_formValues = $formValues;

        // type of selector
        $this->_type = $type;

        //CRM_Error::debug_var('formValues', $this->_formValues);

        //CRM_Error::ll_method();

    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @param none
     *
     * @return array
     * @access public
     *
     */
    function &getLinks() 
    {
        return CRM_Contact_Selector::$_links;
    } //end of function

    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = "Contact %%StatusMessage%%";
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function

    /**
     * getter for headers for each column of the displayed form.
     *
     * @param 
     * @return array (reference)
     * @access public
     */
    function &getColumnHeaders($action) 
    {
        return self::$_columnHeaders;
    }


    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        switch ($this->_type) {
        case CRM_Form::MODE_BASIC:
            $v1 = $this->_contact->basicSearchQuery($this->_formValues, $offset, $rowCount, $sort, TRUE);
            break;
        case CRM_Form::MODE_ADVANCED:
            $v1 = $this->_contact->advancedSearchQuery($this->_formValues, $offset, $rowCount, $sort, TRUE);
            break;
        }
        $v2 = $v1->getDatabaseResult();
        $v3 = $v2->fetchRow();
        $count = $v3[0];
        return $count;
    }


    /**
     * getter for all the database values to be displayed on the form while listing
     *
     * @param int      $action   the type of action links
     * @param int      $offset   the offset for the query
     * @param int      $rowCount the number of rows to return
     * @param CRM_Sort $sort     the sort object
     *
     * @return array (reference)
     * @access public
     */
    function &getRows($action, $offset, $rowCount, $sort)
    {
        $config = CRM_Config::singleton( );

        // note the formvalues were given by CRM_Contact_Form_Search to us 
        // and contain the search criteria (parameters)
        switch ($this->_type) {
        case CRM_Form::MODE_BASIC:
            $result = $this->_contact->basicSearchQuery($this->_formValues, $offset, $rowCount, $sort);
            break;
        case CRM_Form::MODE_ADVANCED:
            $result = $this->_contact->advancedSearchQuery($this->_formValues, $offset, $rowCount, $sort);
            break;
        }

        // process the result of the query
        $rows = array( );

        while ($result->fetch()) {
            $row = array();

            // the columns we are interested in
            static $properties = array( 'contact_id', 'sort_name', 'street_address',
                                        'city', 'state', 'country', 'postal_code',
                                        'email', 'phone' );
            foreach ($properties as $property) {
                $row[$property] = $result->$property;
            }
            $row['edit'] = CRM_System::url( 'civicrm/contact/edit', 'reset=1&cid=' . $result->contact_id );
            $row['view'] = CRM_System::url( 'civicrm/contact/view', 'reset=1&cid=' . $result->contact_id );
            $contact_type = '<img src="' . $config->resourceBase . 'i/contact_';
            switch ($result->contact_type) {
            case 'Individual' :
                $contact_type .= 'ind.png" alt="Individual">';
                break;
            case 'Household' :
                $contact_type .= 'house.png" alt="Household" height="16" width="16">';
                break;
            case 'Organization' :
                $contact_type .= 'org.gif" alt="Organization" height="16" width="18">';
                break;

            }
            $row['contact_type'] = $contact_type;
            $rows[] = $row;
        }
        return $rows;
    }
    
    
    /**
     * Given the current formValues, gets the query in local
     * language
     *
     * @param array reference $formValues submitted formValues
     * @param int $type the type of form
     *
     * @return string string representing the query in local language
     * @access public
     */
  
    function getQILL($formValues=null, $type=null)
    {
        CRM_Error::le_method();
        CRM_Error::debug_var('formValues', $this->_formValues);

        // query in local language
        $qill = "";
        $dontCare = " <i>dont care</i>";

        // regex patterns
        $patternOr = "/(.*) or$/";
        $patternAnd = "/(.*) and$/";
        $replacement = "$1";

        switch ($this->_type) {
        case CRM_Form::MODE_BASIC:
            if ($this->_formValues['contact_type'] != 'any') {
                $qill .= " " . $this->_formValues['contact_type'] . "s";
            } else {
                $qill .= " contacts";
            }

            // check for group restriction
            if ($this->_formValues['group'] != 'any') {
                // BUG.... 
                $qill .= " belonging to the group \"" . CRM_PseudoConstant::$group[$this->_formValues['group']] . "\" and";
            }
            
            // check for category restriction
            if ($this->_formValues['category'] != 'any') {
                $qill .= " categorized as \"" . CRM_PseudoConstant::$category[$this->_formValues['category']] . "\" and";
            }
            
            // check for last name, as of now only working with sort name
            if ($this->_formValues['sort_name']) {
                $andArray['sort_name'] = " LOWER(crm_contact.sort_name) LIKE '%". strtolower(addslashes($this->_formValues['sort_name'])) ."%'";
                $qill .= "whose name is like \"" . $this->_formValues['sort_name'] . "\" and";
            }
            $qill = preg_replace($patternAnd, $replacement, $qill);
            break;

        case CRM_Form::MODE_ADVANCED:
            // check for contact type restriction
            $qill .= "<ul>";

            // contact type
            $qill .= "<li>Contact Type -";
            if ($this->_formValues['cb_contact_type']) {
                foreach ($this->_formValues['cb_contact_type']  as $k => $v) {
                    $qill .= " {$k}s or";
                }            
                $qill = preg_replace($patternOr, $replacement, $qill);
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            
            // check for group restriction
            $qill .= "<li>Belonging to Group -";
            if ($this->_formValues['cb_group']) {
                foreach ($this->_formValues['cb_group']  as $k => $v) {
                    CRM_Error::debug_var('k', $k);
                    CRM_Error::debug_var("CRM_PseudoConstant_group", CRM_PseudoConstant::$group);
                    $qill .= " \"" . CRM_PseudoConstant::$group[$k] . "\" or";
                }
                $qill = preg_replace($patternOr, $replacement, $qill);
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";
                

            // check for category restriction
            $qill .= "<li>Categorized as -";
            if ($this->_formValues['cb_category']) {
                foreach ($this->_formValues['cb_category'] as $k => $v) {
                    $qill .= " \"" . CRM_PseudoConstant::$category[$k] . "\" or";
                }
                $qill = preg_replace($patternOr, $replacement, $qill);
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            // check for last name, as of now only working with sort name
            $qill .= "<li>Name like -";
            if ($this->_formValues['sort_name']) {
                $qill .= " \"" . $this->_formValues['sort_name'] . "\"";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            // street_name
            $qill .= "<li>Street Name like -";
            if ($this->_formValues['street_name']) {
                $qill .= " \"" . $this->_formValues['street_name'] . "\"";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            // city_name
            $qill .= "<li>City Name like -";
            if ($this->_formValues['city']) {
                $qill .= " \"" . $this->_formValues['city'] . "\"";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            // state
            $qill .= "<li>State -";
            if ($this->_formValues['state_province']) {
                $qill .= " \"" . CRM_PseudoConstant::$stateProvince[$this->_formValues['state_province']] . "\"";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";


            // country
            $qill .= "<li>Country -";
            if ($this->_formValues['country']) {
                $qill .= " \"" . CRM_PseudoConstant::$country[$this->_formValues['country']] . "\"";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";


            // postal code processing
            $qill .= "<li>Postal code -";
            if ($this->_formValues['postal_code'] || $this->_formValues['postal_code_low'] || $this->_formValues['postal_code_high']) {
                // postal code = value
                if ($this->_formValues['postal_code']) {
                    $qill .= " \"" . $this->_formValues['postal_code'] . "\" or";
                }
                
                // postal code between 2 values
                if ($this->_formValues['postal_code_low'] && $this->_formValues['postal_code_high']) {
                    $qill .= " between \"" . $this->_formValues['postal_code_low'] . "\" and \"" . $this->_formValues['postal_code_high'] . "\"";
                } elseif ($this->_formValues['postal_code_low']) {
                    $qill .= " greater than \"" . $this->_formValues['postal_code_low'] . "\"";
                } elseif ($this->_formValues['postal_code_high']) {
                    $qill .= " less than \"" . $this->_formValues['postal_code_high'] . "\"";
                }            
                // remove the trailing "or"
                $qill = preg_replace($patternOr, $replacement, $qill);
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";

            // location type processing
            $qill .= "<li>Location type -";
            if ($this->_formValues['cb_location_type']) {
                if (!$this->_formValues['cb_location_type']['any']) {
                    foreach ($this->_formValues['cb_location_type']  as $k => $v) {
                        $qill .= " " . CRM_PseudoConstant::$locationType[$k] . " or";
                    }
                    $qill = preg_replace($patternOr, $replacement, $qill);
                } else {
                    $qill .= " any";
                }
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";
        
            // primary location processing
            $qill .= "<li>Primary Location only ? -";
            if ($this->_formValues['cb_primary_location']) {
                $andArray['cb_primary_location'] = "crm_location.is_primary = 1";
                $qill .= " Yes";
            } else {
                $qill .= $dontCare;
            }
            $qill .= "</li>";
            break;
        }

        $qill .= "</ul>";

        //CRM_Error::debug_var('qill', $qill);
        //CRM_Error::ll_method();

        if($qill != "all") {
            return $qill;
        } else {
            return "";
        }
    }

    function getExportColumnHeaders($action, $type = 'csv')
    {
    }

    function getExportRows($action, $type = 'csv')
    {
    }

    function getExportFileName($action, $type = 'csv')
    {
    }


}//end of class

?>