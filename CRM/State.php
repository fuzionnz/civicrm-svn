<?php
/**
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
 * The basic state element. Each state element is linked to a form and
 * represents the form in the transition diagram. We use the state to 
 * determine what action to take on various user input. Actions include
 * things like going back / stepping forward / process etc
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

class CRM_State {

    /**
     * Internal state name
     * @var string
     */
    protected $_iname;

    /**
     * Display name for state
     * @var string
     */
    protected $_name;

    /**
     * this is a combination "OR" of the STATE_* constants defined below
     * @var int
     */
    protected $_type;

    /**
     * the state that precedes this state
     * @var object
     */
    protected $_back;

    /**
     * the state that succeeds this state
     * @var object
     */
    protected $_next;

    /**
     * The state machine that this state is part of
     * @var object
     */
    protected $_stateMachine;

    /**
     * The different types of states. As we flush out the framework more
     * we will introduce other conditional / looping states which will
     * bring in more complexity to the framework. For now, lets keep it simple
     * @var int
     */
    const
        START       =  1,
        FINISH      =  2,
        SIMPLE      =  4;

    /**
     * constructor
     *
     * @param string the internal name of the state
     * @param string the display name for this state
     * @param int    the state type
     * @param object the state that precedes this state
     * @param object the state that follows  this state
     * @param object the statemachine that this states belongs to
     *
     * @return object
     * @access public
     */
    function __construct( $iname, $name, $type, $back, $next, $stateMachine ) {
        $this->_iName       = $iname;
        $this->_name        = $name;
        $this->_type        = $type;
        $this->_back        = $back;
        $this->_next        = $next;
    
        $this->_stateMachine = $stateMachine;
    }

    /**
     * Given an CRM Form, jump to the previous page
     *
     * @param object the CRM_Form element under consideration
     *
     * @return mixed does a jump to the back state
     * @access public
     */
    function handleBackState( &$page ) {
        if ( $this->_type & CRM_State::START ) {
            $page->handle('display');
        } else { 
            $back =& $page->controller->getPage($this->_back); 
            return $back->handle('jump'); 
        }
    }

    /**
     * Given an CRM Form, jump to the next page
     *
     * @param object the CRM_Form element under consideration
     *
     * @return mixed does a jump to the nextstate
     * @access public
     */
    function handleNextState( &$page ) {
        if ( $this->_type & CRM_State::FINISH ) {
            $page->handle('process');
        } else { 
            $next =& $page->controller->getPage($this->_next); 
            return $next->handle('jump'); 
        } 
    }

    /**
     * Determine the name of the next state. This is useful when we want
     * to display the navigation labels or potential path
     *
     * @return string
     * @access public
     */
    function getNextState( ) {
        if ( $this->_type & CRM_State::FINISH ) {
            return null;
        } else { 
            $next =& $page->controller->getPage( $this->_next ); 
            return $next;
        } 
    }

    /**
     * Mark this page as valid for the QFC framework. This is needed as
     * we build more advanced functionality into the StateMachine
     *
     * @param object the QFC data container
     *
     * @return void
     * @access public 
     */
    function validate( &$data ) {
        $data['valid'][$this->_name] = true;
    }

    /**
     * Mark this page as invalid for the QFC framework. This is needed as
     * we build more advanced functionality into the StateMachine
     *
     * @param object the QFC data container
     *
     * @return void
     * @access public 
     */
    function invalidate( &$data ) {
        $data['valid'][$this->_name] = null;
    }

    /**
     * getter for name
     *
     * @return string
     * @access public
     */
    function getName( ) {
        return $this->_name;
    }

    /**
     * setter for name
     *
     * @param string
     * @return void
     * @access public
     */
    function setName( $name ) {
        $this->_name = $name;
    }

    /**
     * getter for iname
     *
     * @return string
     * @access public
     */
    function getIName( ) {
        return $this->_iname;
    }

    /**
     * setter for iname
     *
     * @param string
     * @return void
     * @access public
     */
    function setIName( $iname ) {
        $this->_iname = $iname;
    }

    /**
     * getter for type
     *
     * @return int
     * @access public
     */
    function getType( ) {
        return $this->_type;
    }

}

?>
