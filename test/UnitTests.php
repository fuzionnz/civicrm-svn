<?php

require_once 'config.inc.php';

require_once 'SimpleTest/unit_tester.php';
require_once 'SimpleTest/reporter.php';

require_once 'CRM/Config.php';

if ( !defined( 'TEST' ) ) {
    define( 'TEST', __FILE__ );
}

class UnitTests extends GroupTest {
    
    function __construct( ) {
        parent::__construct( 'Unit Tests for CRM' );
        
        $this->addTestFile( CRM_TEST_DIR . 'CRM/api/CreateContact.php' );
    }

}

if ( TEST == __FILE__ ) {
    $test =& new UnitTests( );

    $config = CRM_Config::singleton();
    CRM_DAO::init($config->dsn, $config->daoDebug);
    $factoryClass = 'CRM_Contact_DAO_Factory';
    CRM_DAO::setFactory(new $factoryClass());

    // set error handling
    PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, array('CRM_Error', 'callback'));

    if ( SimpleReporter::inCli( ) ) {
        exit( $test->run( new TextReporter( ) ) ? 0 : 1 );
    }
    $test->run( new HtmlReporter( ) );
}

?>