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
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'api/crm.php';

class CRM_Mailing_Event_BAO_Subscribe extends CRM_Mailing_Event_DAO_Subscribe {

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Register a subscription event.  Create a new contact if one does not
     * already exist.
     *
     * @param int $domain_id        The domain id of the new subscription
     * @param int $group_id         The group id to subscribe to
     * @param string $email         The email address of the (new) contact
     * @return int|null $contact_id      The id of the (new) contact, null on failure
     * @access public
     * @static
     */
    public static function subscribe($domain_id, $group_id, $email) {
        /* First, find out if the contact already exists */        
        $params = array('email' => $email, 'domain_id' => $domain_id);
        $contact_id = CRM_Contact_BAO_Contact::_crm_get_contact_id($params);
        
        CRM_Core_DAO::transaction('BEGIN');
        if (is_a($contact_id, CRM_Core_Error)) {
            /* If the contact does not exist, create one. */
            $formatted = array('contact_type' => 'Individual');
            $value = array('email' => $email, 'location_type' =>
            CRM_Core_BAO_LocationType::getDefaultID());
            _crm_add_formatted_param($value, $formatted);
            $contact = crm_create_contact_formatted($formatted,
                CRM_Import_Parser::DUPLICATE_SKIP);

            if (is_a($contact, CRM_Core_Error)) {
                return null;
            }
            $contact_id = $contact->id;
        }
       
        /* Get the primary email id from the contact to use as a hash input */
        $dao =& new CRM_Core_DAO();
        $emailTable = CRM_Core_BAO_Email::getTableName();
        $locTable   = CRM_Core_BAO_Location::getTableName();
        $contactTable = CRM_Contact_BAO_Contact::getTableName();
        $dao->query("SELECT $emailTable.id as email_id
                    FROM $emailTable
                    INNER JOIN $locTable
                        ON  $emailTable.location_id = $locTable.id
                    WHERE   $emailTable.is_primary = 1
                    AND     $locTable.is_primary = 1
                    AND     $locTable.entity_table = '$contactTable'
                    AND     $locTable.entity_id = $contact_id");
        $dao->fetch();

        $se =& new CRM_Mailing_Event_BAO_Subscribe();
        $se->group_id = $group_id;
        $se->contact_id = $contact_id;
        $se->time_stamp = date('YmdHis');
        $se->hash = sha1("{$group_id}:{$contact_id}:{$dao->email_id}");
        $se->save();

        $shParams = array (
            'contact_id' => $contact_id,
            'group_id'  => null,
            'method'    => 'Email',
            'status'    => 'Pending',
            'tracking'  => $se->id
        );
        CRM_Contact_BAO_SubscriptionHistory::create($shParams);
        $contacts = array($contact_id);
        CRM_Contact_BAO_GroupContact::addContactsToGroup($contacts, $group_id,
            'Email', 'Pending', $se->id);
            
        CRM_Core_DAO::transaction('COMMIT');
        return $contact_id;
    }

    /**
     * Verify the hash of a subscription event
     * 
     * @param int $contact_id       ID of the contact
     * @param int $subscribe_id     ID of the subscription event
     * @param string $hash          Hash to verify
     *
     * @return object|null          The subscribe event object, or null on failure
     * @access public
     * @static
     */
    public static function &verify($contact_id, $subscribe_id, $hash) {
        $se =& new CRM_Mailing_Event_BAO_Subscribe();
        $se->contact_id = $contact_id;
        $se->id = $subscribe_id;
        $se->hash = $hash;
        if ($se->find(true)) {
            return $se;
        }
        return null;
    }
}

?>
