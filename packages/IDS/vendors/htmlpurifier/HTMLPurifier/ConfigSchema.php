<?php

/**
 * Configuration definition, defines directives and their defaults.
 */
class HTMLPurifier_ConfigSchema {
    
    /**
     * Defaults of the directives and namespaces.
     * @note This shares the exact same structure as HTMLPurifier_Config::$conf
     */
    public $defaults = array();
    
    /**
     * Definition of the directives.
     */
    public $info = array();
    
    /**
     * Application-wide singleton
     */
    static protected $singleton;
    
    /**
     * Variable parser.
     */
    protected $parser;
    
    public function __construct() {
        $this->parser = new HTMLPurifier_VarParser_Flexible();
    }
    
    /**
     * Unserializes the default ConfigSchema.
     */
    public static function makeFromSerial() {
        return unserialize(file_get_contents(HTMLPURIFIER_PREFIX . '/HTMLPurifier/ConfigSchema/schema.ser'));
    }
    
    /**
     * Retrieves an instance of the application-wide configuration definition.
     */
    public static function instance($prototype = null) {
        if ($prototype !== null) {
            HTMLPurifier_ConfigSchema::$singleton = $prototype;
        } elseif (HTMLPurifier_ConfigSchema::$singleton === null || $prototype === true) {
            HTMLPurifier_ConfigSchema::$singleton = HTMLPurifier_ConfigSchema::makeFromSerial();
        }
        return HTMLPurifier_ConfigSchema::$singleton;
    }
    
    /**
     * Defines a directive for configuration
     * @warning Will fail of directive's namespace is defined.
     * @warning This method's signature is slightly different from the legacy
     *          define() static method! Beware!
     * @param $namespace Namespace the directive is in
     * @param $name Key of directive
     * @param $default Default value of directive
     * @param $type Allowed type of the directive. See
     *      HTMLPurifier_DirectiveDef::$type for allowed values
     * @param $allow_null Whether or not to allow null values
     */
    public function add($namespace, $name, $default, $type, $allow_null) {
        $default = $this->parser->parse($default, $type, $allow_null);
        $this->info[$namespace][$name] = new HTMLPurifier_ConfigDef_Directive();
        $this->info[$namespace][$name]->type = $type;
        $this->info[$namespace][$name]->allow_null = $allow_null;
        $this->defaults[$namespace][$name]   = $default;
    }
    
    /**
     * Defines a namespace for directives to be put into.
     * @warning This is slightly different from the corresponding static
     *          method.
     * @param $namespace Namespace's name
     */
    public function addNamespace($namespace) {
        $this->info[$namespace] = array();
        $this->defaults[$namespace] = array();
    }
    
    /**
     * Defines a directive value alias.
     * 
     * Directive value aliases are convenient for developers because it lets
     * them set a directive to several values and get the same result.
     * @param $namespace Directive's namespace
     * @param $name Name of Directive
     * @param $aliases Hash of aliased values to the real alias
     */
    public function addValueAliases($namespace, $name, $aliases) {
        foreach ($aliases as $alias => $real) {
            $this->info[$namespace][$name]->aliases[$alias] = $real;
        }
    }
    
    /**
     * Defines a set of allowed values for a directive.
     * @warning This is slightly different from the corresponding static
     *          method definition.
     * @param $namespace Namespace of directive
     * @param $name Name of directive
     * @param $allowed Lookup array of allowed values
     */
    public function addAllowedValues($namespace, $name, $allowed) {
        $type = $this->info[$namespace][$name]->type;
        $this->info[$namespace][$name]->allowed = $allowed;
    }
    
    /**
     * Defines a directive alias for backwards compatibility
     * @param $namespace
     * @param $name Directive that will be aliased
     * @param $new_namespace
     * @param $new_name Directive that the alias will be to
     */
    public function addAlias($namespace, $name, $new_namespace, $new_name) {
        $this->info[$namespace][$name] = new HTMLPurifier_ConfigDef_DirectiveAlias($new_namespace, $new_name);
    }
    
    // DEPRECATED METHODS
    
    /** @see HTMLPurifier_ConfigSchema->set() */
    public static function define($namespace, $name, $default, $type, $description) {
        HTMLPurifier_ConfigSchema::deprecated(__METHOD__);
        // process modifiers (OPTIMIZE!)
        $type_values = explode('/', $type, 2);
        $type = $type_values[0];
        $modifier = isset($type_values[1]) ? $type_values[1] : false;
        $allow_null = ($modifier === 'null');
        $def = HTMLPurifier_ConfigSchema::instance();
        $def->add($namespace, $name, $default, $type, $allow_null);
    }
    
    /** @see HTMLPurifier_ConfigSchema->addNamespace() */
    public static function defineNamespace($namespace, $description) {
        HTMLPurifier_ConfigSchema::deprecated(__METHOD__);
        $def = HTMLPurifier_ConfigSchema::instance();
        $def->addNamespace($namespace);
    }
    
    /** @see HTMLPurifier_ConfigSchema->addValueAliases() */
    public static function defineValueAliases($namespace, $name, $aliases) {
        HTMLPurifier_ConfigSchema::deprecated(__METHOD__);
        $def = HTMLPurifier_ConfigSchema::instance();
        $def->addValueAliases($namespace, $name, $aliases);
    }
    
    /** @see HTMLPurifier_ConfigSchema->addAllowedValues() */
    public static function defineAllowedValues($namespace, $name, $allowed_values) {
        HTMLPurifier_ConfigSchema::deprecated(__METHOD__);
        $allowed = array();
        foreach ($allowed_values as $value) {
            $allowed[$value] = true;
        }
        $def = HTMLPurifier_ConfigSchema::instance();
        $def->addAllowedValues($namespace, $name, $allowed);
    }
    
    /** @see HTMLPurifier_ConfigSchema->addAlias() */
    public static function defineAlias($namespace, $name, $new_namespace, $new_name) {
        HTMLPurifier_ConfigSchema::deprecated(__METHOD__);
        $def = HTMLPurifier_ConfigSchema::instance();
        $def->addAlias($namespace, $name, $new_namespace, $new_name);
    }
    
    /** @deprecated, use HTMLPurifier_VarParser->parse() */
    public function validate($a, $b, $c = false) {
        trigger_error("HTMLPurifier_ConfigSchema->validate deprecated, use HTMLPurifier_VarParser->parse instead", E_USER_NOTICE);
        return $this->parser->parse($a, $b, $c);
    }
    
    /**
     * Throws an E_USER_NOTICE stating that a method is deprecated.
     */
    private static function deprecated($method) {
        trigger_error("Static HTMLPurifier_ConfigSchema::$method deprecated, use add*() method instead", E_USER_NOTICE);
    }
    
}


