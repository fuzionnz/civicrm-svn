<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4.0                                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2003 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Adam Daniel <adaniel1@eesus.jnj.com>                        |
// |          Alexey Borzov <borz_off@cs.msu.su>                          |
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id: RuleRegistry.php,v 1.12 2004/02/28 15:47:22 avb Exp $

/**
* Registers rule objects and uses them for validation
*
*/
class HTML_QuickForm_RuleRegistry
{
    /**
     * Array containing references to used rules
     * @var     array
     * @access  private
     */
    var $_rules = array();


    /**
     * Returns a singleton of HTML_QuickForm_RuleRegistry
     *
     * Usually, only one RuleRegistry object is needed, this is the reason
     * why it is recommended to use this method to get the validation object. 
     *
     * @access    public
     * @static
     * @return    object    Reference to the HTML_QuickForm_RuleRegistry singleton
     */
    function &singleton()
    {
        static $obj;
        if (!isset($obj)) {
            $obj = new HTML_QuickForm_RuleRegistry();
        }
        return $obj;
    } // end func singleton

    /**
     * Registers a new validation rule
     *
     * In order to use a custom rule in your form, you need to register it
     * first. For regular expressions, one can directly use the 'regex' type
     * rule in addRule(), this is faster than registering the rule.
     *
     * Functions and methods can be registered. Use the 'function' type.
     * When registering a method, specify the class name as second parameter.
     *
     * You can also register an HTML_QuickForm_Rule subclass with its own
     * validate() method.
     *
     * @param     string    $ruleName   Name of validation rule
     * @param     string    $type       Either: 'regex', 'function' or null
     * @param     string    $data1      Name of function, regular expression or
     *                                  HTML_QuickForm_Rule object class name
     * @param     string    $data2      Object parent of above function or HTML_QuickForm_Rule file path
     * @access    public
     * @return    void
     */
    function registerRule($ruleName, $type, $data1, $data2 = null)
    {
        $type = strtolower($type);
        if ($type == 'regex') {
            // Regular expression
            $rule =& $this->getRule('regex');
            $rule->addData($ruleName, $data1);
            $GLOBALS['_HTML_QuickForm_registered_rules'][$ruleName] = $GLOBALS['_HTML_QuickForm_registered_rules']['regex'];

        } elseif ($type == 'function' || $type == 'callback') {
            // Callback function
            $rule =& $this->getRule('callback');
            $rule->addData($ruleName, $data1, $data2, 'function' == $type);
            $GLOBALS['_HTML_QuickForm_registered_rules'][$ruleName] = $GLOBALS['_HTML_QuickForm_registered_rules']['callback'];

        } elseif (is_object($data1)) {
            // An instance of HTML_QuickForm_Rule
            $this->_rules[strtolower(get_class($data1))] = $data1;
            $GLOBALS['_HTML_QuickForm_registered_rules'][$ruleName] = array(strtolower(get_class($data1)), null);

        } else {
            // Rule class name
            $GLOBALS['_HTML_QuickForm_registered_rules'][$ruleName] = array(strtolower($data1), $data2);
        }
    } // end func registerRule

    /**
     * Returns a reference to the requested rule object
     *
     * @param     string   $ruleName        Name of the requested rule
     * @access    public
     * @return    object
     */
    function &getRule($ruleName)
    {
        list($class, $path) = $GLOBALS['_HTML_QuickForm_registered_rules'][$ruleName];

        if (!isset($this->_rules[$class])) {
            if (!empty($path)) {
                include_once($path);
            }
            $this->_rules[$class] =& new $class();
        }
        $this->_rules[$class]->setName($ruleName);
        return $this->_rules[$class];
    } // end func getRule

    /**
     * Performs validation on the given values
     *
     * @param     string   $ruleName        Name of the rule to be used
     * @param     mixed    $values          Can be a scalar or an array of values 
     *                                      to be validated
     * @param     mixed    $options         Options used by the rule
     * @param     mixed    $multiple        Whether to validate an array of values altogether
     * @access    public
     * @return    mixed    true if no error found, int of valid values (when an array of values is given) or false if error
     */
    function validate($ruleName, $values, $options = null, $multiple = false)
    {
        $rule =& $this->getRule($ruleName);

        if (is_array($values) && !$multiple) {
            $result = 0;
            foreach ($values as $value) {
                if ($rule->validate($value, $options) === true) {
                    $result++;
                }
            }
            return ($result == 0) ? false : $result;
        } else {
            return $rule->validate($values, $options);
        }
    } // end func validate

    /**
     * Returns the validation test in javascript code
     *
     * @param     mixed     Element(s) the rule applies to
     * @param     string    Element name, in case $element is not array
     * @param     array     Rule data
     * @access    public
     * @return    string    JavaScript for the rule
     */
    function getValidationScript(&$element, $elementName, $ruleData)
    {
        $reset =  (isset($ruleData['reset'])) ? $ruleData['reset'] : false;
        $rule  =& $this->getRule($ruleData['type']);
        if (!is_array($element)) {
            list($jsValue, $jsReset) = $this->_getJsValue($element, $elementName, $reset, null);
        } else {
            $jsValue = "  value = new Array();\n";
            $jsReset = '';
            for ($i = 0; $i < count($element); $i++) {
                list($tmp_value, $tmp_reset) = $this->_getJsValue($element[$i], $element[$i]->getName(), $reset, $i);
                $jsValue .= "\n" . $tmp_value;
                $jsReset .= $tmp_reset;
            }
        }
        $jsField = isset($ruleData['group'])? $ruleData['group']: $elementName;
        list ($jsPrefix, $jsCheck) = $rule->getValidationScript($ruleData['format']);
        if (!isset($ruleData['howmany'])) {
            $js = $jsValue . "\n" . $jsPrefix . 
                  "  if (" . str_replace('{jsVar}', 'value', $jsCheck) . " && !errFlag['{$jsField}']) {\n" .
                  "    errFlag['{$jsField}'] = true;\n" .
                  "    _qfMsg = _qfMsg + '\\n - {$ruleData['message']}';\n" .
                  $jsReset .
                  "  }\n";
        } else {
            $js = $jsValue . "\n" . $jsPrefix . 
                  "  var res = 0;\n" .
                  "  for (var i = 0; i < value.length; i++) {\n" .
                  "    if (!(" . str_replace('{jsVar}', 'value[i]', $jsCheck) . ")) {\n" .
                  "      res++;\n" .
                  "    }\n" .
                  "  }\n" . 
                  "  if (res < {$ruleData['howmany']} && !errFlag['{$jsField}']) {\n" . 
                  "    errFlag['{$jsField}'] = true;\n" .
                  "    _qfMsg = _qfMsg + '\\n - {$ruleData['message']}';\n" .
                  $jsReset .
                  "  }\n";
        }
        return $js;
    } // end func getValidationScript


   /**
    * Returns JavaScript to get and to reset the element's value 
    * 
    * @access private
    * @param  object HTML_QuickForm_element     element being processed
    * @param  string    element's name
    * @param  bool      whether to generate JavaScript to reset the value
    * @param  integer   value's index in the array (only used for multielement rules)
    * @return array     first item is value javascript, second is reset
    */
    function _getJsValue(&$element, $elementName, $reset = false, $index = null)
    {
        $jsIndex = isset($index)? '[' . $index . ']': '';
        $tmp_reset = $reset? "    var field = frm.elements['$elementName'];\n": '';
        if (is_a($element, 'html_quickform_group')) {
            $value = "  var {$elementName}Elements = '::";
            for ($i = 0, $count = count($element->_elements); $i < $count; $i++) {
                $value .= $element->getElementName($i) . '::';
            }
            $value .=
                "';\n" .
                "  value{$jsIndex} = new Array();\n" .
                "  var valueIdx = 0;\n" .
                "  for (var i = 0; i < frm.elements.length; i++) {\n" .
                "    var _element = frm.elements[i];\n" .
                "    if ({$elementName}Elements.indexOf('::' + _element.name + '::') >= 0) {\n" . 
                "      switch (_element.type) {\n" .
                "        case 'checkbox':\n" .
                "        case 'radio':\n" .
                "          if (_element.checked) {\n" .
                "            value{$jsIndex}[valueIdx++] = _element.value;\n" .
                "          }\n" .
                "          break;\n" .
                "        case 'select':\n" .
                "          if (-1 != _element.selectedIndex) {\n" .
                "            value{$jsIndex}[valueIdx++] = _element.options[_element.selectedIndex].value;\n" .
                "          }\n" .
                "          break;\n" .
                "        default:\n" .
                "          value{$jsIndex}[valueIdx++] = _element.value;\n" .
                "      }\n" .
                "    }\n" .
                "  }\n";
            if ($reset) {
                $tmp_reset =
                    "    for (var i = 0; i < frm.elements.length; i++) {\n" .
                    "      var _element = frm.elements[i];\n" .
                    "      if ({$elementName}Elements.indexOf('::' + _element.name + '::') >= 0) {\n" . 
                    "        switch (_element.type) {\n" .
                    "          case 'checkbox':\n" .
                    "          case 'radio':\n" .
                    "            _element.checked = _element.defaultChecked;\n" .
                    "            break;\n" .
                    "          case 'select':\n" .
                    "            for (var j = 0; j < _element.options.length; j++) {\n" .
                    "              _element.options[j].selected = _element.options[j].defaultSelected;\n" .
                    "            }\n" .
                    "            break;\n" .
                    "          default:\n" .
                    "            _element.value = _element.defaultValue;\n" .
                    "        }\n" .
                    "      }\n" .
                    "    }\n";
            }

        } elseif ($element->getType() == 'select') {
            if ($element->getMultiple()) {
                $elementName .= '[]';
                $value =
                    "  value{$jsIndex} = new Array();\n" .
                    "  var valueIdx = 0;\n" .
                    "  for (var i = 0; i < frm.elements['{$elementName}'].options.length; i++) {\n" . 
                    "    if (frm.elements['{$elementName}'].options[i].selected) {\n" .
                    "      value{$jsIndex}[valueIdx++] = frm.elements['{$elementName}'].options[i].value;\n" .
                    "    }\n" .
                    "  }\n";
            } else {
                $value = "  value{$jsIndex} = frm.elements['{$elementName}'].options[frm.elements['{$elementName}'].selectedIndex].value;\n";
            }
            if ($reset) {
                $tmp_reset .= 
                    "    for (var i = 0; i < field.options.length; i++) {\n" .
                    "      field.options[i].selected = field.options[i].defaultSelected;\n" .
                    "    }\n";
            }

        } elseif ($element->getType() == 'checkbox') {
            $value = "  if (frm.elements['$elementName'].checked) {\n" .
                     "    value{$jsIndex} = '1';\n" .
                     "  } else {\n" .
                     "    value{$jsIndex} = '';\n" .
                     "  }";
            $tmp_reset .= ($reset) ? "    field.checked = field.defaultChecked;\n" : '';

        } elseif ($element->getType() == 'radio') {
            $value = "  value{$jsIndex} = '';\n" .
                     "  for (var i = 0; i < frm.elements['$elementName'].length; i++) {\n" .
                     "    if (frm.elements['$elementName'][i].checked) {\n" .
                     "      value{$jsIndex} = frm.elements['$elementName'][i].value;\n" .
                     "    }\n" .
                     "  }";
            if ($reset) {
                $tmp_reset .= "    for (var i = 0; i < field.length; i++) {\n" .
                              "      field[i].checked = field[i].defaultChecked;\n" .
                              "    }";
            }

        } else {
            $value = "  value{$jsIndex} = frm.elements['$elementName'].value;";
            $tmp_reset .= ($reset) ? "    field.value = field.defaultValue;\n" : '';
        }
        return array($value, $tmp_reset);
    }
} // end class HTML_QuickForm_RuleRegistry
?>
