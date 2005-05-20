<?php

/*******************************************************
 * This class generates data for the schema located in Contact.sql
 *
 * each public method generates data for the concerned table.
 * so for example the addContactDomain method generates and adds
 * data to the contact_domain table
 *
 * Data generation is a bit tricky since the data generated
 * randomly in one table could be used as a FKEY in another
 * table.
 *
 * In order to ensure that a randomly generated FKEY matches
 * a field in the referened table, the field in the referenced
 * table is always generated linearly.
 *
 *
 *
 *
 * Some numbers
 *
 * Domain ID's - 1 to NUM_DOMAIN
 *
 * Context - 3/domain
 *
 * Contact - 1 to NUM_CONTACT
 *           75% - Individual
 *           15% - Household
 *           10% - Organization
 *
 *           Contact to Domain distribution should be equal.
 *
 *
 * Contact Individual = 1 to 0.75*NUM_CONTACT
 *
 * Contact Household = 0.75*NUM_CONTACT to 0.9*NUM_CONTACT
 *
 * Contact Organization = 0.9*NUM_CONTACT to NUM_CONTACT
 *
 * Contact Location = 15% for Households, 10% for Organizations, (75-(15*4))% for Individuals.
 *                     (Assumption is that each household contains 4 individuals)
 *
 *******************************************************/

/*******************************************************
 *
 * Note: implication of using of mt_srand(1) in constructor
 * The data generated will be done in a consistent manner
 * so as to give the same data during each run (but this
 * would involve populating the entire db at one go - since
 * mt_srand(1) is in the constructor, if one needs to be able
 * to get consistent random numbers then the mt_srand(1) shld
 * be in each function that adds data to each table.
 *
 *******************************************************/


require_once '../modules/config.inc.php';
require_once 'CRM/Core/Config.php';
require_once 'CRM/Core/Error.php';
require_once 'CRM/Core/I18n.php';

class CRM_GCD {

    /*******************************************************
     * constants
     *******************************************************/
    const DATA_FILENAME="sample_data.xml";

    const NUM_DOMAIN = 10;
    const NUM_CONTACT = 100;

    const INDIVIDUAL_PERCENT = 75;
    const HOUSEHOLD_PERCENT = 15;
    const ORGANIZATION_PERCENT = 10;
    const NUM_INDIVIDUAL_PER_HOUSEHOLD = 4;


    // relationship types from the table crm_relationship_type
    const CHILD_OF            = 1;
    const SPOUSE_OF           = 2;
    const SIBLING_OF          = 3;
    const HEAD_OF_HOUSEHOLD   = 6;
    const MEMBER_OF_HOUSEHOLD = 7;


    // location types from the table crm_location_type
    const HOME            = 1;
    const WORK            = 2;
    const MAIN            = 3;
    const OTHER           = 4;
    
    const ADD_TO_DB=TRUE;
    //const ADD_TO_DB=FALSE;
    const DEBUG_LEVEL=1;

    
    /*********************************
     * private members
     *********************************/
    
    // enum's from database
    private $preferredCommunicationMethod = array('Phone', 'Email', 'Post');
    private $greetingType = array('Formal', 'Informal', 'Honorific', 'Custom', 'Other');
    private $contactType = array('Individual', 'Household', 'Organization');
    private $gender = array('Female', 'Male', 'Transgender');    
    private $phoneType = array('Phone', 'Mobile', 'Fax', 'Pager');    

    // almost enums
    private $prefix = array('Mr', 'Mrs', 'Ms', 'Dr');
    private $suffix = array('Jr', 'Sr');

    // store domain id's
    private $domain = array();

    // store contact id's
    private $contact = array();
    private $individual = array();
    private $household = array();
    private $organization = array();
    

    // store names, firstnames, street 1, street2
    private $firstName = array();
    private $lastName = array();
    private $streetName = array();
    private $supplementalAddress1 = array();
    private $city = array();
    private $state = array();
    private $country = array();
    private $addressDirection = array();
    private $streetType = array();
    private $emailDomain = array();
    private $emailTLD = array();
    private $organizationName = array();
    private $organizationField = array();
    private $organizationType = array();
    private $group = array();
    private $note = array();
    
    // stores the strict individual id and household id to individual id mapping
    private $strictIndividual = array();
    private $householdIndividual = array();
    
    // sample data in xml format
    private $sampleData = NULL;
    
    // private vars
    private $numIndividual = 0;
    private $numHousehold = 0;
    private $numOrganization = 0;
    private $numStrictIndividual = 0;

    private $CSC = array(
                         1228 => array( // united states
                                       1004 => array ('San Francisco', 'Los Angeles', 'Palo Alto'), // california
                                       1031 => array ('New York', 'Albany'), // new york
                                       ),
                         1101 => array( // india
                                       1113 => array ('Mumbai', 'Pune', 'Nasik'), // maharashtra
                                       1114 => array ('Bangalore', 'Mangalore', 'Udipi'), // karnataka
                                       ),
                         1172 => array( // poland
                                       1115 => array ('Warszawa', 'Plock'), // Mazowieckie
                                       1116 => array ('Gdansk', 'Gdynia'), // Pomorskie 
                                       ),
                         );
    
  /*********************************
   * private methods
   *********************************/

    // get a randomly generated string
    private function _getRandomString($size=32)
    {
        $string = "";

        // get an ascii code for each character
        for($i=0; $i<$size; $i++) {
            $random_int = mt_rand(65,122);
            if(($random_int<97) && ($random_int>90)) {
                // if ascii code between 90 and 97 substitute with space
                $random_int=32;
            }
            $random_char=chr($random_int);
            $string .= $random_char;
        }
        return $string;
    }

    private function _getRandomChar()
    {
        return chr(mt_rand(65, 90));
    }        

    private function getRandomBoolean()
    {
        return mt_rand(0,1);

    }

    private function _getRandomElement(&$array1)
    {
        return $array1[mt_rand(1, count($array1))-1];
    }
    
    // country state city combo
    private function _getRandomCSC()
    {
        $array1 = array();

        $c = array_rand($this->CSC);

        // the state array now
        $s = array_rand($this->CSC[$c]);

        // the city
        $ci = array_rand($this->CSC[$c][$s]);
        $city = $this->CSC[$c][$s][$ci];

        $array1[] = $c;
        $array1[] = $s;
        $array1[] = $city;

        return $array1;
    }



    /**
     * Generate a random date. 
     *
     *   If both $startDate and $endDate are defined generate
     *   date between them.
     *
     *   If only startDate is specified then date generated is
     *   between startDate + 1 year.
     *
     *   if only endDate is specified then date generated is
     *   between endDate - 1 year.
     *
     *   if none are specified - date is between today - 1year 
     *   and today
     *
     * @param  int $startDate Start Date in Unix timestamp
     * @param  int $endDate   End Date in Unix timestamp
     * @access private
     * @return string randomly generated date in the format "Ymd"
     *
     */
    private function _getRandomDate($startDate=0, $endDate=0)
    {
        
        // number of seconds per year
        $numSecond = 31536000;
        $dateFormat = "Ymd";
        $today = time();

        // both are defined
        if ($startDate && $endDate) {
            return date($dateFormat, mt_rand($startDate, $endDate));
        }

        // only startDate is defined
        if ($startDate) {
            // $nextYear = mktime(0, 0, 0, date("m", $startDate),   date("d", $startDate),   date("Y")+1);
            return date($dateFormat, mt_rand($startDate, $startDate+$numSecond));
        }

        // only endDate is defined
        if ($startDate) {
            return date($dateFormat, mt_rand($endDate-$numSecond, $endDate));
        }        
        
        // none are defined
        return date($dateFormat, mt_rand($today-$numSecond, $today));
    }


    // insert data into db's
    private function _insert($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->insert()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }

    // update data into db's
    private function _update($dao)
    {
        if (self::ADD_TO_DB) {
            if (!$dao->update()) {
                echo mysql_error() . "\n";
                exit(1);
            }
        }
    }


    /**
     * Insert a note 
     *
     *   Helper function which randomly populates "note" and 
     *   "date_modified" and inserts it.
     *
     * @param  CRM_DAO_Note DAO object for Note
     * @access private
     * @return none
     *
     */
    private function _insertNote($note) {
        $note->note = $this->_getRandomElement($this->note);
        $note->modified_date = $this->_getRandomDate();                
        $this->_insert($note);        
    }


    /*******************************************************
     *
     * Start of public functions
     *
     *******************************************************/
    // constructor
    function __construct()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        // initialize all the vars
        $this->numIndividual = self::INDIVIDUAL_PERCENT * self::NUM_CONTACT / 100;
        $this->numHousehold = self::HOUSEHOLD_PERCENT * self::NUM_CONTACT / 100;
        $this->numOrganization = self::ORGANIZATION_PERCENT * self::NUM_CONTACT / 100;
        $this->numStrictIndividual = $this->numIndividual - ($this->numHousehold * self::NUM_INDIVIDUAL_PER_HOUSEHOLD);


    }

    public function parseDataFile()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $sampleData = simplexml_load_file(self::DATA_FILENAME);

        // first names
        foreach ($sampleData->first_names->first_name as $first_name) {
            $this->firstName[] = trim($first_name);
        }

        // last names
        foreach ($sampleData->last_names->last_name as $last_name) {
            $this->lastName[] = trim($last_name);
        }

        //  street names
        foreach ($sampleData->street_names->street_name as $street_name) {
            $this->streetName[] = trim($street_name);
        }

        //  supplemental address 1
        foreach ($sampleData->supplemental_addresses_1->supplemental_address_1 as $supplemental_address_1) {
            $this->supplementalAddress1[] = trim($supplemental_address_1);
        }

        //  cities
        foreach ($sampleData->cities->city as $city) {
            $this->city[] = trim($city);
        }

        //  address directions
        foreach ($sampleData->address_directions->address_direction as $address_direction) {
            $this->addressDirection[] = trim($address_direction);
        }

        // street types
        foreach ($sampleData->street_types->street_type as $street_type) {
            $this->streetType[] = trim($street_type);
        }

        // email domains
        foreach ($sampleData->email_domains->email_domain as $email_domain) {
            $this->emailDomain[] = trim($email_domain);
        }

        // email top level domain
        foreach ($sampleData->email_tlds->email_tld as $email_tld) {
            $this->emailTLD[] = trim($email_tld);
        }

        // organization name
        foreach ($sampleData->organization_names->organization_name as $organization_name) {
            $this->organization_name[] = trim($organization_name);
        }

        // organization field
        foreach ($sampleData->organization_fields->organization_field as $organization_field) {
            $this->organizationField[] = trim($organization_field);
        }

        // organization type
        foreach ($sampleData->organization_types->organization_type as $organization_type) {
            $this->organizationType[] = trim($organization_type);
        }

        // group
        foreach ($sampleData->groups->group as $group) {
            $this->group[] = trim($group);
        }

        // notes
        foreach ($sampleData->notes->note as $note) {
            $this->note[] = trim($note);
        }
    }

    public function getContactType($id)
    {
        if(in_array($id, $this->individual))
            return 'Individual';
        if(in_array($id, $this->household))
            return 'Household';
        if(in_array($id, $this->organization))
            return 'Organization';
    }


    public function initDB()
    {
        $config = CRM_Core_Config::singleton();
    }


    /*******************************************************
     *
     * this function creates arrays for the following
     *
     * domain id
     * contact id
     * contact_location id
     * contact_contact_location id
     * contact_email uuid
     * contact_phone_uuid
     * contact_instant_message uuid
     * contact_relationship uuid
     * contact_task uuid
     * contact_note uuid
     *
     *******************************************************/
    public function initID()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        // may use this function in future if needed to get
        // a consistent pattern of random numbers.

        // get the domain and contact id arrays
        $this->domain = range(1, self::NUM_DOMAIN);
        shuffle($this->domain);
        $this->contact = range(1, self::NUM_CONTACT);
        shuffle($this->contact);

        // get the individual, household  and organizaton contacts
        $offset = 0;
        $this->individual = array_slice($this->contact, $offset, $this->numIndividual);
        $offset += $this->numIndividual;
        $this->household = array_slice($this->contact, $offset, $this->numHousehold);
        $offset += $this->numHousehold;
        $this->organization = array_slice($this->contact, $offset, $this->numOrganization);

        // get the strict individual contacts (i.e individual contacts not belonging to any household)
        $this->strictIndividual = array_slice($this->individual, 0, $this->numStrictIndividual);
        
        // get the household to individual mapping array
        $this->householdIndividual = array_diff($this->individual, $this->strictIndividual);
        $this->householdIndividual = array_chunk($this->householdIndividual, self::NUM_INDIVIDUAL_PER_HOUSEHOLD);
        $this->householdIndividual = array_combine($this->household, $this->householdIndividual);
    }


    /*******************************************************
     *
     * addDomain()
     *
     * This method adds NUM_DOMAIN domains and then adds NUM_REVISION
     * revisions for each domain with the latest revision being the last one..
     *
     *******************************************************/
    public function addDomain()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $domain = new CRM_Core_DAO_Domain();
        for ($id=2; $id<=self::NUM_DOMAIN; $id++) {
            // domain name is pretty simple. it is "Domain $id"
            $domain->name = "Domain $id";
            $domain->description = "Description $id";
            
            // insert domain
            $this->_insert($domain);
        }
    }

    /*******************************************************
     *
     * addContact()
     *
     * This method adds data to the contact table
     *
     * id - from $contact
     * domain_id (fkey into domain) (random - 1 to num_domain)
     * contact_type 'Individual' 'Household' 'Organization'
     * preferred_communication (random 1 to 3)
     *
     *******************************************************/
    public function addContact()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        // add contacts
        $contact = new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=self::NUM_CONTACT; $id++) {
            $contact->domain_id = $this->_getRandomElement($this->domain);
            $contact->contact_type = $this->getContactType($id);
            $contact->do_not_phone = mt_rand(0, 1);
            $contact->do_not_email = mt_rand(0, 1);
            $contact->do_not_post = mt_rand(0, 1);
            $contact->preferred_communication_method = $this->_getRandomElement($this->preferredCommunicationMethod);
            $this->_insert($contact);
        }
    }


    /*******************************************************
     *
     * addIndividual()
     *
     * This method adds data to the contact_individual table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - individual
     * contact_rid - latest one
     * first_name 'First Name $contact_uuid'
     * middle_name 'Middle Name $contact_uuid'
     * last_name 'Last Name $contact_uuid'
     * job_title 'Job Title $contact_uuid'
     * greeting_type - randomly select from the enum values
     * custom_greeting - "custom greeting $contact_uuid'
     *
     *******************************************************/
    public function addIndividual()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $individual = new CRM_Contact_DAO_Individual();
        $contact = new CRM_Contact_DAO_Contact();

        for ($id=1; $id<=$this->numIndividual; $id++) {
            $individual->contact_id = $this->individual[($id-1)];
            $individual->first_name = ucfirst($this->_getRandomElement($this->firstName));
            $individual->middle_name = ucfirst($this->_getRandomChar());
            $individual->last_name = ucfirst($this->_getRandomElement($this->lastName));
            $individual->prefix = $this->_getRandomElement($this->prefix);
            $individual->suffix = $this->_getRandomElement($this->suffix);
            $individual->display_name = "$individual->first_name $individual->middle_name $individual->last_name";
            $individual->greeting_type = $this->_getRandomElement($this->greetingType);
            $individual->gender = $this->_getRandomElement($this->gender);
            //$individual->birth_date = date("Y-m-d", mt_rand(0, time()));
            // there's some bug or irrational logic in DB_DataObject hence the above iso format does not work
            $individual->birth_date = date("Ymd", mt_rand(0, time()));
            $individual->is_deceased = mt_rand(0, 1);
            // $individual->phone_to_household_id = mt_rand(0, 1);
            // $individual->email_to_household_id = mt_rand(0, 1);
            // $individual->mail_to_household_id = mt_rand(0, 1);
            $this->_insert($individual);

            // also update the sort name for the contact id.
            $contact->id = $individual->contact_id;
            $contact->sort_name = $individual->last_name . ' ' . $individual->first_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);
        }
    }


    /*******************************************************
     *
     * addHousehold()
     *
     * This method adds data to the contact_household table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - household_individual
     * contact_rid - latest one
     * household_name 'household $contact_uuid primary contact $primary_contact_uuid'
     * nick_name 'nick $contact_uuid'
     * primary_contact_uuid = $household_individual[$contact_uuid][0];
     *
     *******************************************************/
    public function addHousehold()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $household = new CRM_Contact_DAO_Household();
        $contact = new CRM_Contact_DAO_Contact();
        
        for ($id=1; $id<=$this->numHousehold; $id++) {
            $household->contact_id = $this->household[($id-1)];
            $household->primary_contact_id = $this->householdIndividual[$household->contact_id][0];

            // get the last name of the primary contact id
            $individual = new CRM_Contact_DAO_Individual();
            $individual->contact_id = $household->primary_contact_id;
            $individual->find(true);
            $firstName = $individual->first_name;
            $lastName = $individual->last_name;

            // need to name the household and nick name appropriately
            $household->household_name = "$firstName $lastName" . "'s home";
            $household->nick_name = "$lastName" . "'s home";
            $this->_insert($household);

            // need to update the sort name for the main contact table
            $contact->id = $household->contact_id;
            $contact->sort_name = $household->household_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);
        }
    }



    /*******************************************************
     *
     * addOrganization()
     *
     * This method adds data to the contact_organization table
     *
     * The following fields are generated and added.
     *
     * contact_uuid - organization
     * contact_rid - latest one
     * organization_name 'organization $contact_uuid'
     * legal_name 'legal  $contact_uuid'
     * nick_name 'nick $contact_uuid'
     * sic_code 'sic $contact_uuid'
     * primary_contact_id - random individual contact uuid
     *
     *******************************************************/
    public function addOrganization()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $organization = new CRM_Contact_DAO_Organization();
        $contact = new CRM_Contact_DAO_Contact();       

        for ($id=1; $id<=$this->numOrganization; $id++) {
            $organization->contact_id = $this->organization[($id-1)];
            $name = $this->_getRandomElement($this->organization_name) . " " . $this->_getRandomElement($this->organization_field) . " " . $this->_getRandomElement($this->organization_type);
            $organization->organization_name = $name;
            $organization->primary_contact_id = $this->_getRandomElement($this->strict_individual);
            $this->_insert($organization);

            // need to update the sort name for the main contact table
            $contact->id = $organization->contact_id;
            $contact->sort_name = $organization->organization_name;
            $contact->hash = crc32($contact->sort_name);
            $this->_update($contact);
        }
    }


    /*******************************************************
     *
     * addRelationship()
     *
     * This method adds data to the contact_relationship table
     *
     * it adds the following fields
     *
     *******************************************************/
    public function addRelationship()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $relationship = new CRM_Contact_DAO_Relationship();

        $relationship->is_active = 1; // all active for now.

        foreach ($this->householdIndividual as $household_id => $household_member) {
            // add child_of relationship
            // 2 for each child
            $relationship->relationship_type_id = self::CHILD_OF;
            $relationship->contact_id_a = $household_member[2];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[2];
            $relationship->contact_id_b = $household_member[1];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[1];
            $this->_insert($relationship);

            // add spouse_of relationship 1 for both the spouses
            $relationship->relationship_type_id = self::SPOUSE_OF;
            $relationship->contact_id_a = $household_member[1];
            $relationship->contact_id_b = $household_member[0];
            $this->_insert($relationship);

            // add sibling_of relationship 1 for both the siblings
            $relationship->relationship_type_id = self::SIBLING_OF;
            $relationship->contact_id_a = $household_member[3];
            $relationship->contact_id_b = $household_member[2];
            $this->_insert($relationship);

            // add head_of_household relationship 1 for head of house
            $relationship->relationship_type_id = self::HEAD_OF_HOUSEHOLD;
            $relationship->contact_id_a = $household_member[0];
            $relationship->contact_id_b = $household_id;
            $this->_insert($relationship);

            // add member_of_household relationship 3 for all other members
            $relationship->relationship_type_id = self::MEMBER_OF_HOUSEHOLD;
            $relationship->contact_id_a = $household_member[1];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[2];
            $this->_insert($relationship);
            $relationship->contact_id_a = $household_member[3];
            $this->_insert($relationship);
        }
    }


    /*******************************************************
     *
     * addLocation()
     *
     * This method adds data to the location table
     *
     *******************************************************/
    public function addLocation()
    {
        CRM_Core_Error::le_method();


//         CRM_Core_Error::debug_var('household', $this->household);
//         CRM_Core_Error::debug_var('organization', $this->organization);

        // strict individuals
        foreach ($this->strictIndividual as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }
        
        //household
        foreach ($this->household as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }
        
        //organization
        foreach ($this->organization as $contactId) {
            $this->_addLocation(self::MAIN, $contactId);
        }

        // some individuals.
        $someIndividual = array_diff($this->individual, $this->strictIndividual);
        $someIndividual = array_slice($someIndividual, 0, (int)(75*($this->numIndividual-$this->numStrictIndividual)/100));
        foreach ($someIndividual as $contactId) {
            $this->_addLocation(self::HOME, $contactId);
        }

        CRM_Core_Error::ll_method();
    }

    private function _addLocation($locationType, $contactId)
    {

        CRM_Core_Error::le_method();

        $locationDAO = new CRM_Contact_DAO_Location();

        $locationDAO->is_primary = 1; // primary location for now
        $locationDAO->location_type_id = $locationType;
        $locationDAO->contact_id = $contactId;

        $this->_insert($locationDAO);
        $this->_addAddress($locationDAO->id);        

        // add two phones for each location
        $this->_addPhone($locationDAO->id, 'Phone', true);
        $this->_addPhone($locationDAO->id, 'Mobile', false);

        // need to get sort name to generate email id
        $contact = new CRM_Contact_DAO_Contact();
        $contact->id = $locationDAO->contact_id;
        $contact->find(true);
        // get the sort name of the contact
        $sortName  = $contact->sort_name;

        // add 2 email for each location
        for ($emailId=1; $emailId<=2; $emailId++) {
            $this->_addEmail($locationDAO->id, $sortName, ($emailId == 1));
        }

        CRM_Core_Error::ll_method();
    }

    private function _addAddress($locationId)
    {

        CRM_Core_Error::le_method();
        $addressDAO = new CRM_Contact_DAO_Address();

        // add addresses now currently we are adding only 1 address for each location
        $addressDAO->location_id = $locationId;


        if ($locationId % 5) {
            $addressDAO->street_number = mt_rand(1, 1000);
            $addressDAO->street_number_suffix = ucfirst($this->_getRandomChar());
            $addressDAO->street_number_predirectional = $this->_getRandomElement($this->addressDirection);
            $addressDAO->street_name = ucwords($this->_getRandomElement($this->streetName));
            $addressDAO->street_type = $this->_getRandomElement($this->streetType);
            $addressDAO->street_number_postdirectional = $this->_getRandomElement($this->addressDirection);
            $addressDAO->street_address = $addressDAO->street_number_predirectional . " " . $addressDAO->street_number .  $addressDAO->street_number_suffix .  " " . $addressDAO->street_name .  " " . $addressDAO->street_type . " " . $addressDAO->street_number_postdirectional;
            $addressDAO->supplemental_address_1 = ucwords($this->_getRandomElement($this->supplementalAddress1));
        }
        
        // lets do some good skips
        if ($locationId % 9) {
            $addressDAO->postal_code = mt_rand(400001, 499999);
        }

        
        // some more random skips
        if ($locationId % 7) {
            $array1 = $this->_getRandomCSC();
            $addressDAO->city = $array1[2];
            $addressDAO->state_province_id = $array1[1];
            $addressDAO->country_id = $array1[0];
        }        

        $addressDAO->county_id = 1;
        $addressDAO->geo_coord_id = 1;
        
        $this->_insert($addressDAO);

        CRM_Core_Error::ll_method();
    }

    private function _sortNameToEmail($sortName)
    {
        $sortName = strtolower(str_replace(" ", "", $sortName));
        $sortName = strtolower(str_replace(",", "_", $sortName));
        $sortName = strtolower(str_replace("'s", "_", $sortName));
        return $sortName;
    }

    private function _addPhone($locationId, $phoneType, $primary=false)
    {
        CRM_Core_Error::le_method();
        if ($locationId % 3) {
            $phone = new CRM_Contact_DAO_Phone();
            $phone->location_id = $locationId;
            $phone->is_primary = $primary;
            $phone->phone = mt_rand(11111111, 99999999);
            $phone->phone_type = $phoneType;
            $this->_insert($phone);
        }
        CRM_Core_Error::ll_method();
    }

    private function _addEmail($locationId, $sortName, $primary=false)
    {
        CRM_Core_Error::le_method();
        if ($locationId % 7) {
            $email = new CRM_Contact_DAO_Email();
            $email->location_id = $locationId;
            $email->is_primary = $primary;
            
            $emailName = $this->_sortNameToEmail($sortName);
            $emailDomain = $this->_getRandomElement($this->emailDomain);
            $tld = $this->_getRandomElement($this->emailTLD);
            $email->email = $emailName . "@" . $emailDomain . "." . $tld;
            $this->_insert($email);
        }
        CRM_Core_Error::ll_method();

    }


    /*******************************************************
     *
     * addCategoryEntity()
     *
     * This method populates the crm_entity_category table
     *
     *******************************************************/
    public function addEntityCategory()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $entity_category = new CRM_Contact_DAO_EntityCategory();
        
        // add categories 1,2,3 for Organizations.
        for ($i=0; $i<$this->numOrganization; $i+=2) {
            $org_id = $this->organization[$i];
            // echo "org_id = $org_id\n";
            $entity_category->entity_table = 'crm_contact';
            $entity_category->entity_id = $this->organization[$i];
            $entity_category->category_id = mt_rand(1, 3);
            $this->_insert($entity_category);
        }

        // add categories 4,5 for Individuals.        
        for ($i=0; $i<$this->numIndividual; $i+=2) {
            $entity_category->entity_table = 'crm_contact';
            $entity_category->entity_id = $this->individual[$i];
            if(($entity_category->entity_id)%3) {
                $entity_category->category_id = mt_rand(4, 5);
                $this->_insert($entity_category);
            } else {
                // some of the individuals are in both categories (4 and 5).
                $entity_category->category_id = 4;
                $this->_insert($entity_category);                
                $entity_category->category_id = 5;
                $this->_insert($entity_category);                
            }
        }
    }

    /*******************************************************
     *
     * addGroup()
     *
     * This method populates the crm_entity_category table
     *
     *******************************************************/
    public function addGroup()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $group = new CRM_Contact_DAO_Group();

        
        // add the 3 groups first
        $numGroup = count($this->group);

        for ($i=0; $i<$numGroup; $i++) {
            $group->domain_id = $this->_getRandomElement($this->domain);
            $group->title = $this->group[$i];
            $group->type = 'static';
            $this->_insert($group);
        }


        // 60 are for newsletter
        for ($i=0; $i<60; $i++) {
            $groupContact = new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 1; // newsletter subscribers
            $groupContact->contact_id = $this->individual[$i];
            $this->_setGroupContactStatus($groupContact);
            $this->_insert($groupContact);
        }

        // 15 volunteers
        for ($i=0; $i<15; $i++) {
            $groupContact = new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 2; // Volunteers
            $groupContact->contact_id = $this->individual[$i+60];
            $this->_setGroupContactStatus($groupContact);
            $this->_insert($groupContact);
        }

        // 8 advisory board group
        for ($i=0; $i<8; $i++) {
            $groupContact = new CRM_Contact_DAO_GroupContact();
            $groupContact->group_id = 3; // advisory board group
            $groupContact->contact_id = $this->individual[$i*7];
            $this->_setGroupContactStatus($groupContact);
            $this->_insert($groupContact);
        }
    }


    private function _setGroupContactStatus($groupContact)
    {

        // clear existing fields for DAO
        if ($groupContact->contact_id % 7) {
            $groupContact->status = "In";
            $groupContact->in_date = $this->_getRandomDate();
            $groupContact->in_method = 'Admin';
        } else {
            $groupContact->status = "Out";
            $groupContact->out_date = $this->_getRandomDate();
            $groupContact->in_date = $this->_getRandomDate(0, strtotime($groupContact->out_date));
            $groupContact->out_method = 'Admin';
        }
        if (!$groupContact->contact_id % 13) {
            unset($groupContact->in_date);
            unset($groupContact->in_method);
            $groupContact->status = "Pending";
            $groupContact->pending_date = $this->_getRandomDate();
            $groupContact->pending_method = 'Admin';
        }
        return;
    }


    /*******************************************************
     *
     * addNote()
     *
     * This method populates the crm_note table
     *
     *******************************************************/
    public function addNote()
    {

        CRM_Core_Error::le_method();
        CRM_Core_Error::ll_method();

        $note = new CRM_Core_DAO_Note();
        $note->table_name = 'crm_contact';
        // $note->table_id = 1;
        $note->contact_id = 1;

        for ($i=0; $i<self::NUM_CONTACT; $i++) {
            // $note->contact_id = $this->contact[$i];
            $note->table_id = $this->contact[$i];
            if ($this->contact[$i] % 5) {
                $this->_insertNote($note);
            }
            if ($this->contact[$i] % 3) {
                $this->_insertNote($note);
            }            
            if ($this->contact[$i] % 2) {
                $this->_insertNote($note);
            }            
        }
    }
}

echo("Starting data generation on " . date("F dS h:i:s A") . "\n");
$obj1 = new CRM_GCD();
$obj1->initID();
$obj1->parseDataFile();
$obj1->initDB();
$obj1->addDomain();
$obj1->addContact();
$obj1->addIndividual();
$obj1->addHousehold();
$obj1->addOrganization();
$obj1->addRelationship();
$obj1->addLocation();
$obj1->addEntityCategory();
$obj1->addGroup();
$obj1->addNote();

echo("Ending data generation on " . date("F dS h:i:s A") . "\n");

?>
