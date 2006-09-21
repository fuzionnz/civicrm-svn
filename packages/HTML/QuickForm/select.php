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
// |          Bertrand Mansion <bmansion@mamasam.com>                     |
// +----------------------------------------------------------------------+
//
// $Id: select.php,v 1.29 2005/07/22 17:30:51 avb Exp $

require_once('HTML/QuickForm/element.php');

/**
 * Class to dynamically create an HTML SELECT
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */
class HTML_QuickForm_select extends HTML_QuickForm_element {
    
    // {{{ properties

    /**
     * Contains the select options
     *
     * @var       array
     * @since     1.0
     * @access    private
     */
    var $_options = array();
    
    /**
     * Default values of the SELECT
     * 
     * @var       string
     * @since     1.0
     * @access    private
     */
    var $_values = null;

    // }}}
    // {{{ constructor
        
    /**
     * Class constructor
     * 
     * @param     string    Select name attribute
     * @param     mixed     Label(s) for the select
     * @param     mixed     Data to be used to populate options
     * @param     mixed     Either a typical HTML attribute string or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    function HTML_QuickForm_select($elementName=null, $elementLabel=null, $options=null, $attributes=null)
    {
        HTML_QuickForm_element::HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_type = 'select';
        if (isset($options)) {
            $this->load($options);
        }
    } //end constructor
    
    // }}}
    // {{{ apiVersion()

    /**
     * Returns the current API version 
     * 
     * @since     1.0
     * @access    public
     * @return    double
     */
    function apiVersion()
    {
        return 2.3;
    } //end func apiVersion

    // }}}
    // {{{ setSelected()

    /**
     * Sets the default values of the select box
     * 
     * @param     mixed    $values  Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setSelected($values)
    {
        if (is_string($values) && $this->getMultiple()) {
            $values = split("[ ]?,[ ]?", $values);
        }
        if (is_array($values)) {
            $this->_values = array_values($values);
        } else {
            $this->_values = array($values);
        }
    } //end func setSelected
    
    // }}}
    // {{{ getSelected()

    /**
     * Returns an array of the selected values
     * 
     * @since     1.0
     * @access    public
     * @return    array of selected values
     */
    function getSelected()
    {
        return $this->_values;
    } // end func getSelected

    // }}}
    // {{{ setName()

    /**
     * Sets the input field name
     * 
     * @param     string    $name   Input field name attribute
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setName($name)
    {
        $this->updateAttributes(array('name' => $name));
    } //end func setName
    
    // }}}
    // {{{ getName()

    /**
     * Returns the element name
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getName()
    {
        return $this->getAttribute('name');
    } //end func getName

    // }}}
    // {{{ getPrivateName()

    /**
     * Returns the element name (possibly with brackets appended)
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getPrivateName()
    {
        if ($this->getAttribute('multiple')) {
            return $this->getName() . '[]';
        } else {
            return $this->getName();
        }
    } //end func getPrivateName

    // }}}
    // {{{ setValue()

    /**
     * Sets the value of the form element
     *
     * @param     mixed    $values  Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setValue($value)
    {
        $this->setSelected($value);
    } // end func setValue

    // }}}
    // {{{ getValue()

    /**
     * Returns an array of the selected values
     * 
     * @since     1.0
     * @access    public
     * @return    array of selected values
     */
    function getValue()
    {
        return $this->_values;
    } // end func getValue

    // }}}
    // {{{ setSize()

    /**
     * Sets the select field size, only applies to 'multiple' selects
     * 
     * @param     int    $size  Size of select  field
     * @since     1.0
     * @access    public
     * @return    void
     */
    function setSize($size)
    {
        $this->updateAttributes(array('size' => $size));
    } //end func setSize
    
    // }}}
    // {{{ getSize()

    /**
     * Returns the select field size
     * 
     * @since     1.0
     * @access    public
     * @return    int
     */
    function getSize()
    {
        return $this->getAttribute('size');
    } //end func getSize

    // }}}
    // {{{ setMultiple()

    /**
     * Sets the select mutiple attribute
     * 
     * @param     bool    $multiple  Whether the select supports multi-selections
     * @since     1.2
     * @access    public
     * @return    void
     */
    function setMultiple($multiple)
    {
        if ($multiple) {
            $this->updateAttributes(array('multiple' => 'multiple'));
        } else {
            $this->removeAttribute('multiple');
        }
    } //end func setMultiple
    
    // }}}
    // {{{ getMultiple()

    /**
     * Returns the select mutiple attribute
     * 
     * @since     1.2
     * @access    public
     * @return    bool    true if multiple select, false otherwise
     */
    function getMultiple()
    {
        return (bool)$this->getAttribute('multiple');
    } //end func getMultiple

    // }}}
    // {{{ addOption()

    /**
     * Adds a new OPTION to the SELECT
     *
     * @param     string    $text       Display text for the OPTION
     * @param     string    $value      Value for the OPTION
     * @param     mixed     $attributes Either a typical HTML attribute string 
     *                                  or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
    // function addOption($text, $value, $attributes=null)
    function addOption($text, $value, $attributes=null, &$optGroup=null)
    // <OPTGROUP> MODIFICATION END
    {
        // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
        // if text is an array, start an optgroup
        if (is_array($text)) {
            if (is_array($optGroup)) {
                $optGroup[$value]['options'] = array();
                $optGroup =& $optGroup[$value]['options'];
            }
            else {
                $this->_options[$value]['options'] = array();
                $optGroup =& $this->_options[$value]['options'];
            }
            foreach($text as $key=>$val) {
                $this->addOption($val, $key, null, $optGroup);
            }
            // done all the options in the optgroup
            return;
        }
        // <OPTGROUP> MODIFICATION END

        if (null === $attributes) {
            $attributes = array('value' => $value);
        } else {
            $attributes = $this->_parseAttributes($attributes);
            if (isset($attributes['selected'])) {
                // the 'selected' attribute will be set in toHtml()
                $this->_removeAttr('selected', $attributes);
                if (is_null($this->_values)) {
                    $this->_values = array($value);
                } elseif (!in_array($value, $this->_values)) {
                    $this->_values[] = $value;
                }
            }
            $this->_updateAttrArray($attributes, array('value' => $value));
        }

        // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
        // $this->_options[] = array('text' => $text, 'attr' => $attributes);

        // if $optGroup is an array, add the option to it
        if (is_array($optGroup)) {
            $optGroup[$text]['attr'] = $attributes;
        }
        // if $optGroup is a string, add the option to the option group
        // used if directly adding an option to an optgroup
        elseif (is_string($optGroup)) {
            $optGroups = explode($optGroup, ',');
            $target =& $this->_options;
            foreach($optGroups as $group) {
                // create the option group if it does not exist
                if (empty($target[$group]['options'])) {
                    $target[$group]['options'] = array();
                }
                $target =& $target[$group]['options'];
            }
            // add the option
            $target[$text]['attr'] = $attributes;
        }
        // else if there are attributes, add them to the option
        elseif (is_array($attributes)) {
            $this->_options[$text]['attr'] = $attributes;
        }
        // <OPTGROUP> MODIFICATION END

    } // end func addOption
    
    // }}}
    // {{{ loadArray()

    /**
     * Loads the options from an associative array
     * 
     * @param     array    $arr     Associative array of options
     * @param     mixed    $values  (optional) Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadArray($arr, $values=null)
    {
        if (!is_array($arr)) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadArray is not a valid array');
        }
        if (isset($values)) {
            $this->setSelected($values);
        }
        foreach ($arr as $key => $val) {
            // Warning: new API since release 2.3
            $this->addOption($val, $key);
        }
        return true;
    } // end func loadArray

    // }}}
    // {{{ loadDbResult()

    /**
     * Loads the options from DB_result object
     * 
     * If no column names are specified the first two columns of the result are
     * used as the text and value columns respectively
     * @param     object    $result     DB_result object 
     * @param     string    $textCol    (optional) Name of column to display as the OPTION text 
     * @param     string    $valueCol   (optional) Name of column to use as the OPTION value 
     * @param     mixed     $values     (optional) Array or comma delimited string of selected values
     * @since     1.0
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function loadDbResult(&$result, $textCol=null, $valueCol=null, $values=null)
    {
        if (!is_object($result) || !is_a($result, 'db_result')) {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadDbResult is not a valid DB_result');
        }
        if (isset($values)) {
            $this->setValue($values);
        }
        $fetchMode = ($textCol && $valueCol) ? DB_FETCHMODE_ASSOC : DB_FETCHMODE_DEFAULT;
        while (is_array($row = $result->fetchRow($fetchMode)) ) {
            if ($fetchMode == DB_FETCHMODE_ASSOC) {
                $this->addOption($row[$textCol], $row[$valueCol]);
            } else {
                $this->addOption($row[0], $row[1]);
            }
        }
        return true;
    } // end func loadDbResult
    
    // }}}
    // {{{ loadQuery()

    /**
     * Queries a database and loads the options from the results
     *
     * @param     mixed     $conn       Either an existing DB connection or a valid dsn 
     * @param     string    $sql        SQL query string
     * @param     string    $textCol    (optional) Name of column to display as the OPTION text 
     * @param     string    $valueCol   (optional) Name of column to use as the OPTION value 
     * @param     mixed     $values     (optional) Array or comma delimited string of selected values
     * @since     1.1
     * @access    public
     * @return    void
     * @throws    PEAR_Error
     */
    function loadQuery(&$conn, $sql, $textCol=null, $valueCol=null, $values=null)
    {
        if (is_string($conn)) {
            require_once('DB.php');
            $dbConn = &DB::connect($conn, true);
            if (DB::isError($dbConn)) {
                return $dbConn;
            }
        } elseif (is_subclass_of($conn, "db_common")) {
            $dbConn = &$conn;
        } else {
            return PEAR::raiseError('Argument 1 of HTML_Select::loadQuery is not a valid type');
        }
        $result = $dbConn->query($sql);
        if (DB::isError($result)) {
            return $result;
        }
        $this->loadDbResult($result, $textCol, $valueCol, $values);
        $result->free();
        if (is_string($conn)) {
            $dbConn->disconnect();
        }
        return true;
    } // end func loadQuery

    // }}}
    // {{{ load()

    /**
     * Loads options from different types of data sources
     *
     * This method is a simulated overloaded method.  The arguments, other than the
     * first are optional and only mean something depending on the type of the first argument.
     * If the first argument is an array then all arguments are passed in order to loadArray.
     * If the first argument is a db_result then all arguments are passed in order to loadDbResult.
     * If the first argument is a string or a DB connection then all arguments are 
     * passed in order to loadQuery.
     * @param     mixed     $options     Options source currently supports assoc array or DB_result
     * @param     mixed     $param1     (optional) See function detail
     * @param     mixed     $param2     (optional) See function detail
     * @param     mixed     $param3     (optional) See function detail
     * @param     mixed     $param4     (optional) See function detail
     * @since     1.1
     * @access    public
     * @return    PEAR_Error on error or true
     * @throws    PEAR_Error
     */
    function load(&$options, $param1=null, $param2=null, $param3=null, $param4=null)
    {
        switch (true) {
            case is_array($options):
                return $this->loadArray($options, $param1);
                break;
            case (is_a($options, 'db_result')):
                return $this->loadDbResult($options, $param1, $param2, $param3);
                break;
            case (is_string($options) && !empty($options) || is_subclass_of($options, "db_common")):
                return $this->loadQuery($options, $param1, $param2, $param3, $param4);
                break;
        }
    } // end func load
    
    // }}}
    // {{{ toHtml()

    /**
     * Returns the SELECT in HTML
     *
     * @since     1.0
     * @access    public
     * @return    string
     */
    function toHtml()
    {
        if ($this->_flagFrozen) {
            return $this->getFrozenHtml();
        } else {
            $tabs    = $this->_getTabs();
            $strHtml = '';

            if ($this->getComment() != '') {
                $strHtml .= $tabs . '<!-- ' . $this->getComment() . " //-->\n";
            }

            if (!$this->getMultiple()) {
                $attrString = $this->_getAttrString($this->_attributes);
            } else {
                $myName = $this->getName();
                $this->setName($myName . '[]');
                $attrString = $this->_getAttrString($this->_attributes);
                $this->setName($myName);
            }
            $strHtml .= $tabs . '<select' . $attrString . ">\n";

            // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
            // foreach ($this->_options as $option) {
            //     if (is_array($this->_values) && in_array((string)$option['attr']['value'], $this->_values)) {
            //         $this->_updateAttrArray($option['attr'], array('selected' => 'selected'));
            //     }
            //     $strHtml .= $tabs . "\t<option" . $this->_getAttrString($option['attr']) . '>' .
            //                 $option['text'] . "</option>\n";
            // }
            foreach ($this->_options as $text=>$option) {
                $strHtml .= $tabs . $this->_optionToHtml($text, $option);
            }
            // <OPTGROUP> MODIFICATION END

            return $strHtml . $tabs . '</select>';
        }
    } //end func toHtml
    
    // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
    /**
     * Returns an OPTION in HTML
     * 
     * This function is called recursively to support optgroups
     *
     * @param     string    $text       Display text for the option
     * @param     array     $option     The option
     * @since     ??
     * @access    private
     * @return    string
     */
    // Creates the HTML for an option
    function _optionToHtml($text, $option)
    {
        $tabs = $this->_getTabs();

        // if an option has options it's an optgroup
        if (isset($option['options'])) {
            $strHtml = $tabs . "<optgroup label=\"$text\">\n";

            foreach($option['options'] as $txt=>$opt) {
                $strHtml .= $tabs . $this->_optionToHtml($txt, $opt);
            }

            $strHtml .= $tabs . "</optgroup>\n";

            return($strHtml);
        }

        // else it's an option
        else {
            if (is_array($this->_values) && in_array((string)$option['attr']['value'], $this->_values)) {
                $this->_updateAttrArray($option['attr'], array('selected' => 'selected'));
            }
            return("\t<option" . $this->_getAttrString($option['attr']) . ">$text</option>\n");       
        }
    }
    // <OPTGROUP> MODIFICATION END


    // }}}
    // {{{ getFrozenHtml()

    /**
     * Returns the value of field without HTML tags
     * 
     * @since     1.0
     * @access    public
     * @return    string
     */
    function getFrozenHtml()
    {
        $value = array();
        if (is_array($this->_values)) {
            foreach ($this->_values as $key => $val) {
                for ($i = 0, $optCount = count($this->_options); $i < $optCount; $i++) {
                    if ((string)$val == (string)$this->_options[$i]['attr']['value']) {
                        $value[$key] = $this->_options[$i]['text'];
                        break;
                    }
                }
            }
        }
        $html = empty($value)? '&nbsp;': join('<br />', $value);
        if ($this->_persistantFreeze) {
            $name = $this->getPrivateName();
            // Only use id attribute if doing single hidden input
            if (1 == count($value)) {
                $id     = $this->getAttribute('id');
                $idAttr = isset($id)? array('id' => $id): array();
            } else {
                $idAttr = array();
            }
            foreach ($value as $key => $item) {
                $html .= '<input' . $this->_getAttrString(array(
                             'type'  => 'hidden',
                             'name'  => $name,
                             'value' => $this->_values[$key]
                         ) + $idAttr) . ' />';
            }
        }
        return $html;
    } //end func getFrozenHtml

    // }}}
    // {{{ exportValue()

   /**
    * We check the options and return only the values that _could_ have been
    * selected. We also return a scalar value if select is not "multiple"
    */
    function exportValue(&$submitValues, $assoc = false)
    {
        $value = $this->_findValue($submitValues);
        if (is_null($value)) {
            $value = $this->getValue();
        } elseif(!is_array($value)) {
            $value = array($value);
        }
        if (is_array($value) && !empty($this->_options)) {
            $cleanValue = null;
            foreach ($value as $v) {

                // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
                // for ($i = 0, $optCount = count($this->_options); $i < $optCount; $i++) {
                //     if ($v == $this->_options[$i]['attr']['value']) {
                //         $cleanValue[] = $v;
                //         break;
                //     }
                // }
                if ($this->_isInOptGroup($v, $this->_options)) {
                    $cleanValue[] = $v;
                }
                // <OPTGROUP> MODIFICATION END

            }
        } else {
            $cleanValue = $value;
        }
        if (is_array($cleanValue) && !$this->getMultiple()) {
            return $this->_prepareValue($cleanValue[0], $assoc);
        } else {
            return $this->_prepareValue($cleanValue, $assoc);
        }
    }
    
    // <OPTGROUP> MODIFICATION FROM http://pear.php.net/bugs/bug.php?id=1283
    function _isInOptGroup($v, $opts) {
        $isInOptGroup = false;
        foreach ($opts as $opt) {
            if (isset($opt['options'])) {
                $isInOptGroup = $this->_isInOptGroup($v, $opt['options']);
            } else {
                if ($v == $opt['attr']['value']) {
                    $isInOptGroup = true;
                }
            }
            if ($isInOptGroup) break;
        }
        return $isInOptGroup;
    }
    // <OPTGROUP> MODIFICATION END


    // }}}
    // {{{ onQuickFormEvent()

    function onQuickFormEvent($event, $arg, &$caller)
    {
        if ('updateValue' == $event) {
            $value = $this->_findValue($caller->_constantValues);
            if (null === $value) {
                $value = $this->_findValue($caller->_submitValues);
                // Fix for bug #4465
                // XXX: should we push this to element::onQuickFormEvent()?
                if (null === $value && !$caller->isSubmitted()) {
                    $value = $this->_findValue($caller->_defaultValues);
                }
            }
            if (null !== $value) {
                $this->setValue($value);
            }
            return true;
        } else {
            return parent::onQuickFormEvent($event, $arg, $caller);
        }
    }

    // }}}
} //end class HTML_QuickForm_select
?>
