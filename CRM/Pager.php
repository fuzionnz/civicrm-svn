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
 * This class extends the PEAR pager object by substituting standard default pager arguments
 * We also extract the pageId from either the GET variables or the POST variable (since we
 * use a POST to jump to a specific page. At some point we should evaluate if we want
 * to use Pager_Jumping instead. We've changed the format to allow navigation by jumping
 * to a page and also First, Prev CURRENT Next Last
 *
 */

require_once 'Pager/Sliding.php';

class CRM_Pager extends Pager_Sliding {

    /**
     * constants for static parameters of the pager
     */
    const
        ROWCOUNT         = 50,
        PAGE_ID          = 'crmPID',
        PAGE_ID_BOTTOM   = 'crmPID_B';

    /**
     * the output of the pager. This is a name/value array with various keys
     * that an application could use to display the pager
     * @var array
     */
    public $_response;  // array of caculated Pager values;
  
    /**  
     * The pager constructor. Takes a few values, and then assigns a lot of defaults
     * to the PEAR pager class
     * We have embedded some html in this class. Need to figure out how to export this
     * to the top level at some point in time
     *
     * @param int     total        the total count of items to be displayed
     * @param int     currentPage  the page currently being displayed
     * @param string  status       the status message to be displayed. It embeds a token
     *                             %%statusMessage%% that will be replaced with which items
     *                             are currently being displayed
     * @param string  csvString    the title of the link to be displayed for the export
     * @param int     perPage      the number of items displayed per page
     *
     * @return object              the newly created and initialized pager object
     *
     * @access public
     *
     */
    function __construct( $params ) {
        if( $params['status'] === null ) {
            $params['status'] = "Contacts %%StatusMessage%%";
        }

        $this->initialize( $params );

        $this->Pager_Sliding( $params);

        $links = $this->getLinks( );

        list( $offset, $limit ) = $this->getOffsetAndRowCount( );
        $start = $offset + 1;
        $end   = $offset + $limit;
        if ( $end > $params['total'] ) {
            $end = $params['total'];
        }

        if ( $params['total'] == 0 ) {
            $statusMessage = '';
        } else {
            $statusMessage = "$start - $end of " . $params['total'];
        }
        $params['status'] = str_replace( '%%StatusMessage%%', $statusMessage, $params['status'] );

        $this->_response = array(
                               'first'        => $links['first'],
                               'back'         => $links['back'] ,
                               'next'         => $links['next'] ,
                               'last'         => $links['last'] ,
                               'pages'        => $links['pages'],
                               'currentPage'  => $this->getCurrentPageID(),
                               'numPages'     => $this->numPages(),
                               'csvString'    => $params['csvString'],
                               'status'       => $params['status'],
                               'buttonTop'    => $params['buttonTop'],
                               'buttonBottom' => $params['buttonBottom'],
                               );


        /**
         * A page cannot have two variables with the same form name. Hence in the 
         * pager display, we have a form submission at the top with the normal
         * page variable, but a different form element for one at the bottom
         *
         */
        $this->_response['titleTop']    = 'Page <input size=2 maxlength=3 name="' . CRM_Pager::PAGE_ID . '" type="text" value="' . $this->_response['currentPage'] . '" /> of ' . $this->_response['numPages'];
        $this->_response['titleBottom']    = 'Page <input size=2 maxlength=3 name="' . CRM_Pager::PAGE_ID_BOTTOM . '" type="text" value="' . $this->_response['currentPage'] . '" /> of ' . $this->_response['numPages'];

    }


    /**
     * helper function to assign remaining pager options as good default
     * values
     *
     * @param array   $params      the set of options needed to initialize the parent
     *                             constructor
     *
     * @access public
     * @return void
     *
     */
    function initialize( &$params ) {
        $config = CRM_Config::singleton( );

        /* set the mode for the pager to Sliding */
        $params['mode']       = 'Sliding';

        /* also set the urlVar to be a crm specific get variable */
        $params['urlVar']     = CRM_Pager::PAGE_ID;
    
        /* set this to a small value, since we dont use this functionality */
        $params['delta']      = 1;

        $params['totalItems'] = $params['total'];
        $params['perPage']    = $params['rowCount'];
        $params['append']     = true;
        $params['separator']  = '';
        $params['spacesBeforeSeparator'] = 1;
        $params['spacesAfterSeparator']  = 1;


        // set previous and next text labels
        $params['prevImg']    = 'Previous';
        $params['nextImg']    = 'Next';


        // set first and last text fragments
        $params['firstPagePre']  = '';
        $params['firstPageText'] = 'First';
        $params['firstPagePost'] = '';

        $params['lastPagePre']   = '';
        $params['lastPageText']  = 'Last';
        $params['lastPagePost']  = '';

        $params['currentPage'] = $this->getPageID( $params['pageID'], $params );

        return $params;
    }

    /**
     * Figure out the current page number based on value of
     * GET / POST variables. Hierarchy rules are followed,
     * GET over-rides a POST, a POST at the top overrides
     * a POST at the bottom (of the page)
     *
     * @param int defaultPageId   current pageId
     *
     * @return int                new pageId to display to the user
     *
     * @access public
     *
     */
    function getPageID( $defaultPageId = 1, &$params ) {
        if ( isset( $_GET[ CRM_Pager::PAGE_ID ] ) ) {
            $currentPage = max( (int ) @$_GET[ CRM_Pager::PAGE_ID ], 1 );
        } else if ( isset( $_POST[ $params['buttonTop'] ] ) && isset( $_POST[ CRM_Pager::PAGE_ID ] ) ) {
            $currentPage = max( (int ) @$_POST[ CRM_Pager::PAGE_ID ], 1 );
        } else if ( isset( $_POST[ $params['buttonBottom'] ] ) && isset( $_POST[ CRM_Pager::PAGE_ID_BOTTOM ] ) ) {
            $currentPage = max( (int ) @$_POST[ CRM_Pager::PAGE_ID_BOTTOM ], 1 );
        } else {
            $currentPage = $defaultPageId;
        }
        return $currentPage;
    }


    /**
     * Use the pager class to get the pageId and Offset
     *
     * @param void
     *
     * @return array: an array of the pageID and offset
     *
     * @access public
     *
     */
    function getOffsetAndRowCount( ) {
        $pageId = $this->getCurrentPageID( );
        if ( ! $pageId ) {
            $pageId = 1;
        }

        $offset = ( $pageId - 1 ) * $this->_perPage;

        return array( $offset, $this->_perPage );
    }

}

?>
