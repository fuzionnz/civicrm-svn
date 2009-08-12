<?php

require_once 'api/v2/Membership.php';
require_once 'CiviTest/CiviUnitTestCase.php';

class api_v2_MembershipTypeGetTest extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_contributionTypeID;
    function get_info( )
    {
        return array(
                     'name'        => 'MembershipType Get',
                     'description' => 'Test all Membership Type Get API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }
    
    function setUp() 
    {
        parent::setUp();

        $this->_contactID           = $this->organizationCreate( ) ;
        $this->_contributionTypeID  = $this->contributionTypeCreate();
    }
       
    function testMembershipTypeGetEmpty()
    {
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }
        
    function testMembershipTypeGetWithoutId()
    {
        $params = array(
                        'name'                 => '60+ Membership',
                        'description'          => 'people above 60 are given health instructions',
                        'contribution_type_id' => $this->_contributionTypeID ,
                        'minimum_fee'          => '200',
                        'duration_unit'        => 'month',
                        'duration_interval'    => '10',
                        'visibility'           => 'public'
                        );
        
        $membershiptype = & civicrm_membership_types_get( $params );
        $this->assertEquals( $membershiptype['is_error'], 1 );
    }

    function testMembershipTypeGet()
    {
       
        $id = $this->membershipTypeCreate( $this->_contactID,$this->_contributionTypeID );
        $params = array( 'id'=> $id );        
        $membershiptype = & civicrm_membership_types_get( $params );
                       
        $this->assertEquals($membershiptype[$id]['name'],'General');
        $this->assertEquals($membershiptype[$id]['member_of_contact_id'],$this->_contactID);
        $this->assertEquals($membershiptype[$id]['contribution_type_id'],$this->_contributionTypeID);
        $this->assertEquals($membershiptype[$id]['duration_unit'],'year');
        $this->assertEquals($membershiptype[$id]['duration_interval'],'1');
        $this->assertEquals($membershiptype[$id]['period_type'],'rolling');
        $this->membershipTypeDelete( $membershiptype[$id]['id'] );
    }

    function tearDown() 
    {
        $this->contactDelete( $this->_contactID ) ;
        $this->contributionTypeDelete( $this->_contributionTypeID );
    }
}

