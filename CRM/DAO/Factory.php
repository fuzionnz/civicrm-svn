<?php

require_once 'CRM/Array.php';

class CRM_DAO_Factory {

    static $_classes = array (
                              'Domain'         => 'data',

                              'Country'        => 'singleton',
                              'County'         => 'singleton',
                              'StateProvince'  => 'singleton',
                              'GeoCoord'       => 'singleton',
                              'IMProvider'     => 'singleton',
                              'MobileProvider' => 'singleton',
                             );

    static $_prefix = array(
                           'business'  =>  'CRM/BAO/',
                           'data'      =>  'CRM/DAO/',
                           );
    
    static $_suffix = '.php';
    
    static $_preCall = array(
                             'singleton'  =>  '',
                             'business'  =>  'new',
                             'data'      =>  'new',
                             );
    
    static $_extCall = array(
                             'singleton'  =>  '::singleton',
                             'business'  =>  '',
                             'data'      =>  '',
                             );
    

    static function &create ( $className ) {
      $type = CRM_Array::value( $className, self::$_classes );
      if ( ! $type ) {
        CRM_Error::fatal( "class $className not found" );
      }

      $file  = self::$_prefix[$type] . $className;
      $class = str_replace( '/', '_', $file );
                
      require_once( $file . self::$_suffix );
                
      $newObj = eval( sprintf( "return %s %s%s();", 
                               self::$_preCall[$type],
                               $class,
                               self::$_extCall[$type] ) );
      
      return $newObj;
    }

}

?>
