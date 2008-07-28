<?php

/**
 * PHPIDS
 * 
 * Requirements: PHP5, SimpleXML
 *
 * Copyright (c) 2007 PHPIDS group (http://php-ids.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * PHP version 5.1.6+
 * 
 * @category Security
 * @package  PHPIDS
 * @author   Mario Heiderich <mario.heiderich@gmail.com>
 * @author   Christian Matthies <ch0012@gmail.com>
 * @author   Lars Strojny <lars@strojny.net>
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL
 * @link     http://php-ids.org/
 */

/**
 * Monitoring engine
 *
 * This class represents the core of the frameworks attack detection mechanism
 * and provides functions to scan incoming data for malicious appearing script
 * fragments.
 *
 * @category  Security
 * @package   PHPIDS
 * @author    Christian Matthies <ch0012@gmail.com>
 * @author    Mario Heiderich <mario.heiderich@gmail.com>
 * @author    Lars Strojny <lars@strojny.net>
 * @copyright 2007 The PHPIDS Group
 * @license   http://www.gnu.org/licenses/lgpl.html LGPL
 * @version   Release: $Id:Monitor.php 949 2008-06-28 01:26:03Z christ1an $
 * @link      http://php-ids.org/
 */
class IDS_Monitor
{

    /**
     * Tags to define what to search for
     *
     * Accepted values are xss, csrf, sqli, dt, id, lfi, rfe, spam, dos
     *
     * @var array
     */
    private $tags = null;

    /**
     * Request array
     *
     * Array containing raw data to search in
     *
     * @var array
     */
    private $request = null;

    /**
     * Container for filter rules
     *
     * Holds an instance of IDS_Filter_Storage
     *
     * @var object
     */
    private $storage = null;

    /**
     * Results
     *
     * Holds an instance of IDS_Report which itself provides an API to
     * access the detected results
     *
     * @var object
     */
    private $report = null;

    /**
     * Scan keys switch
     *
     * Enabling this property will cause the monitor to scan both the key and
     * the value of variables
     *
     * @var boolean
     */
    public $scanKeys = false;

    /**
     * Exception container
     *
     * Using this array it is possible to define variables that must not be
     * scanned. Per default, utmz google analytics parameters are permitted.
     *
     * @var array
     */
    private $exceptions = array();

    /**
     * Html container
     *
     * Using this array it is possible to define variables that legally 
     * contain html and have to be prepared before hitting the rules to 
     * avoid too many false alerts
     *
     * @var array
     */
    private $html = array();
    
    /**
     * Holds HTMLPurifier object
     *
     * @var object
     */
    private $htmlpurifier = NULL;

	/**
	 * Path to HTMLPurifier source
	 *
	 * This path might be changed in case one wishes to make use of a
	 * different HTMLPurifier source file e.g. if already used in the 
	 * application PHPIDS is protecting
	 *
	 * @var	string
	 */
	private	$pathToHTMLPurifier = '';
	
	/**
	 * HTMLPurifier cache directory
	 *
	 * @var	string
	 */
	private $HTMLPurifierCache = '';
    
    
    /**
     * Constructor
     *
     * @param array  $request array to scan
     * @param object $init    instance of IDS_Init
     * @param array  $tags    list of tags to which filters should be applied
     * 
     * @return void
     */
    public function __construct(array $request, IDS_Init $init, array $tags = null) 
    {
        $version = isset($init->config['General']['min_php_version']) ? $init->config['General']['min_php_version'] : '5.1.6';
            
        if (version_compare(PHP_VERSION, $version, '<')) {
            throw new Exception(
                'PHP version has to be equal or higher than ' . $version . ' or 
                PHP version couldn\'t be determined'
            );          
        }     
    

        if (!empty($request)) {
            $this->storage = new IDS_Filter_Storage($init);
            $this->request = $request;
            $this->tags    = $tags;

			$this->scanKeys   = $init->config['General']['scan_keys'];
			$this->exceptions = isset($init->config['General']['exceptions']) ? $init->config['General']['exceptions'] : false;
			$this->html       = isset($init->config['General']['html']) ? $init->config['General']['html'] : false;
			$this->pathToHTMLPurifier	= CRM_Utils_Array::value( 'HTML_Purifier_Path', $init->config['General'] );
			$this->HTMLPurifierCache	= CRM_Utils_Array::value( 'HTML_Purifier_Cache', $init->config['General'] );
        }

        if (!is_writeable($init->config['General']['tmp_path'])) {
            throw new Exception(
                'Please make sure the IDS/tmp folder is writable'
            );
        }

        include_once 'IDS/Report.php';
        $this->report = new IDS_Report;
    }

    /**
     * Starts the scan mechanism
     *
     * @return object IDS_Report
     */
    public function run()
    {
        if (!empty($this->request)) {
            foreach ($this->request as $key => $value) {
                $this->_iterate($key, $value);
            }
        }

        return $this->getReport();
    }

    /**
     * Iterates through given data and delegates it to IDS_Monitor::_detect() in
     * order to check for malicious appearing fragments
     *
     * @param mixed $key   the former array key
     * @param mixed $value the former array value
     * 
     * @return void
     */
    private function _iterate($key, $value) 
    {

        if (!is_array($value)) {
            if (is_string($value)) {

                if ($filter = $this->_detect($key, $value)) {
                    include_once 'IDS/Event.php';
                    $this->report->addEvent(
						new IDS_Event(
                        	$key,
                        	$value,
                        	$filter
                    	)
					);
                }
            }
        } else {
            foreach ($value as $subKey => $subValue) {
                $this->_iterate($key . '.' . $subKey, $subValue);
            }
        }
    }

    /**
     * Checks whether given value matches any of the supplied filter patterns
     *
     * @param mixed $key   the key of the value to scan
     * @param mixed $value the value to scan
     * 
     * @return bool|array false or array of filter(s) that matched the value
     */
    private function _detect($key, $value) 
    {

		// to increase performance, only start detection if value
        // isn't alphanumeric 
        if (!(preg_match('/[^\w\s\/@]+/ims', $value) && !empty($value) || preg_match('/Q\d{2}/', $value))) {
			return false;
		}

		// check if this field is part of the exceptions
		if (is_array($this->exceptions) && in_array($key, $this->exceptions, true)) {
			return false;
		}
		
		// check for magic quotes and remove them if necessary
		if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		
		// if html monitoring is enabled for this field - then do it!
		if (is_array($this->html) && in_array($key, $this->html, true)) {
			list($key, $value) = $this->_purifyValues($key, $value);
		}
	   
		// use the converter
		include_once 'IDS/Converter.php';
		$value = IDS_Converter::runAll($value);
		$value = IDS_Converter::runCentrifuge($value, $this);
		
		// scan keys if activated via config
		$key = $this->scanKeys ? IDS_Converter::runAll($key) : $key;
		$key = $this->scanKeys ? IDS_Converter::runCentrifuge($key, $this) : $key;
		
		$filters   = array();
		$filterSet = $this->storage->getFilterSet();
		foreach ($filterSet as $filter) {
		
			/*
			 * in case we have a tag array specified the IDS will only
			 * use those filters that are meant to detect any of the 
			 * defined tags
			 */
			if (is_array($this->tags)) {
				if (array_intersect($this->tags, $filter->getTags())) {
					if ($this->_match($key, $value, $filter)) {
						$filters[] = $filter;
					}
				}
			} else {
				if ($this->_match($key, $value, $filter)) {
					$filters[] = $filter;
				}
			}
		}

		return empty($filters) ? false : $filters;
    }
	
	
    /**
     * Purifies given key and value variables using HTMLPurifier
     *
	 * This function is needed whenever there is variables for which HTML
	 * might be allowed like e.g. WYSIWYG post bodies. It will dectect malicious
	 * code fragments and leaves harmless parts untouched.
	 *
     * @param	mixed	$key
     * @param	mixed	$value
	 * @since	0.5
     * 
     * @return	array
     */
	private function _purifyValues($key, $value) {
	
		include_once $this->pathToHTMLPurifier;
		if (!is_writeable($this->HTMLPurifierCache)) {
			throw new Exception($this->HTMLPurifierCache . ' must be writeable');
		}
		
		if (class_exists('HTMLPurifier')) {
			$config = HTMLPurifier_Config::createDefault();
			$config->set('Attr', 'EnableID', true);
            $config->set('Cache', 'SerializerPath', $this->HTMLPurifierCache);
			$this->htmlpurifier = new HTMLPurifier($config);
		} else {
			throw new Exception(
				'HTMLPurifier class could not be found - make sure the purifier files are valid and' .
				' the path is correct'
			);
		}					
		
		$purified_value = $this->htmlpurifier->purify($value);
		$purified_key   = $this->htmlpurifier->purify($key);
		
		$redux_value = strip_tags($value);
		$redux_key   = strip_tags($key);
		
		if ($value != $purified_value || $redux_value) {
			$value = $this->_diff($value, $purified_value, $redux_value);
		} else {
			$value = NULL;
		}
		if ($key != $purified_key) {
			$key = $this->_diff($key, $purified_key, $redux_key);
		} else {
			$key = NULL;
		}
		
		return array($key, $value);
	}

    /**
     * This method calculates the difference between the original 
     * and the purified markup strings.
     *
     * @param string $original the original markup
     * @param string $purified the purified markup
     * @param string $redux    the string without html
	 * @since 0.5
	 *
     * @return string the difference between the strings
     */
    private function _diff($original, $purified, $redux)
    {
        /*
         * deal with over-sensitive alt-attribute addition of the purifier 
         * and other common html formatting problems
         */
        $purified = preg_replace('/\s+alt="[^"]*"/m', null, $purified);
        $purified = preg_replace('/=?\s*"\s*"/m', null, $purified);
        
        $original = preg_replace('/=?\s*"\s*"/m', null, $original);
        $original = preg_replace('/\s+alt=?/m', null, $original);
        
        // check which string is longer
        $length = (strlen($original) - strlen($purified));
        /*
         * Calculate the difference between the original html input 
         * and the purified string.
         */		
        if ($length > 0) {
            $array_2 = str_split($original);
            $array_1 = str_split($purified);
        } else {
            $array_1 = str_split($original);
            $array_2 = str_split($purified);
        }
        foreach ($array_2 as $key => $value) {
            if ($value !== $array_1[$key]) {
                $array_1   = array_reverse($array_1);
                $array_1[] = $value;
                $array_1   = array_reverse($array_1);
            }
        }
        
		// return the diff - ready to hit the converter and the rules
        $diff = trim(join('', array_reverse((array_slice($array_1, 0, $length)))));
        
        // clean up spaces between tag delimiters
        $diff = preg_replace('/>\s*</m', '><', $diff); 
        
        // correct over-sensitively stripped bad html elements
        $diff = preg_replace('/[^<](iframe|script|embed|object' . 
            '|applet|base|img|style)/m', '<$1', $diff);
        
        if ($original == $purified && !$redux) {
            return null;
        }
        
        return $diff . $redux;
    }

    /**
     * Matches given value and/or key against given filter
     *
     * @param mixed  $key    the key to optionally scan
     * @param mixed  $value  the value to scan
     * @param object $filter the filter object
     * 
     * @return boolean
     */
    private function _match($key, $value, $filter) 
    {
        if ($this->scanKeys) {
            if ($filter->match($key)) {
                return true;
            }
        }

        if ($filter->match($value)) {
            return true;
        }

        return false;
    }

    /**
     * Sets exception array
     *
     * @param mixed $exceptions the thrown exceptions
     * 
     * @return void
     */
    public function setExceptions($exceptions) 
    {
        if (!is_array($exceptions)) {
            $exceptions = array($exceptions);
        }

        $this->exceptions = $exceptions;
    }

    /**
     * Returns exception array
     *
     * @return array
     */
    public function getExceptions() 
    {
        return $this->exceptions;
    }

    /**
     * Sets html array
     *
     * @param mixed $html the fields not to monitor
	 * @since 0.5
	 *
     * @return void
     */
    public function setHtml($html) 
    {
        if (!is_array($html)) {
            $html = array($html);
        }

        $this->html = $html;
    }
    
	/**
     * Adds a value to the html array
	 *
	 * @since 0.5
	 *
     * @return void
     */
    public function addHtml($value) 
	{
        $this->html[] = $value;
    }

    /**
     * Returns exception array
	 *
	 * @since 0.5
	 *
     * @return array the fields that contain allowed html
     */
    public function getHtml() 
    {
        return $this->html;
    }	
    
    /**
     * Returns report object providing various functions to work with 
     * detected results. Also the centrifuge data is being set as property 
     * of the report object.
     *
     * @return object IDS_Report
     */
    public function getReport() 
    {
        if (isset($this->centrifuge) && $this->centrifuge) {
            $this->report->setCentrifuge($this->centrifuge);
        }
        
        return $this->report;
    }
	
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 */