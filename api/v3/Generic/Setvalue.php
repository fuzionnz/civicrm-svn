<?php
/** 
 * params must contain at least id=xx & {one of the fields from getfields}=value
*/
function civicrm_api3_generic_setValue($apiRequest) {
  $entity = $apiRequest['entity'];
  $params = $apiRequest['params'];
  civicrm_api3_verify_mandatory($params, NULL, array('id','field','value'));// we can't use _spec, doesn't work with generic
  $id=$params['id'];
  if (!is_numeric ($id))
    return civicrm_api3_create_error(ts( 'Please enter a number' ), array ('error_code'=> 'NaN','field'=>"id"));

  $field=CRM_Utils_String::munge($params['field']);
  $value=$params['value'];

  $fields = civicrm_api ($entity,'getFields',array ("version"=>3,"sequential"));
  if ($fields['is_error'])
    return $fields; // getfields error, shouldn't happen. 
  $fields=$fields['values'];
  
  if (!array_key_exists ($field,$fields)) {
    return civicrm_api3_create_error("Param 'field' ($field) is invalid. must be an existing field",array ("fields"=>array_keys($fields)));
  }
   
  $def=$fields[$field];
  switch ($def['type']){
    case 1://int
      if (!is_numeric ($value))
        return civicrm_api3_create_error("Param '$field' must be a number",array('error_code'=>'NaN'));
      break;
    case 2://string
      require_once ("CRM/Utils/Rule.php");
      if (!CRM_Utils_Rule::xssString( $value ))
        return civicrm_api3_create_error(ts( 'Illegal characters in input (potential scripting attack)' ), array ('error_code'=> 'XSS'));
      if ($def['maxlength'])
        $value = substr ($value,0,$def['maxlength']);
      break;
    case 16://boolean
      $value = (boolean) $value;
      break;
    case 4://date
    default:
      return civicrm_api3_create_error("Param '$field' is of a type not managed yet. Join the API team and help us implement it");
  }

  if (CRM_Core_DAO::setFieldValue(_civicrm_api3_get_DAO($entity),$id,$field,$value)) {
    $entity=array ('id'=>$id,$field=>$value);
    CRM_Utils_Hook::post( 'edit', $entity, $id, $entity );
    return civicrm_api3_create_success($entity);
  } else {
    return civicrm_api3_create_error("error assigning $field=$value for $entity (id=$id)");
  }
}