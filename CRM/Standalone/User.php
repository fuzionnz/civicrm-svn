<?php

class CRM_Standalone_User {
  
  public $id;

  public $openid;
  
  public $email;
  
  public $first_name;
  
  public $last_name;
  
  public $name;
  
  public $street_address;
  
  public $city;
  
  public $postal_code;
  
  public $state_province;
  
  public $country;
  
  function __construct( $openid, $email = null, $firstName = null, $lastName = null, $streetAddr = null, $city = null, $postalCode = null, $stateProvince = null, $country = null ) {
    $this->openid = $openid;
    $this->email = $email;
    $this->first_name = $firstName;
    $this->last_name = $lastName;
    $this->name = $firstName . ' ' . $lastName;
    $this->street_address = $streetAddr;
    $this->city = $city;
    $this->postal_code = $postalCode;
    $this->state_province = $stateProvince;
    $this->country = $country;
  }
}

