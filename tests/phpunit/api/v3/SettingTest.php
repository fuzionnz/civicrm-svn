<?php
// $Id$

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
*
* @package CiviCRM_APIv3_Core
*/

require_once 'tests/phpunit/CiviTest/CiviUnitTestCase.php';

/**
 * Class contains api test cases for civicrm settings
 *
 */
class api_v3_SettingTest extends CiviUnitTestCase {

  protected $_apiversion = 3;
  protected $_contactID;
  protected $_params;
  protected $_domainID2;
  protected $_domainID3;

  function __construct() {
    parent::__construct();

  }

  function get_info() {
    return array(
      'name' => 'Settings Tests',
      'description' => 'Settings API',
      'group' => 'CiviCRM API Tests',
    );
  }

  function setUp() {
    $params = array(
        'name' => 'A-team domain',
        'description' => 'domain of chaos',
        'version' => 3,
        'domain_version' => '4.2',
        'loc_block_id' => '2',
    );

    $result = civicrm_api( 'domain','create',$params );
    $this->_domainID2 = $result['id'];
    $params['name'] = 'B-team domain';
    $result = civicrm_api( 'domain','create',$params );
    $this->_domainID3 = $result['id'];
    parent::setUp();
  }

  function tearDown() {
    parent::tearDown();
  }

  /**
   * check getfields works
   */
  function testGetFields() {
    $params = array('version' => $this->_apiversion);
    $result = civicrm_api('setting', 'getfields', $params);
    $description = 'Demonstrate return from getfields - see subfolder for variants';
    $this->documentMe($params, $result, __FUNCTION__, __FILE__, $description);
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertArrayHasKey('customCSSURL', $result['values']);

    //let's check it's loading from cache by meddling with the cache
    $settingsMetadata = array();
    CRM_Core_BAO_Cache::setItem($settingsMetadata,'CiviCRM setting Spec', 'settingsMetadata_');
    $result = civicrm_api('setting', 'getfields', array('version' => $this->_apiversion));
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertArrayNotHasKey('customCSSURL', $result['values']);

    civicrm_api('system','flush', array('version' => $this->_apiversion));
    $description = 'Demonstrate return from getfields';
    $result = civicrm_api('setting', 'getfields', array('version' => $this->_apiversion));
    //  $this->documentMe($params, $result, __FUNCTION__, __FILE__, $description, 'GetFieldsGroup');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertArrayHasKey('customCSSURL', $result['values']);
    civicrm_api('system','flush', array('version' => $this->_apiversion));
  }

  /**
   * check getfields works
   */
  function testCreateSetting() {

    $params = array('version' => $this->_apiversion,
        'domain_id' => $this->_domainID2,
        'uniq_email_per_site' => 1,
    );
    $result = civicrm_api('setting', 'create', $params);
    $description = "shows setting a variable for a given domain - if no domain is set current is assumed";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__);
    $this->assertAPISuccess($result, "in line " . __LINE__);

    $params = array('version' => $this->_apiversion,
        'uniq_email_per_site' => 1,
    );
    $result = civicrm_api('setting', 'create', $params);
    $description = "shows setting a variable for a current domain";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__, $description, 'CreateSettingCurrentDomain');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertArrayHasKey(CRM_Core_Config::domainID(), $result['values']);
  }

  /**
   * check getfields works
   */
  function testCreateInvalidSettings() {

    $params = array('version' => $this->_apiversion,
        'domain_id' => $this->_domainID2,
        'invalid_key' => 1,
    );
    $result = civicrm_api('setting', 'create', $params);
    $this->assertEquals(1, $result['is_error']);

   }
  /**
   * check getfields works
   */
  function testCreateSettingMultipleDomains() {

    $params = array('version' => $this->_apiversion,
        'domain_id' => 'all',
        'uniq_email_per_site' => 1,
    );
    $result = civicrm_api('setting', 'create', $params);
    $description = "shows setting a variable for all domains";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__,$description, 'CreateAllDomains');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertEquals(1, $result['values'][3]['uniq_email_per_site']);
    $this->assertEquals(1, $result['values'][2]['uniq_email_per_site']);
    $this->assertEquals(1, $result['values'][1]['uniq_email_per_site']);


    // we'll check it with a 'get'
    $result = civicrm_api('setting', 'get', $params);
    $description = "shows getting a variable for all domains";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__,$description, 'GetAllDomains', 'Get');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertEquals(1, $result['values'][3]['uniq_email_per_site']);
    $this->assertEquals(1, $result['values'][2]['uniq_email_per_site']);
    $this->assertEquals(1, $result['values'][1]['uniq_email_per_site']);


    $params = array('version' => $this->_apiversion,
        'domain_id' => array(1,3),
        'uniq_email_per_site' => 0,
    );
    $result = civicrm_api('setting', 'create', $params);
    $description = "shows setting a variable for specified domains";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__,$description, 'CreateSpecifiedDomains');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertEquals(0, $result['values'][3]['uniq_email_per_site']);
    $this->assertEquals(0, $result['values'][1]['uniq_email_per_site']);

    $params['domain_id'] = array(1,2);
    $result = civicrm_api('setting', 'get', $params);
    $description = "shows getting a variable for specified domains";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__,$description, 'GetSpecifiedDomains', 'Get');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertEquals(1, $result['values'][2]['uniq_email_per_site']);
    $this->assertEquals(0, $result['values'][1]['uniq_email_per_site']);

  }

  function testGetSetting() {

    $params = array('version' => $this->_apiversion,
        'domain_id' => $this->_domainID2,
        'uniq_email_per_site' => 1,
    );
    $result = civicrm_api('setting', 'get', $params);
    $description = "shows get setting a variable for a given domain - if no domain is set current is assumed";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__);
    $this->assertAPISuccess($result, "in line " . __LINE__);

    $params = array('version' => $this->_apiversion,
        'uniq_email_per_site' => 1,
    );
    $result = civicrm_api('setting', 'get', $params);
    $description = "shows getting a variable for a current domain";
    $this->documentMe($params, $result, __FUNCTION__, __FILE__, $description, 'CreateSettingCurrentDomain');
    $this->assertAPISuccess($result, "in line " . __LINE__);
    $this->assertArrayHasKey(CRM_Core_Config::domainID(), $result['values']);
  }
}

