<?php

require_once 'api/crm.php';

class TestOfCreateEvent extends UnitTestCase 
{
    protected $_event    = array();
    protected $_event1   = array();
    protected $_event2   = array();
        
    function setUp() 
    {
    }
    
    function tearDown() 
    {
    }

    function testCreateWrongEvent()
    {
        $params = array();        

        $event = & civicrm_event_create($params);
        $this->assertEqual( $contact['is_error'], 1 );
    }
    
    function testCreateWrongEventWithoutTitle()
    {
    
        $params = array(
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & crm_create_event($params);
        $this->assertEqual( $contact['is_error'], 1 );
    
    }

    function testCreateWrongEventWithoutEventTypeId()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '1',
                        'max_participants'         => '150',
                        'is_active'                => '1' 
                        );

        $event = & crm_create_event($params);
        $this->assertEqual( $contact['is_error'], 1 );
    
    }

    function testCreateWrongEventWithoutStartDate()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'is_active'                => '1' 
                        );

        $event = & crm_create_event($params);
        $this->assertEqual( $contact['is_error'], 1 );
        
    }

    function testCreateEventWithoutSummary()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
        
        $event = & crm_create_event($params);               
    
    }
    
    function testCreateEventWithoutEndDate()
    {
    
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $event = & crm_create_event($params);
        $this->assertNotNull( $event['event_id'] );               

        $this->_event2 = $event;
    
    }

    function testCreateEvent()
    {
        
        $params = array(
                        'title'                    => 'Annual Function',
                        'summary'                  => 'Regular function',
                        'description'              => 'Award ceremony and cultural events',
                        'event_type_id'            => '3',
                        'is_public'                => '1',
                        'start_date'               => '20070219', 
                        'end_date'                 => '20071019',
                        'is_online_registration'   => '0',
                        'registration_link_text'   => 'link',
                        'max_participants'         => '150',
                        'event_full_text'          => 'efull', 
                        'is_monetary'              => '0', 
                        'contribution_type_id'     => '0', 
                        'is_map'                   => '0', 
                        'is_active'                => '1' 
                        );
	
        $event = & crm_create_event($params);  
        $this->assertNotNull( $event['event_id'] );                

        $this->_event = $event;
    }
    
    function testDeleteEvent()
    {
       
        $val = &crm_delete_event($this->_event['event_id']);
        $this->assertEqual( $val['is_error'], 0 );    
        $val1 = &crm_delete_event($this->_event1['event_id']);
        $this->assertEqual( $val['is_error'], 0 );    
        
    }
    
}
