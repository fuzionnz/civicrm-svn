<?php

require_once 'api/crm.php';

class TestOfUpdateUFFieldAPI extends UnitTestCase 
{
    protected $_UFGroup;
    protected $_UFField;
    
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }
    
    function testCreateUFGroup()
    {
        $params = array(
                        'title'     => 'New Profile Group F02',
                        'help_pre'  => 'Help For Profile Group F02',
                        'is_active' => 1
                        );
        $UFGroup = crm_create_uf_group($params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFGroup');
        $this->_UFGroup = $UFGroup;
    }
    
    function testCreateUFField()
    {
        $params = array(
                        'field_name' => 'middle_name',
                        'visibility' => 'Public User Pages and Listings',
                        );
        $UFField = crm_create_uf_field($this->_UFGroup, $params);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFField');
        $this->_UFField = $UFField;
    }
    
    function testUpdateUFFieldError()
    {
        $params = array();
        $UFField = crm_update_uf_field($params, $this->UFField->id);
        $this->assertIsA($UFGroup, 'CRM_Core_Error');
    }
    
    function testUpdateUFField()
    {
        $params = array(
                        'help_post' => 'Help for field added .. !!',
                        );
        $UFField = crm_update_uf_field($params, $this->UFField->id);
        $this->assertIsA($UFGroup, 'CRM_Core_BAO_UFField');
    }
    
    function testDeleteUFField()
    {
        $UFField = crm_delete_uf_field($this->_UFField);
        $this->assertNull($UFField);
    }
    
    function testDeleteUFGroup()
    {
        $UFGroup = crm_delete_uf_group($this->_UFGroup);
        $this->assertNull($UFGroup);
    }
}
?>