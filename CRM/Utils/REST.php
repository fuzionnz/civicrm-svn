<?php
/*
   +--------------------------------------------------------------------+
   | CiviCRM version 4.1                                                |
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
 * This class handles all REST client requests.
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2011
 *
 */
class CRM_Utils_REST {

  /**
   * Number of seconds we should let a REST process idle
   * @static
   */
  static $rest_timeout = 0;

  /**
   * Cache the actual UF Class
   */
  public $ufClass;

  /**
   * Class constructor.  This caches the real user framework class locally,
   * so we can use it for authentication and validation.
   *
   * @param  string $uf       The userframework class
   */
  public function __construct() {
    // any external program which call Rest Server is responsible for
    // creating and attaching the session
    $args = func_get_args();
    $this->ufClass = array_shift($args);
  }

  /**
   * Simple ping function to test for liveness.
   *
   * @param string $var   The string to be echoed
   *
   * @return string       $var
   * @access public
   */
  public function ping($var = NULL) {
    $session = CRM_Core_Session::singleton();
    $key = $session->get('key');
    //$session->set( 'key', $var );
    return self::simple(array('message' => "PONG: $key"));
  }

  /**
   * Authentication wrapper to the UF Class
   *
   * @param string $name      Login name
   * @param string $pass      Password
   *
   * @return string           The REST Client key
   * @access public
   * @static
   */
  public function authenticate($name, $pass) {

    $result = &CRM_Utils_System::authenticate($name, $pass);

    if (empty($result)) {
      return self::error('Could not authenticate user, invalid name or password.');
    }

    $session = CRM_Core_Session::singleton();
    $api_key = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $result[0], 'api_key');

    if (empty($api_key)) {
      // These two lines can be used to set the initial value of the key.  A better means is needed.
      //CRM_Core_DAO::setFieldValue('CRM_Contact_DAO_Contact', $result[0], 'api_key', sha1($result[2]) );
      //$api_key = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $result[0], 'api_key');
      return self::error("This user does not have a valid API key in the database, and therefore cannot authenticate through this interface");
    }

    // Test to see if I can pull the data I need, since I know I have a good value.
    $user = &CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $api_key, 'id', $api_key);

    $session->set('api_key', $api_key);
    $session->set('key', $result[2]);
    $session->set('rest_time', time());
    $session->set('PHPSESSID', session_id());
    $session->set('cms_user_id', $result[1]);

    return self::simple(array('api_key' => $api_key, 'PHPSESSID' => session_id(), 'key' => sha1($result[2])));
  }

  // Generates values needed for error messages
  function error($message = 'Unknown Error') {

    $values = array(
      'error_message' => $message,
      'is_error' => 1,
    );
    return $values;
  }

  // Generates values needed for non-error responses.
  function simple($params) {
    $values = array('is_error' => 0);
    $values += $params;
    return $values;
  }

  function run() {
    $result = self::handle();
    return self::output($result);
  }

  function output(&$result) {
    $hier = FALSE;
    if (is_scalar($result)) {
      if (!$result) {
        $result = 0;
      }
      $result = self::simple(array('result' => $result));
    }
    elseif (is_array($result)) {
      if (CRM_Utils_Array::isHierarchical($result)) {
        $hier = TRUE;
      }
      elseif (!array_key_exists('is_error', $result)) {
        $result['is_error'] = 0;
      }
    }
    else {
      $result = self::error('Could not interpret return values from function.');
    }

    if (CRM_Utils_Array::value('json', $_REQUEST)) {
      header('Content-Type: text/javascript');
      $json = json_encode(array_merge($result));
      if (CRM_Utils_Array::value('debug', $_REQUEST)) {
        return CRM_Utils_REST::jsonFormated($json);
      }
      return $json;
    }


    if (isset($result['count'])) {


      $count = ' count="' . $result['count'] . '" ';


    }
    else $count = "";
    $xml = "<?xml version=\"1.0\"?>
      <ResultSet xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" $count>
      ";
    // check if this is a single element result (contact_get etc)
    // or multi element
    if ($hier) {
      foreach ($result['values'] as $n => $v) {
        $xml .= "<Result>\n" . CRM_Utils_Array::xml($v) . "</Result>\n";
      }
    }
    else {
      $xml .= "<Result>\n" . CRM_Utils_Array::xml($result) . "</Result>\n";
    }

    $xml .= "</ResultSet>\n";
    return $xml;
  }

  function jsonFormated($json) {
    $tabcount   = 0;
    $result     = '';
    $inquote    = FALSE;
    $inarray    = FALSE;
    $ignorenext = FALSE;

    $tab = "\t";
    $newline = "\n";

    for ($i = 0; $i < strlen($json); $i++) {
      $char = $json[$i];

      if ($ignorenext) {
        $result .= $char;
        $ignorenext = FALSE;
      }
      else {
        switch ($char) {
          case '{':
            if ($inquote) {
              $result .= $char;
            }
            else {
              $inarray = FALSE;
              $tabcount++;
              $result .= $char . $newline . str_repeat($tab, $tabcount);
            }
            break;

          case '}':
            if ($inquote) {
              $result .= $char;
            }
            else {
              $tabcount--;
              $result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
            }
            break;

          case ',':
            if ($inquote || $inarray) {
              $result .= $char;
            }
            else $result .= $char . $newline . str_repeat($tab, $tabcount);
            break;

          case '"':
            $inquote = !$inquote;
            $result .= $char;
            break;

          case '\\':
            if ($inquote) {
              $ignorenext = TRUE;
            }
            $result .= $char;
            break;

          case '[':
            $inarray = TRUE;
            $result .= $char;
            break;

          case ']':
            $inarray = FALSE;
            $result .= $char;
            break;

          default:
            $result .= $char;
        }
      }
    }

    return $result;
  }

  function handle() {
    // Get the function name being called from the q parameter in the query string
    $q = CRM_Utils_array::value('q', $_REQUEST);
    // or for the rest interface, from fnName
    $r = CRM_Utils_array::value('fnName', $_REQUEST);
    if (!empty($r)) {
      $q = $r;
    }
    if (!empty($q)) {
      $args = explode('/', $q);
      // If the function isn't in the civicrm namespace, reject the request.
      if ($args[0] != 'civicrm') {
        return self::error('Unknown function invocation.');
      }

      // If the query string is malformed, reject the request.
      if ((count($args) != 3) && ($args[1] != 'login') && ($args[1] != 'ping')) {
        return self::error('Unknown function invocation.');
      }
      $store = NULL;
      if ($args[1] == 'login') {
        $name = CRM_Utils_Request::retrieve('name', 'String', $store, FALSE, NULL, 'REQUEST');
        $pass = CRM_Utils_Request::retrieve('pass', 'String', $store, FALSE, NULL, 'REQUEST');
        if (empty($name) ||
          empty($pass)
        ) {
          return self::error('Invalid name / password.');
        }
        return self::authenticate($name, $pass);
      }
      elseif ($args[1] == 'ping') {
        return self::ping();
      }
    }
    else {
      // or the new format (entity+action)
      $args[1] = CRM_Utils_array::value('entity', $_REQUEST);
      $args[2] = CRM_Utils_array::value('action', $_REQUEST);
    }
    // Everyone should be required to provide the server key, so the whole
    //  interface can be disabled in more change to the configuration file.
    //  This used to be done in the authenticate function, but that was bad...trust me
    // first check for civicrm site key
    if (!CRM_Utils_System::authenticateKey(FALSE)) {
      $docLink = CRM_Utils_System::docURL2("Managing Scheduled Jobs", TRUE, NULL, NULL, NULL, "wiki");
      $key = CRM_Utils_array::value('key', $_REQUEST);
      if (empty($key)) {
        return self::error("FATAL: mandatory param 'key' missing. More info at: " . $docLink);
      }
    }


    // At this point we know we are not calling either login or ping (neither of which
    //  require authentication prior to being called.  Therefore, at this point we need
    //  to make sure we're working with a trusted user.

    // There are two ways to check for a trusted user:
    //  First: they can be someone that has a valid session currently
    //  Second: they can be someone that has provided an API_Key

    $valid_user = FALSE;

    // Check for valid session.  Session ID's only appear here if you have
    // run the rest_api login function.  That might be a problem for the
    // AJAX methods.
    $session = CRM_Core_Session::singleton();
    if ($session->get('PHPSESSID')) {
      $valid_user = TRUE;
    }

    // If the user does not have a valid session (most likely to be used by people using
    // an ajax interface), we need to check to see if they are carring a valid user's
    // secret key.
    if (!$valid_user) {
      $api_key = CRM_Utils_Request::retrieve('api_key', 'String', $store, FALSE, NULL, 'REQUEST');
      if (!$api_key) {
        return ("FATAL:mandatory param 'api_key' (user key) missing");
      }
      $valid_user = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $api_key, 'id', 'api_key');
    }

    // If we didn't find a valid user either way, then die.
    if (empty($valid_user)) {
      return self::error("Invalid session or user api_key invalid");
    }

    return self::process($args);
  }

  static
  function process(&$args, $restInterface = TRUE) {
    $params = &self::buildParamList();

    $params['check_permissions'] = TRUE;
    $fnName = $apiFile = NULL;
    // clean up all function / class names. they should be alphanumeric and _ only
    for ($i = 1; $i <= 3; $i++) {
      if (!empty($args[$i])) {
        $args[$i] = CRM_Utils_String::munge($args[$i]);
      }
    }

    // incase of ajax functions className is passed in url
    if (isset($params['className'])) {
      $params['className'] = CRM_Utils_String::munge($params['className']);

      // functions that are defined only in AJAX.php can be called via
      // rest interface
      $class = explode('_', $params['className']);
      if ($class[0] != 'CRM' ||
        count($class) < 4 ||
        $class[count($class) - 1] != 'AJAX'
      ) {
        return self::error('Unknown function invocation.');
      }

      $params['fnName'] = CRM_Utils_String::munge($params['fnName']);

      // evaluate and call the AJAX function
      require_once (str_replace('_', DIRECTORY_SEPARATOR, $params['className']) . ".php");
      if (!method_exists($params['className'], $params['fnName'])) {
        return self::error('Unknown function invocation.');
      }

      return call_user_func(array($params['className'], $params['fnName']), $params);
    }

    if (!array_key_exists('version', $params)) {
      $params['version'] = 3;
    }

    if ($params['version'] == 2) {
      $result['is_error'] = 1;
      $result['error_message'] = "FATAL: API v2 not accessible from ajax/REST";
      $result['deprecated'] = "Please upgrade to API v3";
      return $result;
    }

    // trap all fatal errors
    CRM_Core_Error::setCallback(array('CRM_Utils_REST', 'fatal'));
    $result = civicrm_api($args[1], $args[2], $params);
    CRM_Core_Error::setCallback();

    if ($result === FALSE) {
      return self::error('Unknown error.');
    }
    return $result;
  }

  static
  function &buildParamList() {
    $params = array();

    $skipVars = array(
      'q' => 1,
      'json' => 1,
      'key' => 1,
    );

    foreach ($_REQUEST as $n => $v) {
      if (!array_key_exists($n, $skipVars)) {
        $params[$n] = $v;
      }
    }
    if (array_key_exists('return', $_REQUEST) && is_array($_REQUEST['return'])) {
      foreach ($_REQUEST['return'] as $key => $v) $params['return.' . $key] = 1;
    }
    return $params;
  }

  static
  function fatal($pearError) {
    header('Content-Type: text/xml');
    $error = array();
    $error['code'] = $pearError->getCode();
    $error['error_message'] = $pearError->getMessage();
    $error['mode'] = $pearError->getMode();
    $error['debug_info'] = $pearError->getDebugInfo();
    $error['type'] = $pearError->getType();
    $error['user_info'] = $pearError->getUserInfo();
    $error['to_string'] = $pearError->toString();
    $error['is_error'] = 1;

    echo self::output($error);

    CRM_Utils_System::civiExit();
  }

  static
  function APIDoc() {

    CRM_Utils_System::setTitle("API Parameters");
    $template = CRM_Core_Smarty::singleton();
    return CRM_Utils_System::theme('page',
      $template->fetch('CRM/Core/APIDoc.tpl'),
      TRUE
    );
  }

  static
  function ajaxDoc() {

    CRM_Utils_System::setTitle("API explorer and generator");
    $template = CRM_Core_Smarty::singleton();
    return CRM_Utils_System::theme('page',
      $template->fetch('CRM/Core/AjaxDoc.tpl'),
      TRUE
    );
  }

  /** used to load a template "inline", eg. for ajax, without having to build a menu for each template */
  static 
  function loadTemplate () {
    $request = CRM_Utils_Request::retrieve( 'q', 'String');
    if (false !== strpos($request, '..')) {
      die ("SECURITY FATAL: the url can't contain '..'. Please report the issue on the forum at civicrm.org");
    }

    $request = split ('/',$request);
    $entity = _civicrm_api_get_camel_name($request[2]);
    $tplfile=_civicrm_api_get_camel_name($request[3]);

    $tpl = 'CRM/'.$entity.'/Page/Inline/'.$tplfile.'.tpl';
    $smarty= CRM_Core_Smarty::singleton( );
    CRM_Utils_System::setTitle( "$entity::$tplfile inline $tpl" );
    if( !$smarty->template_exists($tpl) ){
      header("Status: 404 Not Found"); 
      die ("Can't find the requested template file templates/$tpl");
    }
    if (array_key_exists('id',$_GET)) {// special treatmenent, because it's often used
      $smarty->assign ('id',(int)$_GET['id']);// an id is always positive
    }
    $pos = strpos (implode (array_keys ($_GET)),'<') ;

    if ($pos !== false) {
      die ("SECURITY FATAL: one of the param names contains &lt;");
    }
    $param = array_map( 'htmlentities' , $_GET);
    unset($param['q']);
    $smarty->assign_by_ref("request", $param);

    if  ( ! array_key_exists ( 'HTTP_X_REQUESTED_WITH', $_SERVER ) ||
      $_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest"  )  {

        $smarty->assign( 'tplFile', $tpl );
        $config = CRM_Core_Config::singleton();
        $content = $smarty->fetch( 'CRM/common/'. strtolower($config->userFramework) .'.tpl' );

        CRM_Utils_System::appendTPLFile( $tpl, $content );

        return CRM_Utils_System::theme( 'page', $content, true);

      } else {
        $content = "<!-- .tpl file embeded: $tpl -->\n";
        CRM_Utils_System::appendTPLFile( $tpl, $content );
        echo $content . $smarty->fetch ($tpl);
        CRM_Utils_System::civiExit( );
    }
  }

  /** This is a wrapper so you can call an api via json (it returns json too)
   * http://example.org/civicrm/api/json?entity=Contact&action=Get"&json={"contact_type":"Individual","email.get.email":{}} to take all the emails from individuals
   * works for POST & GET (POST recommended)
   **/
  static
  function ajaxJson() {
    require_once 'api/v3/utils.php';
    if (!$config->debug && (!array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) ||
        $_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest"
      )) {
      $error = civicrm_api3_create_error("SECURITY ALERT: Ajax requests can only be issued by javascript clients, eg. $().crmAPI().",
        array(
          'IP' => $_SERVER['REMOTE_ADDR'],
          'level' => 'security',
          'referer' => $_SERVER['HTTP_REFERER'],
          'reason' => 'CSRF suspected',
        )
      );
      echo json_encode($error);
      CRM_Utils_System::civiExit();
    }
    if (empty($_REQUEST['entity'])) {
      echo json_encode(civicrm_api3_create_error('missing entity param'));
      CRM_Utils_System::civiExit();
    }
    if (empty($_REQUEST['entity'])) {
      echo json_encode(civicrm_api3_create_error('missing entity entity'));
      CRM_Utils_System::civiExit();
    }
    if (!empty($_REQUEST['json'])) {
      $params = json_decode($_REQUEST['json'], TRUE);
    }
    $entity = CRM_Utils_String::munge(CRM_Utils_Array::value('entity', $_REQUEST));
    $action = CRM_Utils_String::munge(CRM_Utils_Array::value('action', $_REQUEST));
    if (!is_array($params)) {
      echo json_encode(array('is_error' => 1, 'error_message', 'invalid json format: ?{"param_with_double_quote":"value"}'));
      CRM_Utils_System::civiExit();
    }

    $params['check_permissions'] = TRUE;
    $params['version'] = 3;
    $_REQUEST['json'] = 1;
    if (!$params['sequential']) {
      $params['sequential'] = 1;
    }
    // trap all fatal errors
    CRM_Core_Error::setCallback(array('CRM_Utils_REST', 'fatal'));
    $result = civicrm_api($entity, $action, $params);

    CRM_Core_Error::setCallback();

    echo self::output($result);

    CRM_Utils_System::civiExit();
  }

  static
  function ajax() {
    // this is driven by the menu system, so we can use permissioning to
    // restrict calls to this etc
    // the request has to be sent by an ajax call. First line of protection against csrf
    $config = CRM_Core_Config::singleton();
    if (FALSE &&
      !$config->debug &&
      (!array_key_exists('HTTP_X_REQUESTED_WITH', $_SERVER) ||
        $_SERVER['HTTP_X_REQUESTED_WITH'] != "XMLHttpRequest"
      )
    ) {
      require_once 'api/v3/utils.php';
      $error = civicrm_api3_create_error("SECURITY ALERT: Ajax requests can only be issued by javascript clients, eg. $().crmAPI().",
        array(
          'IP' => $_SERVER['REMOTE_ADDR'],
          'level' => 'security',
          'referer' => $_SERVER['HTTP_REFERER'],
          'reason' => 'CSRF suspected',
        )
      );
      echo json_encode($error);
      CRM_Utils_System::civiExit();
    }

    $q = CRM_Utils_Array::value('fnName', $_REQUEST);
    if (!$q) {
      $entity = CRM_Utils_Array::value('entity', $_REQUEST);
      $action = CRM_Utils_Array::value('action', $_REQUEST);
      if (!$entity || !$action) {
        $err = array('error_message' => 'missing mandatory params "entity=" or "action="', 'is_error' => 1);
        echo self::output($err);
        CRM_Utils_System::civiExit();
      }
      $args = array('civicrm', $entity, $action);
    }
    else {
      $args = explode('/', $q);
    }

    // get the class name, since all ajax functions pass className
    $className = CRM_Utils_Array::value('className', $_REQUEST);

    // If the function isn't in the civicrm namespace, reject the request.
    if (($args[0] != 'civicrm' &&
        count($args) != 3
      ) && !$className) {
      return self::error('Unknown function invocation.');
    }

    $result = self::process($args, FALSE);

    echo self::output($result);

    CRM_Utils_System::civiExit();
  }

  function loadCMSBootstrap() {
    $q = CRM_Utils_array::value('q', $_REQUEST);
    $args = explode('/', $q);

    // If the function isn't in the civicrm namespace or request
    // is for login or ping
    if (empty($args) ||
      $args[0] != 'civicrm' ||
      ((count($args) != 3) && ($args[1] != 'login') && ($args[1] != 'ping')) ||
      $args[1] == 'ping'
    ) {
      return;
    }

    if (!CRM_Utils_System::authenticateKey(FALSE)) {
      return;
    }

    if ($args[1] == 'login') {
      CRM_Utils_System::loadBootStrap(CRM_Core_DAO::$_nullArray, TRUE, FALSE);
      return;
    }

    $uid = NULL;
    $session = CRM_Core_Session::singleton();

    if ($session->get('PHPSESSID') &&
      $session->get('cms_user_id')
    ) {
      $uid = $session->get('cms_user_id');
    }

    if (!$uid) {
      $store      = NULL;
      $api_key    = CRM_Utils_Request::retrieve('api_key', 'String', $store, FALSE, NULL, 'REQUEST');
      $contact_id = CRM_Core_DAO::getFieldValue('CRM_Contact_DAO_Contact', $api_key, 'id', 'api_key');
      if ($contact_id) {
        $uid = CRM_Core_BAO_UFMatch::getUFId($contact_id);
      }
    }

    if ($uid) {
      CRM_Utils_System::loadBootStrap(array('uid' => $uid), TRUE, FALSE);
    }
  }
}

