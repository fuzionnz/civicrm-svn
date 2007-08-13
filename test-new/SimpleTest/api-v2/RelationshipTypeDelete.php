<?php

require_once 'api/v2/Relationship.php';

/**
 * Class contains api test cases for "civicrm_relationship_type"
 *
 */

class TestOfRelationshipTypeDeleteAPIV2 extends CiviUnitTestCase 
{
    protected $_relTypeID;
    
    function setUp( ) 
    {

    }

    // First Create a relationship type
    function testRelationshipTypeCreate( )
    {
        $this->_relTypeID = $this->relationshipTypeCreate( );
    }
    
    /**
     * check with empty array
     */
    function testRelationshipTypeDeleteEmpty( )
    {
        $params = array( );
        $result =& civicrm_relationship_type_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
    }
    
    /**
     * check with No array
     */
    
    function testRelationshipTypeDeleteParamsNotArray( )
    {
        $params = 'name_a_b = Test1';                            
        $result =& civicrm_relationship_type_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
    }
    
    /**
     * check if required fields are not passed
     */
    function testRelationshipTypeDeleteWithoutRequired( )
    {
        $params = array(
                        'name_b_a'       => 'Relation 2',
                        'contact_type_b' => 'Individual',
                        'is_reserved'    => 0,
                        'is_active'      => 0
                        );
        
        $result =& civicrm_relationship_type_delete( $params );
        
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Missing required parameter' );
    }
    
    /**
     * check with incorrect required fields
     */
    function testRelationshipTypeDeleteWithIncorrectData( )
    {
        $params = array(
                        'id'             => 'abcd',
                        'name_b_a'       => 'Relation 2',
                        'description'    => 'Testing relationship type',
                        'contact_type_a' => 'Individual',
                        'contact_type_b' => 'Individual',
                        'is_reserved'    => 0,
                        'is_active'      => 0
                        );

        $result =& civicrm_relationship_type_delete( $params );
 
        $this->assertEqual( $result['is_error'], 1 );
        $this->assertEqual( $result['error_message'], 'Invalid value for relationship type ID' );
    }
    
    /**
     * check relationship type delete
     */
    function testRelationshipTypeDelete( )
    {
        $params = array(
                        'id'       => $this->_relTypeID,
                        'name_a_b' => 'Relation 1',
                        'name_b_a' => 'Relation 2',
                        );

        $result = & civicrm_relationship_type_delete( $params );

        $this->assertEqual( $result['is_error'], 0 );
    }
    
    function tearDown( ) 
    {
        
    }
}
?> 