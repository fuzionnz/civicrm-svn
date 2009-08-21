<?php

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'CiviTest/Contact.php';
require_once 'CiviTest/Custom.php';

class CRM_Core_BAO_CustomValueTableSetGetTest extends CiviUnitTestCase 
{
    function get_info( ) 
    {
        return array(
                     'name'        => 'Custom Value Table BAO setValues and getValues',
                     'description' => 'Test setValues and getValues Core_BAO_CustomValueTable methods.',
                     'group'       => 'CiviCRM BAO Tests',
                     );
    }
    
    function setUp( ) 
    {
        parent::setUp();
    }

    /*
     * Test setValues() method with custom Date field
     * Using sample custom field 'Marriage Date'
     *
     */
    function testSetValuesDate()
    {
        $this->markTestSkipped( 'blows up with fatal, needs fixing!' );

        $params      = array();
        $contactID   = Contact::createIndividual();

        // Retrieve the field ID for sample custom field 'Marriage Date'
        $params = array( );
        $params = array( 'label'   => 'Marriage Date');
        $field  = array( );
        
        require_once 'CRM/Core/BAO/CustomField.php';
        CRM_Core_BAO_CustomField::retrieve( $params, $field );
        $fieldID = $field['id'];

        // Set Marriage Date to a valid date value
        $date = '20080608000000';
        $params = array( 'entityID'           => $contactID,
                         'custom_' . $fieldID => $date );
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $result = CRM_Core_BAO_CustomValueTable::setValues( $params );
        $this->assertEquals( $result['is_error'], 0, 'Verify that is_error = 0 (success).');

        // CRM_Core_Error::debug('r1',$result);

        // Check that the date value is stored
        $values = array( );
        $params = array( 'entityID'           => $contactID,
                         'custom_' . $fieldID => 1);
        $values = CRM_Core_BAO_CustomValueTable::getValues( $params );

        // CRM_Core_Error::debug('v1',$values);

        $this->assertEquals( $values['is_error'], 0, 'Verify that is_error = 0 (success).');
        require_once 'CRM/Utils/Date.php';
        $this->assertEquals( $values['custom_' . $fieldID], CRM_Utils_Date::mysqlToIso($date), 'Verify that the date value is stored for contact ' . $contactID);

        // Now set Marriage Date to an invalid date value and try to reset
        $badDate = '20080631000000';
        $params   = array( 'entityID'           => $contactID,
                           'custom_' . $fieldID => $badDate );
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $result = CRM_Core_BAO_CustomValueTable::setValues( $params );
        
        // Check that the error flag is set AND that custom date value has not been modified
        $this->assertEquals( $result['is_error'], 1, 'Verify that is_error = 1 when bad date is passed.');
        
        // CRM_Core_Error::debug('r2-bad date',$result);
                
        $params = array( 'entityID'               => $contactID,
                         'custom_' . $fieldID => 1);
        $values = CRM_Core_BAO_CustomValueTable::getValues( $params );
        $this->assertEquals( $values['custom_' . $fieldID], CRM_Utils_Date::mysqlToIso($date), 'Verify that the date value has NOT been updated for contact ' . $contactID);
        
        // CRM_Core_Error::debug('v2',$values);

        // Test setting Marriage Date to null
        $params = array( 'entityID'           => $contactID,
                         'custom_' . $fieldID => null );
        require_once 'CRM/Core/BAO/CustomValueTable.php';
        $result = CRM_Core_BAO_CustomValueTable::setValues( $params );

        // CRM_Core_Error::debug('r3',$result);

        // Check that the date value is empty
        $params = array( 'entityID'           => $contactID,
                         'custom_' . $fieldID => 1);
        $values = CRM_Core_BAO_CustomValueTable::getValues( $params );
        $this->assertEquals( $values['custom_' . $fieldID], '', 'Verify that the date value is empty for contact ' . $contactID);
        $this->assertEquals( $values['is_error'], 0, 'Verify that is_error = 0 (success).');

        // CRM_Core_Error::debug('v3-empty date',$values);

        // Cleanup our contact
        Contact::delete( $contactID );
    }
}
