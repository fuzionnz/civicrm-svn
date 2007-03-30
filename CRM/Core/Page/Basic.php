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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
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

require_once 'CRM/Core/Page.php';



abstract class CRM_Core_Page_Basic extends CRM_Core_Page {
    
    /**
     * define all the abstract functions here
     */

    /**
     * name of the BAO to perform various DB manipulations
     *
     * @return string
     * @access public
     */

    abstract function getBAOName( );

    /**
     * an array of action links
     *
     * @return array (reference)
     * @access public
     */
    abstract function &links( );

    /**
     * name of the edit form class
     *
     * @return string
     * @access public
     */
    abstract function editForm( );

    /**
     * name of the form
     *
     * @return string
     * @access public
     */
    abstract function editName( );

    /**
     * userContext to pop back to
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    abstract function userContext( $mode = null );

    /**
     * function to get userContext params
     *
     * @param int $mode mode that we are in
     *
     * @return string
     * @access public
     */
    function userContextParams( $mode = null ) {
        return 'reset=1&action=browse';
    }

    /**
     * allow objects to be added based on permission
     *
     * @param int $id   the id of the object
     * @param int $name the name or title of the object
     *
     * @return string   permission value if permission is granted, else null
     * @access public
     */
    public function checkPermission( $id, $name ) {
        return CRM_Core_Permission::EDIT;
    }

    /**
     * allows the derived class to add some more state variables to
     * the controller. By default does nothing, and hence is abstract
     *
     * @param CRM_Core_Controller $controller the controller object
     *
     * @return void
     * @access public
     */
    function addValues( $controller ) {
    }

    /**
     * class constructor
     *
     * @param string $title title of the page
     * @param int    $mode  mode of the page
     *
     * @return CRM_Core_Page
     */
    function __construct( $title = null, $mode = null ) {
        parent::__construct($title, $mode);
    }

    /**
     * Run the basic page (run essentially starts execution for that page).
     *
     * @return void
     */
    function run( $sort = '' )
    {
        // what action do we want to perform ? (store it for smarty too.. :) 
        $this->assign( 'dojoIncludes', "dojo.require('dojo.widget.SortableTable');" );

        $action = CRM_Utils_Request::retrieve( 'action', 'String',
                                               $this, false, 'browse' );
        $this->assign( 'action', $action );

        // get 'id' if present
        $id  = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                            $this, false, 0 );

        require_once(str_replace('_', DIRECTORY_SEPARATOR, $this->getBAOName()) . ".php");

        if ($action & (CRM_Core_Action::VIEW | CRM_Core_Action::ADD | CRM_Core_Action::UPDATE | CRM_Core_Action::DELETE)) {
            $this->edit($action, $id);                               // use edit form for view, add or update or delete
        } else if ($action & CRM_Core_Action::DISABLE) {
            eval($this->getBAOName( ) . '::setIsActive( $id, 0 );'); //disable
        } else if ( $action & CRM_Core_Action::ENABLE ) {
            eval($this->getBAOName( ) . '::setIsActive( $id, 1 );'); // enable
        } 

        // finally browse (list) the page
        $this->browse(null, $sort);

        return parent::run();
    }


    /**
     * browse all entities.
     *
     * @param int $action
     *
     * @return void
     * @access public
     */
    function browse( $action = null, $sort ) {
        $links =& $this->links();
        if ($action == null) {
            $action = array_sum(array_keys($links));
        }
        if ( $action & CRM_Core_Action::DISABLE ) {
            $action -= CRM_Core_Action::DISABLE;
        }
        if ( $action & CRM_Core_Action::ENABLE ) {
            $action -= CRM_Core_Action::ENABLE;
        }
        
        eval( '$object =& new ' . $this->getBAOName( ) . '( );' );
        
        $values = array();
        
        /*
         * lets make sure we get the stuff sorted by name if it exists
         */
        $fields =& $object->fields( );
        $key = '';
        if ( CRM_Utils_Array::value( 'title', $fields ) ) {
            $key = 'title';
        }  else if ( CRM_Utils_Array::value( 'label', $fields ) ) {
            $key = 'label';
        } else if ( CRM_Utils_Array::value( 'name', $fields ) ) {
            $key = 'name';
        }
        
        if ( $key ) {
            $object->orderBy ( $key . ' asc' );
        }
        
        if (trim($sort)) {
            $object->orderBy ( $sort );
        }
        
        // set the domain_id parameter
        $config =& CRM_Core_Config::singleton( );
        $object->domain_id = $config->domainID( );
        
        // find all objects
        $object->find();
        while ($object->fetch()) {
            if ( ! isset( $object->mapping_type ) ||
                 $object->mapping_type != "Search Builder" ) {
                $permission = CRM_Core_Permission::EDIT;
                if ( $key ) {
                    $permission = $this->checkPermission( $object->id, $object->$key );
                }
                if ( $permission ) {
                    $values[$object->id] = array( );
                    CRM_Core_DAO::storeValues( $object, $values[$object->id]);

                    require_once 'CRM/Contact/DAO/RelationshipType.php';
                    CRM_Contact_DAO_RelationshipType::addDisplayEnums($values[$object->id]);
                    
                    // populate action links
                    self::action( $object, $action, $values[$object->id], $links, $permission );
                }
                $this->assign( 'rows', $values );
            }
        }
    }
    
    /**
     * Given an object, get the actions that can be associated with this
     * object. Check the is_active and is_required flags to display valid
     * actions
     *
     * @param CRM_Core_DAO $object the object being considered
     * @param int     $action the base set of actions
     * @param array   $values the array of values that we send to the template
     * @param array   $links  the array of links
     * @param string  $permission the permission assigned to this object
     *
     * @return void
     * @access private
     */
    function action( &$object, $action, &$values, &$links, $permission ) {
        $values['class'] = '';
        if ( array_key_exists( 'is_reserved', $object ) && $object->is_reserved ) {
            $newAction = 0;
            $values['action'] = '';
            $values['class'] = 'reserved';
            return;
        }

        $newAction = $action;
        if ( array_key_exists( 'is_active', $object ) ) {
            if ( $object->is_active ) {
                $newAction += CRM_Core_Action::DISABLE;
            } else {
                $newAction += CRM_Core_Action::ENABLE;
            }
        }

        // make sure we only allow those actions that the user is permissioned for
        $newAction = $newAction & CRM_Core_Action::mask( $permission );

        $values['action'] = CRM_Core_Action::formLink( $links, $newAction, array( 'id' => $object->id ) );
    }

    /**
     * Edit this entity.
     *
     * @param int $mode - what mode for the form ?
     * @param int $id - id of the entity (for update, view operations)
     * @return void
     */
    function edit( $mode, $id = null , $imageUpload = false , $pushUserContext = true) 
    {
        $controller =& new CRM_Core_Controller_Simple( $this->editForm( ), $this->editName( ), $mode , $imageUpload );

       // set the userContext stack
        if( $pushUserContext ) {
            $session =& CRM_Core_Session::singleton();
            $session->pushUserContext( CRM_Utils_System::url( $this->userContext( $mode ), $this->userContextParams( $mode ) ) );
        }
        if ($id) {
            $controller->set( 'id'   , $id );
        }
        $controller->set('BAOName', $this->getBAOName());
        $this->addValues($controller);
        $controller->setEmbedded( true );
        $controller->process( );
        $controller->run( );
    }

}

?>
