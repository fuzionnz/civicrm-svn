<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Controller.php';

class CRM_Quest_Controller_Recommender extends CRM_Core_Controller {

    protected $_action;

    protected $_scID;

    // public so that the state machine can access this
    public    $_subType;

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true,
                          $subType ) {
        parent::__construct( $title, $modal );

        $cid = $this->get( 'contactID' );
        $this->_action = CRM_Utils_Request::retrieve('action', 'String',
                                                     $this, false, 'update' );
        $this->assign( 'action', $this->_action );
        $this->assign( 'appName', $subType );

        $this->_scID = CRM_Utils_Request::retrieve( 'scid', 'Integer', $this, true );

        $this->_subType = $subType; 
        
        if ( ! $cid ) {
            $cid    = CRM_Utils_Request::retrieve( 'id', 'Positive',
                                                   $this );
            $session =& CRM_Core_Session::singleton( );
            $uid     = $session->get( 'userID' );

            if ( $cid ) {
                require_once 'CRM/Contact/BAO/Contact.php';
                require_once 'CRM/Utils/System.php';
                if ( $cid != $uid ) {
                    if ( ( $this->_action & CRM_Core_Action::UPDATE ) &&
                         ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::EDIT ) ) ) {
                        CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to edit this Recommendation.') );
                    } else if ( ( $this->_action & CRM_Core_Action::VIEW ) &&
                                ( ! CRM_Contact_BAO_Contact::permissionedContact( $uid , CRM_Core_Permission::VIEW ) ) ) {
                        CRM_Core_Error::statusBounce( ts('You do not have the necessary permission to view this Recommendation.') );
                    }
                    $this->assign('questURL', CRM_Utils_System::url( 'civicrm/contact/search' ) );
                }
            } else {
                $cid = $uid;
            }

            if ( ! $cid ) {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact id" ) );
            }
            $this->set( 'contactID'       , $cid );

            // set contact id and welcome name
       
            $dao =& new CRM_Contact_DAO_Contact( );
            $dao->id = $cid;
            if ( $dao->find( true ) &&
                 $dao->contact_sub_type == 'Recommender' ) {
                $this->set( 'welcome_name',
                            $dao->display_name );
            } else {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact record" ) );
            }

            // also set student's name
            $dao =& new CRM_Contact_DAO_Contact( );
            $dao->id = $this->_scID;
            if ( $dao->find( true ) &&
                 $dao->contact_sub_type == 'Student' ) {
                $this->set( 'student_welcome_name',
                            $dao->display_name );
            } else {
                CRM_Core_Error::fatal( ts( "Could not find a valid contact record for the student" ) );
            }

            // make sure that recommender is a counselor of student
            require_once 'CRM/Contact/DAO/Relationship.php';
            $dao =& new CRM_Contact_DAO_Relationship( );
            $rid = ( $this->_subType == 'Teacher' ) ? 9 : 10;
            $dao->relationship_type_id = $rid;
            $dao->contact_id_a = $this->_scID;
            $dao->contact_id_b = $cid;
            $dao->is_active    = true;
            if ( ! $dao->find( true ) ) {
                CRM_Core_Error::fatal( ts( "You do not have permission to create a recommendation for this student" ) );
            }
        }


        require_once 'CRM/Project/BAO/TaskStatus.php';
        list( $taskStatusID, $taskStatus ) = 
            CRM_Project_BAO_TaskStatus::getTaskStatusInitial( $this,
                                                              'civicrm_contact', $cid,
                                                              'civicrm_contact', $this->_scID,
                                                              10 );

        require_once "CRM/Quest/StateMachine/Recommender/$subType.php";
        eval( '$this->_stateMachine =& new CRM_Quest_StateMachine_Recommender_' . $subType . '( $this, $this->_action );' );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $this->_action );

        $this->addActions( );
    }

    /**
     * Process the request, overrides the default QFC run method
     * This routine actually checks if the QFC is modal and if it
     * is the first invalid page, if so it call the requested action
     * if not, it calls the display action on the first invalid page
     * avoids the issue of users hitting the back button and getting
     * a broken page
     *
     * This run is basically a composition of the original run and the
     * jump action
     *
     */
    function run( ) {
        // early escape if we are previewing the application
        if ( $this->_action == CRM_Core_Action::PREVIEW ) {
            return $this->preview( );
        }

        // the names of the action and page should be saved
        // note that this is split into two, because some versions of
        // php 5.x core dump on the triple assignment :)
        $this->_actionName = $this->getActionName();
        list($pageName, $action) = $this->_actionName;

        if ( $this->isModal( ) ) {
            if ( ! $this->isValid( $pageName ) ) {
                $pageName = $this->findInvalid( );
                $action   = 'display';
            }
        }

        // check dependency first
        // if dependency fails, this does not return, but does a redirect
        $this->_stateMachine->checkDependency( $this, $this->_pages[$pageName] );

        $this->wizardHeader( $pageName );

        // note that based on action, control might not come back!!
        // e.g. if action is a valid JUMP, u basically do a redirect
        // to the appropriate place
        $this->_pages[$pageName]->handle($action);
        return $pageName;
    }

    /**
     * Create the header for the wizard from the list of pages
     * Store the created header in smarty
     *
     * @param string $currentPageName name of the page being displayed
     * @return array
     * @access public
     */
    function wizardHeader( $currentPageName ) {
        $wizard          = array( );
        $wizard['steps'] = array( );

        $count           = 0;

        $data =& $this->container( );
        foreach ( $this->_pages as $name => $page ) {
            $step  = true;
            $link  = $this->_stateMachine->validPage( $name, $data['valid'] ) ? $page->getLink ( ) : null;
            $valid = $data['valid'][$name];

            $count++;
            $stepNumber = $count;
            $collapsed  = false;

            $wizard['steps'][] = array( 'name'       => $name,
                                        'title'      => $page->getTitle( ),
                                        'link'       => $link,
                                        'valid'      => $valid,
                                        'step'       => $step,
                                        'stepNumber' => $stepNumber,
                                        'collapsed'  => $collapsed );

            if ( $name == $currentPageName ) {
                $wizard['currentStepNumber']    = $stepNumber;
                $wizard['currentStepName']      = $name;
                $wizard['currentStepTitle']     = $page->getTitle( );
                $wizard['currentStepRootTitle'] = null;
            }
        }

        $wizard['stepCount']         = $count;

        // also add last page information, so we can easily get it in the template
        $wizard['count'] = count( $wizard['steps'] ) - 1;
        
        $this->addWizardStyle( $wizard ); 

        $this->assign_by_ref( 'wizard', $wizard );

        return $wizard;
    }

    function addWizardStyle( &$wizard ) {
        $wizard['style'] = array('barClass'             => 'app',
                                 'stepPrefixCurrent'    => ' ',
                                 'stepPrefixPast'       => ' ',
                                 'stepPrefixFuture'     => ' ', 
                                 'subStepPrefixCurrent' => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'subStepPrefixPast'    => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'subStepPrefixFuture'  => '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                                 'showTitle'            => 0 );
    }

    function rebuild( ) {
        $this->_stateMachine->rebuild( $this );

        $this->_pages = array( );
        $this->addPages( $this->_stateMachine );
    }

    function checkApplication( ) {
        $this->_stateMachine->checkApplication( $this );
    }

    function preview( ) {
        // lets switch to print mode
        $this->_print = true;
        
        // cache a display object
        $display =& new CRM_Core_QuickForm_Action_Display( $this->_stateMachine );

        // we need to run each form and display it
        $pageNames = array_keys( $this->_pages );
        $html = array( );
        foreach ( $pageNames as $name ) {
            // build the form and then display it
            $this->_pages[$name]->setAction( CRM_Core_Action::VIEW | CRM_Core_Action::PREVIEW );
            $this->_pages[$name]->buildForm( );
            $this->wizardHeader( $name );
            $title = $this->_pages[$name]->getCompleteTitle( );

            $html[$title] = $display->renderForm( $this->_pages[$name], true );
        }

        $template =& CRM_Core_Smarty::singleton( );
        if ( $this->_subType == 'Counselor' ) {
            $template->assign( 'pageTitle', '2006 College Match Counselor Recommendation' );
        } else {
            $template->assign( 'pageTitle', '2006 College Match Teacher Recommendation' );
        }

        $template->assign_by_ref( 'pageHTML', $html );
        
        echo $template->fetch( "CRM/Quest/Page/View/Preview.tpl" );
        exit( );
    }

    function getTemplateFile( ) {
        if ( $this->_action & CRM_Core_Action::PREVIEW ) {
            return 'CRM/common/printBody.tpl';
        } else if ( $this->getPrint( ) ) {
            return 'CRM/common/print.tpl';
        } else {
            return 'CRM/index.tpl';
        }
    }

    function isApplicationComplete( ) {
        return $this->_stateMachine->isApplicationComplete( $this );
    }

}

?>