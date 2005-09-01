<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.1                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Social Source Foundation                        |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 * This class handles all SOAP client requests.
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'api/utils.php';
require_once 'api/Contact.php';
require_once 'api/Mailer.php';

class CRM_Utils_SoapServer
{
    const
        INVALID_LOGIN   =   1,
        INVALID_KEY     =   2,
        EXPIRED_KEY     =   4;


    /**
     * Number of seconds we should let a soap process idle
     * @static
     */
    static $soap_timeout = 0;
    
    /**
     * Cache the actual UF Class
     */
    public $ufClass;

    /**
     * Class constructor.  This caches the real user framework class locally,
     * so we can use it for authentication and validation.
     *
     * @param  string $uf       The userframework class
     */
    public function __construct() {
        session_start();
        $this->ufClass = array_shift(func_get_args());
    }

    /**
     * Simple ping function to test for liveness.
     *
     * @param string $var   The string to be echoed
     * @return string       $var
     * @access public
     */
    public function ping($var) {
        return $var;
    }


    /**
     * Verify a SOAP key
     *
     * @param string $key   The soap key generated by authenticate()
     * @return none
     * @access public
     */
    public function verify($key) {
        $session =& CRM_Core_Session::singleton();
        $soap_key = $session->get('soap_key');
        $t = time();
        
        if ( $key !== sha1($soap_key) ) {
            throw new SoapFault(self::INVALID_KEY, 'Invalid key');
        }
        

        if (    self::$soap_timeout && 
                $t > ($session->get('soap_time') + self::$soap_timeout)) {
            throw new SoapFault(self::EXPIRED_KEY, 'Expired key');
        }
        
        /* otherwise, we're ok.  update the timestamp */
        $session->set('soap_time', $t);
    }
    
    /**
     * Authentication wrapper to the UF Class
     *
     * @param string $name      Login name
     * @param string $pass      Password
     * @return string           The SOAP Client key
     * @access public
     * @static
     */
    public function authenticate($name, $pass) {
        eval ('$result =& ' . $this->ufClass . '::authenticate($name, $pass);');

        if (empty($result)) {
            throw new SoapFault(self::INVALID_LOGIN, 'Invalid login');
        }
        
        $session =& CRM_Core_Session::singleton();
        $session->set('soap_key', $result[2]);
        $session->set('soap_time', time());
        
        return sha1($result[2]);
    }

    /*** MAILER API ***/

    public function mailer_event_bounce($key, $job, $queue, $hash, $body) {
        $this->verify($key);
        return crm_mailer_event_bounce($job, $queue, $hash, $body);
    }

    public function mailer_event_unsubscribe($key, $job, $queue, $hash) {
        $this->verify($key);
        return crm_mailer_event_unsubscribe($job, $queue, $hash);
    }

    public function mailer_event_domain_unsubscribe($key, $job, $queue, $hash) {
        $this->verify($key);
        return crm_mailer_event_domain_unsubscribe($job, $queue, $hash);
    }

    public function mailer_event_subscribe($key, $email, $domain_id, $group_id) {
        $this->verify($key);
        return crm_mailer_event_subscribe($email, $domain_id, $group_id);
    }

    public function mailer_event_confirm($key, $contact, $subscribe, $hash) {
        $this->verify($key);
        return crm_mailer_event_confirm($contact_id, $subscribe_id, $hash);
    }

    public function mailer_event_reply($key, $job, $queue, $hash, $body, $rt) {
        $this->verify($key);
        return crm_mailer_event_reply($job, $queue, $hash, $body, $rt);
    }


}

?>
