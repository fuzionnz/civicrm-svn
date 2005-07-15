<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

require_once 'Mail/mime.php';

class CRM_Mailing_BAO_Mailing extends CRM_Mailing_DAO_Mailing {

    /**
     * The header associated with this mailing
     */
    private $header = null;

    /**
     * The footer associated with this mailing
     */
    private $footer = null;


    /**
     * The HTML content of the message
     */
    private $html = null;

    /**
     * The text content of the message;
     */
    private $text = null;
    

    /**
     * class constructor
     */
    function __construct( ) {
        parent::__construct( );
    }

    /**
     * Find all intended recipients of a mailing
     *
     * @param none
     * @return array    Tuples of Contact IDs and Email IDs
     */
    function &getRecipients() {
        $mailingGroup =& new CRM_Mailing_DAO_MailingGroup();
        
        $mailing    = CRM_Mailing_DAO_Mailing::tableName();
        $mg         = CRM_Mailing_DAO_MailingGroup::tableName();
        $eq         = CRM_Mailing_DAO_MailingEventQueue::tableName();
        $ed         = CRM_Mailing_DAO_MailingEventDelivered::tableName();
        $eb         = CRM_Mailing_DAO_MailingEventBounce::tableName();
        $job        = CRM_Mailing_DAO_Job::tableName();
        
        $email      = CRM_Contact_DAO_Email::tableName();
        $contact    = CRM_Contact_DAO_Contact::tableName();
        $location   = CRM_Contact_DAO_Location::tableName();
        $group      = CRM_Contact_DAO_Group::tableName();
        $g2contact  = CRM_Contact_DAO_GroupContact::tableName();
       
        /* FIXME Make this mysql 4.0-friendly (no subselects) */

       
        /* Get the contact ids to exclude */
        $excludeSubGroup =
                    "SELECT DISTINCT    $g2contact.contact_id
                    FROM                $g2contact
                    INNER JOIN          $mg
                            ON          $g2contact.group_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = " . $this->id . "
                        AND             $mg.entity_table = '$group'
                        AND             $g2contact.status = 'In'
                        AND             $mg.group_type = 'Exclude'
                    ORDER BY            $g2contact.contact_id";
       
        $excludeSubMailing = 
                    "SELECT DISTINCT    $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $mg
                            ON          $job.mailing_id = $mg.entity_id
                    WHERE
                                        $mg.mailing_id = " . $this->id . "
                        AND             $mg.entity_table '$mailing'
                        AND             $mg.group_type = 'Exclude'
                    ORDER BY            $eq.contact_id";
                    
        $excludeRetry =
                    "SELECT DISTINCT    $eq.contact_id
                    FROM                $eq
                    INNER JOIN          $job
                            ON          $eq.job_id = $job.id
                    INNER JOIN          $ed
                            ON          $eq.id = $ed.event_queue_id
                    LEFT JOIN           $eb
                            ON          $eq.id = $eb.event_queue_id
                    WHERE
                                        $job.mailing_id = " . $this->id . "
                        AND             $eb.id is null
                    ORDER BY            $eq.contact_id";

                    
        $excludeSubQuery =  "($excludeSubGroup) 
                            UNION DISTINCT ($excludeSubMailing) 
                            UNION DISTINCT ($excludeRetry)";

        /* Get all the group contacts we want to include */
        /* TODO: support bounce status */
        $queryGroup = 
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $location
                            ON          $email.location_id = $location.id
                    INNER JOIN          $contact
                            ON          $location.contact_id = $contact.id
                            
                            
                        INNER JOIN          $g2contact
                                ON          $contact.id = $g2contact.contact_id
                        INNER JOIN          $mg
                                ON          $g2contact.group_id = $mg.entity_id
                                
                    WHERE           
                                            $mg.entity_table = '$group'
                            AND             $mg.group_type = 'Include'
                            AND             $g2contact.status = 'In'
                        
                        AND             $contact.do_not_email = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $mg.mailing_id = " . $this->id . "
                        AND             $contact.id NOT IN ($excludeSubQuery)";
                        
        $queryMailing =
                    "SELECT DISTINCT    $email.id as email_id,
                                        $contact.id as contact_id,
                    FROM                $email
                    INNER JOIN          $location
                            ON          $email.location_id = $location.id
                    INNER JOIN          $contact
                            ON          $location.contact_id = $contact.id
                    
                    
                        INNER JOIN          $eq
                                ON          $eq.contact_id = $contact.id
                        INNER JOIN          $job
                                ON          $eq.job_id = $job.id
                        INNER JOIN          $mg
                                ON          $job.mailing_id = $mg.mailing_id
                    
                    
                    WHERE
                                            $mg.entity_table = '$mailing'
                            AND             $mg.group_type = 'Include'
                        
                        AND             $contact.do_not_email = 0
                        AND             $location.is_primary = 1
                        AND             $email.is_primary = 1
                        AND             $mg.mailing_id = " . $this->id . "
                        AND             $contact.id NOT IN ($excludeSubQuery)";

        $query = "($queryGroup) UNION DISTINCT ($queryMailing)";
        
        $results = array();

        $mailingGroup->query($query);
        $mailingGroup->find();

        while ($mailingGroup->fetch()) {
            $results[] =    
                array(  'email_id'  => $mailingGroup->email_id,
                        'contact_id'=> $mailingGroup->contact_id
                );
        }
        return $results;
    }

    /**
     * Retrieve the header and footer for this mailing
     *
     * @param void
     * @return void
     * @access private
     */
    private function getHeaderFooter() {
        $this->header =& new CRM_Mailing_BAO_Component();
        $this->header->id = $this->header_id;
        $this->header->find(true);
        
        $this->footer =& new CRM_Mailing_BAO_Component();
        $this->footer->id = $this->footer_id;
        $this->footer->find(true);
                        
        /* TODO append canspam address to footer */
    }


    /**
     * Compose a message
     *
     * @param int $job_id           ID of the Job associated with this message
     * @param int $event_queue_id   ID of the EventQueue
     * @param string $hash          Hash of the EventQueue
     * @param string $name          Display name of the recipient
     * @param string $email         Destination address
     * @return object               The mail object
     * @access public
     */
    public function &compose($job_id, $event_queue_id, $hash, $name, $email) {
    
        if ($this->html == null || $this->text == null) {
            $this->getHeaderFooter();
        
            $this->html = $this->header->body_html 
                        . $this->body_html 
                        . $this->footer->body_html;
                        
            $this->text = $this->header->body_text
                        . $this->body_text
                        . $this->footer->body_text;
        }

        /* FIXME */
        $domain = "@FIXME.COM";

        foreach (array('reply', 'owner', 'unsubscribe') as $key) {
            $address[$key] = implode('.', 
                        array(
                            $key, 
                            $job_id, 
                            $event_queue_id,
                            $hash
                        )
                    ) . "@$domain";
        }
        
        $headers = array(
            'To'        => "$name <$email>",
            'Subject'   => $this->subject,
            'From'      => $this->from_name . ' <' . $this->from_email . '>',
            'Reply-To'  => CRM_Utils_Verp::encode($address['reply'], $email),
            'Return-path' => CRM_Utils_Verp::encode($address['owner'], $email),
        );

        
        /* TODO Token replacement */

        $message =& new Mail_Mime("\n");

        $message->setTxtBody($this->text);
        $message->setHTMLBody($this->html);
        $message->headers($headers);

        return $message;
    }
}

?>
