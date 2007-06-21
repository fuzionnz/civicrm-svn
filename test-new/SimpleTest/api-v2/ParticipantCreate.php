<?php

require_once 'api/v2/Participant.php';

class TestOfParticipantCreateAPIV2 extends CiviUnitTestCase 
{
    protected $_contactID;
    protected $_createdParticipants = array();

    
    function setUp() 
    {
        $this->_contactID = $this->individualCreate( ) ;
    }

    function tearDown()
    {
        // Cleanup all created participant records.
        foreach ( $this->_createdParticipants as $id ) {
            $result = $this->participantDelete( $id );
        }
        // Cleanup test contact
        $result = $this->contactDelete( $this->_contactID ); 
    }
    

    function testParticipantCreateMissingContactID()
    {
        $params = array(
                        'event_id'      => 2,
                        );
        $participant = & civicrm_participant_create($params);
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    function testParticipantCreateMissingEventID()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        );
        $participant = & civicrm_participant_create($params); 
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertEqual( $participant['is_error'],1 );
        $this->assertNotNull($participant['error_message']);
    }

    function testParticipantCreateEventIdOnly()
    {
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => 1,
                        );
        $participant = & civicrm_participant_create($params); 
        if ( CRM_Utils_Array::value('id', $participant) ) {
            $this->_createdParticipants[] = $participant['id'];
        }
        $this->assertNotEqual( $participant['is_error'],1 );
        
        // Use civicrm_participant_get to retrieve created record, then compare stored values.
        $params = array(
                        'participant_id' => $participant['participant_id']
                        );
        $result = &civicrm_participant_get( $params );
//        CRM_Core_Error::debug('result',$result);
        $this->assertEqual($result['event_id'],1);
        $this->assertEqual($result['participant_status_id'],1);
    }
    
    function testParticipantCreateAllParams()
    {  
        $params = array(
                        'contact_id'    => $this->_contactID,
                        'event_id'      => 2,
                        'status_id'     => 1,
                        'role_id'       => 1,
                        'register_date' => '2007-07-21',
                        'source'        => 'Online Event Registration: API Testing',
                        'event_level'   => 'Tenor'                        
                        );
        
        $participant = & civicrm_participant_create($params);
        if ( CRM_Utils_Array::value('participant_id', $participant) ) {
            $this->_createdParticipants[] = $participant['participant_id'];
        }
        $this->assertNotEqual( $participant['is_error'],1 );

        // Use civicrm_participant_get to retrieve created record, then compare stored values.
        $params = array(
                        'participant_id' => $participant['participant_id']
                        );
        $result = &civicrm_participant_get( $params );
        $this->assertEqual($result['event_id'],2);
        $this->assertEqual($result['participant_status_id'],1);
        $this->assertEqual($result['participant_role_id'],1);
        $this->assertEqual($result['participant_register_date'], '2007-07-21 00:00:00');
        $this->assertEqual($result['participant_source'],'Online Event Registration: API Testing');
        $this->assertEqual($result['event_level'],'Tenor');
    }
    
}
?>
