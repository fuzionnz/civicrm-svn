<?php  // vim: set si ai expandtab tabstop=4 shiftwidth=4 softtabstop=4:

/**
 *  File for the TestContact class
 *
 *  (PHP 5)
 *  
 *   @author Walt Haas <walt@dharmatech.org> (801) 534-1262
 *   @copyright Copyright CiviCRM LLC (C) 2009
 *   @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html
 *              GNU Affero General Public License version 3
 *   @version   $Id$
 *   @package   CiviCRM
 *
 *   This file is part of CiviCRM
 *
 *   CiviCRM is free software; you can redistribute it and/or
 *   modify it under the terms of the GNU Affero General Public License
 *   as published by the Free Software Foundation; either version 3 of
 *   the License, or (at your option) any later version.
 *
 *   CiviCRM is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU Affero General Public License for more details.
 *
 *   You should have received a copy of the GNU Affero General Public
 *   License along with this program.  If not, see
 *   <http://www.gnu.org/licenses/>.
 */

/**
 *  Include class definitions
 */
require_once 'PHPUnit/Extensions/Database/TestCase.php';
require_once 'PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php';
require_once 'PHPUnit/Extensions/Database/DataSet/XmlDataSet.php';
require_once 'PHPUnit/Extensions/Database/DataSet/QueryDataSet.php';
require_once 'AllTests.php';
require_once 'api/v2/Contact.php';

/**
 *  Test APIv2 civicrm_activity_* functions
 *
 *  @package   CiviCRM
 */
class api_v2_TestContact extends PHPUnit_Extensions_Database_TestCase
{
    /**
     *  Database connection
     *
     *  @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $dbconn;

    /**
     *  Create database connection for this instance
     *
     *  @return PHPUnit_Extensions_Database_DB_IDatabaseConnection connection
     */
    protected function getConnection()
    {
        AllTests::installDB();
        return $this->createDefaultDBConnection(AllTests::$utils->pdo,
                                             'test_civicrm');
    }

    /**
     *  Required implementation of abstract method
     */
    protected function getDataSet() { }

    /**
     *  Test setup for every test
     *
     *  Connect to the database, truncate the tables that will be used
     *  and redirect stdin to a temporary file
     */
    public function setUp()
    {
        $this->dbconn = $this->getConnection();
        //  Use a temporary file for STDIN
        $GLOBALS['stdin'] = tmpfile( );
        if ( $GLOBALS['stdin'] === false ) {
            echo "Couldn't open temporary file\n";
            exit(1);
        }

        //  Truncate the tables
        $op = new PHPUnit_Extensions_Database_Operation_Truncate( );
        $op->execute( $this->dbconn,
                      new PHPUnit_Extensions_Database_DataSet_FlatXMLDataSet(
                             dirname(__FILE__) . '/truncate.xml') );
    }

    /**
     *  If tearDown() isn't defined, getConnection() and getDataSet()
     *  will be called automatically during teardown
     */
    public function tearDown() { }

    /**
     *  Test something
     */
    public function testSomething()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.');
    } 

} // class api_v2_TestContact

// -- set Emacs parameters --
// Local variables:
// mode: php;
// tab-width: 4
// c-basic-offset: 4
// c-hanging-comment-ender-p: nil
// indent-tabs-mode: nil
// End: