<?php

require_once 'CRM/DAO/Base.php';

class CRM_Contacts_DAO_Note extends CRM_DAO_Base {
  public $table_name;
  public $table_id;
  public $note;

  function __construct() {
    parent::__construct();
  }

  function dbFields() {
    static $fields;
    if ( $fields === null ) {
      $fields = array_merge(
			    parent::dbFields(),
			    array(
				  'table_name' => array(CRM_Type::T_STRING, self::NOT_NULL),
				  'table_id'   => array(CRM_Type::T_INT, self::NOT_NULL),
				  'note'       => array(CRM_Type::T_TEXT),
				  ) // end of array
			    );
    }
    return $fields;
  } // end of method dbFields

} // end of class CRM_Contacts_DAO_Note

?>
