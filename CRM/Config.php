<?php

require_once 'CRM/DAO.php';

class CRM_Config {
  /*
   * application wide config options, with defaults (should be production defaults)
   *
   */
  public $dsn;
  public $daoDebugLvl		  = 0;
  public $smartyDir           = '/opt/local/lib/php/Smarty/';
  public $templateDir		  = './templates';
  public $templateCompileDir  = './templates_c';
  public $mainMenu            = 'http://localhost/drupal/';
  public $httpBase            = "http://localhost/drupal/";
  public $DAOFactoryClass	  = 'CRM_BAO_Factory';

  private static $_instance = null;

  static function instance($key = 'crm') {
    if (self::$_instance === null ) {
      self::$_instance = new CRM_Config($key);
    }
    return self::$_instance;
  }

  function __construct() {
    if (defined('CRM_DSN')) {
      $this->dsn = CRM_DSN;
    }

    if (defined('CRM_DAO_DEBUG_LVL') ) {
      $this->daoDebugLvl = CRM_DAO_DEBUG_LVL;
    }

    if (defined('CRM_DAO_FACTORY_CLASS') ) {
      $this->DAOFactoryClass = CRM_DAO_FACTORY_CLASS;
    }

    if (defined('CRM_SMARTYDIR')) {
      $this->smartyDir = CRM_SMARTYDIR;
    }

    if (defined('CRM_TEMPLATEDIR')) {
      $this->templateDir = CRM_TEMPLATEDIR;
    }

    if (defined('CRM_TEMPLATE_COMPILEDIR')) {
      $this->templateCompileDir = CRM_TEMPLATE_COMPILEDIR;
    }

    if ( defined( 'CRM_MAINMENU' ) ) {
      $this->mainMenu = CRM_MAINMENU;
    }

    if ( defined( 'CRM_HTTPBASE' ) ) {
      $this->httpBase = CRM_HTTPBASE;
    }

  }

  function init() {
    $this->initDataObject();
  }

  function initDataObject() {
    CRM_DataObject::init(
                         $this->dsn, 
                         $this->daoDebugLvl);

    $factoryClass = $this->DAOFactoryClass;
    CRM_Utils::import($factoryClass);
    CRM_DAO::setFactory(new $factoryClass());
  }

} // end CRM_Configuration

?>