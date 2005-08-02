<?php

global $civicrm_root;

$include_path = ini_get('include_path');
$include_path = '.'        . PATH_SEPARATOR .
                $civicrm_root . PATH_SEPARATOR . 
                $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . PATH_SEPARATOR .
                $include_path;
ini_set('include_path', $include_path);

define( 'CRM_SMARTYDIR'  , $civicrm_root . DIRECTORY_SEPARATOR . 'packages' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR );
define( 'CRM_TEST_DIR'   , $civicrm_root . DIRECTORY_SEPARATOR . 'test'   . DIRECTORY_SEPARATOR );
define( 'CRM_DAO_DEBUG'  , 0 );
define( 'CRM_TEMPLATEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'templates'   );
define( 'CRM_PLUGINSDIR' , $civicrm_root . DIRECTORY_SEPARATOR . 'CRM' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Smarty' . DIRECTORY_SEPARATOR . 'plugins' );

define( 'CRM_GETTEXT_CODESET'    , 'utf-8'   );
define( 'CRM_GETTEXT_DOMAIN'     , 'civicrm' );
define( 'CRM_GETTEXT_RESOURCEDIR', $civicrm_root . DIRECTORY_SEPARATOR . 'l10n' );

if ( ! defined( 'CRM_USERFRAMEWORK' ) ) {
    define( 'CRM_USERFRAMEWORK', 'Drupal' );
}

if ( ! defined( 'CRM_HTTPBASE' ) ) {
  define( 'CRM_HTTPBASE', '/drupal/' );
}

if ( ! defined( 'CRM_RESOURCEBASE' ) ) {
    define( 'CRM_RESOURCEBASE', CRM_HTTPBASE . 'modules/civicrm/' );
}

if ( ! defined( 'CRM_MAINMENU' ) ) {
  define( 'CRM_MAINMENU', CRM_HTTPBASE . 'civicrm/' );
}

if ( ! defined( 'JPSPAN' ) ) {
    define( JPSPAN, $civicrm_root . DIRECTORY_SEPARATOR . packages . DIRECTORY_SEPARATOR . 'JPSpan' . DIRECTORY_SEPARATOR );
}

// drupal specific code
if ( CRM_USERFRAMEWORK == 'Drupal' ) {
    if ( function_exists( 'variable_get' ) && variable_get('clean_url', '0') != '0' ) {
        define( 'CRM_CLEANURL', 1 );
    } else {
        define( 'CRM_CLEANURL', 0 );
    }

    global $db_prefix;
    if ( isset( $db_prefix )    &&
         is_array( $db_prefix ) &&
         array_key_exists( 'civicrm', $db_prefix ) &&
         is_int( $db_prefix['civicrm'] ) ) {
        define( 'CRM_DOMAIN_ID', $db_prefix['civicrm'] );
    } else {
        define( 'CRM_DOMAIN_ID', 1 );
    }
}

?>
