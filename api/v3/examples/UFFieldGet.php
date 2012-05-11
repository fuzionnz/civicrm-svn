<?php



/*
 
 */
function uf_field_get_example(){
$params = array( 
  'version' => 3,
);

  require_once 'api/api.php';
  $result = civicrm_api( 'uf_field','get',$params );

  return $result;
}

/*
 * Function returns array of result expected from previous function
 */
function uf_field_get_expectedresult(){

  $expectedResult = array( 
  'is_error' => 0,
  'version' => 3,
  'count' => 25,
  'values' => array( 
      '1' => array( 
          'id' => '1',
          'uf_group_id' => '1',
          'field_name' => 'first_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '1',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => '1',
          'label' => 'First Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '2' => array( 
          'id' => '2',
          'uf_group_id' => '1',
          'field_name' => 'last_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'help_post' => 'First and last name will be shared with other visitors to the site.',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => '1',
          'label' => 'Last Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '3' => array( 
          'id' => '3',
          'uf_group_id' => '1',
          'field_name' => 'street_address',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'location_type_id' => '1',
          'label' => 'Street Address (Home)',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '4' => array( 
          'id' => '4',
          'uf_group_id' => '1',
          'field_name' => 'city',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '4',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'location_type_id' => '1',
          'label' => 'City (Home)',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '5' => array( 
          'id' => '5',
          'uf_group_id' => '1',
          'field_name' => 'postal_code',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '5',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'location_type_id' => '1',
          'label' => 'Postal Code (Home)',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '6' => array( 
          'id' => '6',
          'uf_group_id' => '1',
          'field_name' => 'country',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '6',
          'help_post' => 'Your state/province and country of residence will be shared with others so folks can find others in their community.',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => '1',
          'location_type_id' => '1',
          'label' => 'Country (Home)',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '7' => array( 
          'id' => '7',
          'uf_group_id' => '1',
          'field_name' => 'state_province',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '7',
          'visibility' => 'User and User Admin Only',
          'in_selector' => '1',
          'is_searchable' => '1',
          'location_type_id' => '1',
          'label' => 'State (Home)',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '8' => array( 
          'id' => '8',
          'uf_group_id' => '2',
          'field_name' => 'first_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '1',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'First Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '9' => array( 
          'id' => '9',
          'uf_group_id' => '2',
          'field_name' => 'last_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Last Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '10' => array( 
          'id' => '10',
          'uf_group_id' => '2',
          'field_name' => 'email',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Email Address',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '11' => array( 
          'id' => '11',
          'uf_group_id' => '3',
          'field_name' => 'participant_status',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '1',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Participant Status',
          'field_type' => 'Participant',
          'is_reserved' => '1',
        ),
      '12' => array( 
          'id' => '12',
          'uf_group_id' => '4',
          'field_name' => 'first_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '1',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'First Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '13' => array( 
          'id' => '13',
          'uf_group_id' => '4',
          'field_name' => 'last_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Last Name',
          'field_type' => 'Individual',
          'is_reserved' => 0,
        ),
      '14' => array( 
          'id' => '14',
          'uf_group_id' => '4',
          'field_name' => 'email',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Email Address',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '15' => array( 
          'id' => '15',
          'uf_group_id' => '5',
          'field_name' => 'organization_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Organization Name',
          'field_type' => 'Organization',
          'is_reserved' => 0,
        ),
      '16' => array( 
          'id' => '16',
          'uf_group_id' => '5',
          'field_name' => 'email',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Email Address',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '17' => array( 
          'id' => '17',
          'uf_group_id' => '6',
          'field_name' => 'household_name',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Household Name',
          'field_type' => 'Household',
          'is_reserved' => 0,
        ),
      '18' => array( 
          'id' => '18',
          'uf_group_id' => '6',
          'field_name' => 'email',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => 0,
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Email Address',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '19' => array( 
          'id' => '19',
          'uf_group_id' => '7',
          'field_name' => 'phone',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '1',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'location_type_id' => '1',
          'phone_type_id' => '1',
          'label' => 'Home Phone',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '20' => array( 
          'id' => '20',
          'uf_group_id' => '7',
          'field_name' => 'phone',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '2',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'location_type_id' => '1',
          'phone_type_id' => '2',
          'label' => 'Home Mobile',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '21' => array( 
          'id' => '21',
          'uf_group_id' => '7',
          'field_name' => 'street_address',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '3',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Primary Address',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '22' => array( 
          'id' => '22',
          'uf_group_id' => '7',
          'field_name' => 'city',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '4',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'City',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '23' => array( 
          'id' => '23',
          'uf_group_id' => '7',
          'field_name' => 'state_province',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '5',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'State',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '24' => array( 
          'id' => '24',
          'uf_group_id' => '7',
          'field_name' => 'postal_code',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '6',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Postal Code',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
      '25' => array( 
          'id' => '25',
          'uf_group_id' => '7',
          'field_name' => 'email',
          'is_active' => '1',
          'is_view' => 0,
          'is_required' => '1',
          'weight' => '7',
          'visibility' => 'User and User Admin Only',
          'in_selector' => 0,
          'is_searchable' => 0,
          'label' => 'Primary Email',
          'field_type' => 'Contact',
          'is_reserved' => 0,
        ),
    ),
);

  return $expectedResult  ;
}




/*
* This example has been generated from the API test suite. The test that created it is called
* 
* testGetUFFieldSuccess and can be found in 
* http://svn.civicrm.org/civicrm/branches/v3.4/tests/phpunit/CiviTest/api/v3/UFFieldTest.php
* 
* You can see the outcome of the API tests at 
* http://tests.dev.civicrm.org/trunk/results-api_v3
* and review the wiki at
* http://wiki.civicrm.org/confluence/display/CRMDOC/CiviCRM+Public+APIs
* Read more about testing here
* http://wiki.civicrm.org/confluence/display/CRM/Testing
*/