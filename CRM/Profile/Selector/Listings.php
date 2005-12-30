<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.3                                                |
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
 *
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id: Selector.php 2609 2005-08-17 00:16:37Z lobo $
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Contact.php';


/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Profile_Selector_Listings extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * array of supported links, currenly view and edit
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    static $_columnHeaders;

    /**
     * The sql params we use to get the list of contacts
     *
     * @var string
     * @access protected
     */
    protected $_params;

    /**
     * the public visible fields to be shown to the user
     *
     * @var array
     * @access protected
     */
    protected $_fields;

    /** 
     * the custom fields for this domain
     * 
     * @var array 
     * @access protected 
     */ 
    protected $_customFields;

    /**
     * cache the query object
     *
     * @var object
     * @access protected
     */
    protected $_query;

    /**
     * cache the expanded options list if any
     *
     * @var object 
     * @access protected 
     */ 
    protected $_options;

    /** 
     * The group id that we are editing
     * 
     * @var int 
     */ 
    protected $_gid; 


    /**
     * Class constructor
     *
     * @param string params the params for the where clause
     *
     * @return CRM_Contact_Selector_Profile
     * @access public
     */
    function __construct( &$params, &$customFields, $ufGroupId = null )
    {
        $this->_params = $params;
        
        $this->_gid = $ufGroupId;
        $this->_fields = CRM_Core_BAO_UFGroup::getListingFields( CRM_Core_Action::VIEW,
                                                                 CRM_Core_BAO_UFGroup::PUBLIC_VISIBILITY |
                                                                 CRM_Core_BAO_UFGroup::LISTINGS_VISIBILITY,
                                                                 false, $this->_gid );
        // CRM_Core_Error::debug( 'p', $this->_params );
        // CRM_Core_Error::debug( 'f', $this->_fields );

        $this->_customFields =& $customFields;

        $returnProperties =& CRM_Contact_BAO_Contact::makeHierReturnProperties( $this->_fields );
        $returnProperties['contact_type'] = 1;

        $this->_query   =& new CRM_Contact_BAO_Query( $this->_params, $returnProperties, $this->_fields );
        $this->_options =& $this->_query->_options;
        // CRM_Core_Error::debug( 'q', $this->_query );
    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     *
     * @return array
     * @access public
     *
     */
    static function &links()
    {
        if ( ! self::$_links ) {
            self::$_links = array( 
                                  CRM_Core_Action::VIEW   => array(
                                                                   'name'  => ts('Details'),
                                                                   'url'   => 'civicrm/profile/view',
                                                                   'qs'    => 'reset=1&cid=%%id%%&gid=%%gid%%',
                                                                   'title' => ts('View Profile Details'),
                                                                   ),
                                  ); 
        }
        return self::$_links;
    } //end of function

    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = ts('Contact %%StatusMessage%%');
        $params['csvString']    = null;
        $params['rowCount']     = CRM_Utils_Pager::ROWCOUNT;

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    }//end of function


    /**
     * returns the column headers as an array of tuples:
     * (name, sortName (key to the sort array))
     *
     * @param string $action the action being performed
     * @param enum   $output what should the result set include (web/email/csv)
     *
     * @return array the column headers that need to be displayed
     * @access public
     */
    function &getColumnHeaders($action = null, $output = null) 
    {
        static $skipFields = array( 'group', 'tag' );
        $direction = CRM_Utils_Sort::ASCENDING;
        $empty = true;
        if ( ! isset( self::$_columnHeaders ) ) {
            self::$_columnHeaders = array( array( 'name' => '' ) );
            foreach ( $this->_fields as $name => $field ) { 
                if ( $field['in_selector'] &&
                     ! in_array( $name, $skipFields ) ) {
                    self::$_columnHeaders[] = array( 'name'     => $field['title'],
                                                     'sort'     => $name,
                                                     'direction' => $direction );
                    $direction = CRM_Utils_Sort::DONTCARE;
                    $empty = false;
                }
            }

            // if we dont have any valid columns, dont add the implicit ones
            // this allows the template to check on emptiness of column headers
            if ( $empty ) {
                self::$_columnHeaders = array( );
            } else {
                self::$_columnHeaders[] = array('desc' => ts('Actions'));
            }
        }
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
        return $this->_query->searchQuery( 0, 0, null, true );
    }

    /**
     * Return the qill for this selector
     *
     * @return string
     * @access public
     */
    function getQill( ) {
        return $this->_query->qill( );
    }

    /**
     * returns all the rows in the given offset and rowCount
     *
     * @param enum   $action   the action being performed
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     * @param string $sort     the sql string that describes the sort order
     * @param enum   $output   what should the result set include (web/email/csv)
     *
     * @return int   the total number of rows for this action
     */
    function &getRows($action, $offset, $rowCount, $sort, $output = null) {
        
        //$sort object processing for location fields
        if( $sort ) {
            foreach ($sort->_vars as $key => $field) {
                $fieldArray = explode('-' , $field['name']);
                if( is_numeric($fieldArray[1]) ) {
                    $locationType = & new CRM_Core_DAO_LocationType();
                    $locationType->id = $fieldArray[1];
                    $locationType->find(true);
                    if($fieldArray[0]!='email') {
                        $field['name'] = "`".$locationType->name."-".$fieldArray[0]."`";
                    } else if ($fieldArray[0] == 'email') {
                        $field['name'] = "`".$locationType->name."-".$fieldArray[0]."-1`";
                    }
                }
            }
        }
     
        $result = $this->_query->searchQuery( $offset, $rowCount, $sort );

        // process the result of the query
        $rows = array( );

        $mask = CRM_Core_Action::mask( CRM_Core_Permission::getPermission( ) );

        require_once 'CRM/Core/PseudoConstant.php';
        $locationTypes = CRM_Core_PseudoConstant::locationType( );

        $links =& self::links( );
        $names = array( );
        static $skipFields = array( 'group', 'tag' ); 
        foreach ( $this->_fields as $key => $field ) {
            if ( $field['in_selector'] && 
                 ! in_array( $key, $skipFields ) ) { 
                if ( strpos( $key, '-' ) !== false ) {
                    list( $fieldName, $id ) = explode( '-', $key );
                    $locationTypeName = CRM_Utils_Array::value( $id, $locationTypes );
                    if ( ! $locationTypeName ) {
                        continue;
                    }

                    if ( in_array( $fieldName, array( 'phone', 'im', 'email' ) ) ) { 
                        $names[] = "{$locationTypeName}-{$fieldName}-1";
                    } else {
                        $names[] = "{$locationTypeName}-{$fieldName}";
                    }
                } else {
                    $names[] = $field['name'];
                }
            }
        }

        while ($result->fetch()) {
            if (isset($result->country)) {
                // the query returns the untranslated country name
                $i18n =& CRM_Core_I18n::singleton();
                $result->country = $i18n->translate($result->country);
            }
            $row = array( );
            $empty = true;
            $row[] = CRM_Contact_BAO_Contact::getImage( $result->contact_type );
            foreach ( $names as $name ) {
                if ( $cfID = CRM_Core_BAO_CustomField::getKeyID($name)) {
                    $row[] = CRM_Core_BAO_CustomField::getDisplayValue( $result->$name, $cfID, $this->_options );
                } else {
                    $row[] = $result->$name;
                }

                if ( ! empty( $result->$name ) ) {
                    $empty = false;
                }
            }

            $row[] = CRM_Core_Action::formLink(self::links(), $mask, array('id' => $result->contact_id, 'gid' => $this->_gid));

            if ( ! $empty ) {
                $rows[] = $row;
            }
        }
        return $rows;
    }

    /**
     * name of export file.
     *
     * @param string $output type of output
     * @return string name of the file
     */
    function getExportFileName( $output = 'csv') {
        return ts('CiviCRM Profile Listings');
    }
    
}//end of class

?>
