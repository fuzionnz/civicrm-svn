<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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

require_once 'api/v3/GroupContact.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v3_GroupContactTest extends CiviUnitTestCase 
{
   
    protected $_contactId;
    protected $_contactId1;
    protected $_apiversion;    

    function get_info( )
    {
        return array(
                     'name'        => 'Group Contact Create',
                     'description' => 'Test all Group Contact Create API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    /*
     * Set up for group contact tests
     * 
     * @todo set up calls function that doesn't work @ the moment 
     */
    function setUp() 
    {
      $this->_apiversion =3;     
        parent::setUp();

        $this->_contactId = $this->individualCreate(null,3);

        $this->_groupId1  = $this->groupCreate( null,3);
        $params = array( 'contact_id.' => $this->_contactId,
                         'group_id'     => $this->_groupId1,
                         'version'			=> $this->_apiversion, );


      $result = civicrm_group_contact_create( $params );
        print_r($result);
        
        $group = array(
                       'name'        => 'Test Group 2',
                       'domain_id'   => 1,
                       'title'       => 'New Test Group2 Created',
                       'description' => 'New Test Group2 Created',
                       'is_active'   => 1,
                       'visibility'  => 'User and User Admin Only',
                       'version'			=> $this->_apiversion, 
                       );
        $this->_groupId2  = $this->groupCreate( $group,3 );
        $params = array( 'contact_id.1' => $this->_contactId,
                         'group_id'     =>  $this->_groupId2,
                         'version'			=> $this->_apiversion,   );
      //@todo uncomment me when I work         
      //  civicrm_group_contact_create( $params );
        
        $this->_group = array($this->_groupId1  => array( 'title'      => 'New Test Group Created',
                                                          'visibility' => 'Public Pages',
                                                          'in_method'  => 'API'),
                              $this->_groupId2  => array( 'title'      => 'New Test Group2 Created',
                                                          'visibility' => 'User and User Admin Only',
                                                          'in_method'  => 'API' ));
    }
    
    function tearDown() 
    {
    }

///////////////// civicrm_group_contact_get methods

    function testGetWithWrongParamsType()
    {
    
        $params = 1;
        $groups = civicrm_group_contact_get( $params );

        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'input parameter should be an array' );
    }

    function testGetWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_get( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }
    
    function testGet( ) 
    {
        $params = array( 'contact_id' => $this->_contactId,
                          'version'			=> $this->_apiversion, );
        $result = civicrm_group_contact_get( $params );
        $this->documentMe($params,$result,__FUNCTION__,__FILE__);                  
        foreach( $result as $v  ){ 
            $this->assertEquals( $v['title'], $this->_group[$v['group_id']]['title'] );
            $this->assertEquals( $v['visibility'], $this->_group[$v['group_id']]['visibility'] );
            $this->assertEquals( $v['in_method'], $this->_group[$v['group_id']]['in_method'] );
        }
    }
   
///////////////// civicrm_group_contact_add methods

    function testCreateWithWrongParamsType()
    {
        $params  = 1;
        $groups = civicrm_group_contact_create( $params );

        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'input parameter should be an array' );
    }
    
    function testCreateWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_create( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }

    function testCreateWithoutGroupIdParams( ) 
    {
       $params = array(
                        'contact_id.1' => $this->_contactId,
                        );
        
        $groups = civicrm_group_contact_create( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'group_id is a required field' );
    }

    function testCreateWithoutContactIdParams( )     
    {
       $params = array(
                        'group_id' => $this->_groupId1
                        );
        $groups = civicrm_group_contact_create( $params );
        
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );        
    }
    
    function testCreate( ) 
    {
      $cont = array( 'first_name'       => 'Amiteshwar',
                       'middle_name'      => 'L.',
                       'last_name'        => 'Prasad',
                       'prefix_id'        => 3,
                       'suffix_id'        => 3,
                       'email'            => 'amiteshwar.prasad@civicrm.org',
                       'contact_type'     => 'Individual',
                       'version'			=> $this->_apiversion, );
        
        $this->_contactId1 = $this->individualCreate( $cont, $this->_apiversion );
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        'contact_id.2' => $this->_contactId1,
                        'group_id'     => $this->_groupId1,
                        'version'			=> $this->_apiversion, );
        
        $result = civicrm_group_contact_create( $params );
        $this->documentMe($params,$result,__FUNCTION__,__FILE__);        
        $this->assertEquals( $result ['is_error'], 0 );
        $this->assertEquals( $result ['not_added'], 1 );
        $this->assertEquals( $result ['added'], 1 );
        $this->assertEquals( $result ['total_count'], 2 );
        
    }

///////////////// civicrm_group_contact_remove methods

    function testRemoveWithWrongParamsType()
    {
        $params  = 1;
        $groups = civicrm_group_contact_delete( $params );

        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'input parameter should be an array' );
    }
    
    function testRemoveWithEmptyParams( ) 
    {
        $params = array( );
        $groups = civicrm_group_contact_delete( $params );
       
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }

    function testRemoveWithoutGroupIdParams( ) 
    {
        $params = array( );
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        );
        
        $groups = civicrm_group_contact_delete( $params );
              
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'group_id is a required field' );
    }
    
    function testRemoveWithoutContactIdParams( ) 
    {
        $params = array( );
        $params = array(
                        'group_id' => $this->_groupId1,
                        );
        
        $groups = civicrm_group_contact_delete( $params );
              
        $this->assertEquals( $groups['is_error'], 1 );
        $this->assertEquals( $groups['error_message'], 'contact_id is a required field' );
    }    
    
    
    function testRemove( ) 
    {
        $params = array(
                        'contact_id.1' => $this->_contactId,
                        'group_id'     => 1 ,
                        'version'      =>$this->_apiversion,);
        
        
       $result = civicrm_group_contact_delete( $params );
        $this->documentMe($params,$result,__FUNCTION__,__FILE__);             
        $this->assertEquals( $result['is_error'], 0 );
        $this->assertEquals( $result['removed'], 1 );
        $this->assertEquals( $result['total_count'], 1 );

    }
  
}


