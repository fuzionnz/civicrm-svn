 -- CRM-4795
 -- modify type fee_level of civicrm_participant and amount_level of civicrm_contribution

   ALTER TABLE civicrm_participant MODIFY column fee_level text collate utf8_unicode_ci default NULL COMMENT 'Populate with the label (text) associated with a fee level for paid events with multiple levels. Note that we store the label value and not the key'; 

   ALTER TABLE civicrm_contribution MODIFY column amount_level text collate utf8_unicode_ci default NULL;

--- subtype upgrade TODOs: 
-- make changes for CRM-4970

-- modify contact_type column definition
   ALTER TABLE  `civicrm_contact` MODIFY column contact_type varchar(64) collate utf8_unicode_ci DEFAULT NULL COMMENT 'Type of Contact'; 
    
-- add table definiton and data for civicrm_contact_type table
   CREATE TABLE IF NOT EXISTS civicrm_contact_type (
     id int(10) unsigned NOT NULL auto_increment COMMENT 'Contact Type ID',	
     name varchar(64) collate utf8_unicode_ci default NULL COMMENT 'Internal name of Contact Type      (or Subtype).',
     label varchar(64) collate utf8_unicode_ci default NULL COMMENT 'Name of Contact Type.',
     description text collate utf8_unicode_ci COMMENT 'Optional verbose description of the type.',               
     image_URL varchar(255) collate utf8_unicode_ci default NULL  COMMENT'URL of image if any.',
     parent_id int(10) unsigned default NULL  COMMENT 'Optional FK to parent contact type.',
     is_active tinyint(4) default NULL COMMENT 'Is this entry active?',
     PRIMARY KEY  ( id ),
     UNIQUE KEY contact_type ( name ),
     CONSTRAINT FK_civicrm_contact_type_parent_id FOREIGN KEY (parent_id) REFERENCES civicrm_contact_type(id) ON DELETE CASCADE       
       ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

   INSERT INTO civicrm_contact_type 
       ( id, name, label, description, image_URL, parent_id, is_active ) 
   VALUES
       (1, 'Individual', 'Individual', NULL, NULL, NULL, 1),
       (2, 'Household', 'Household', NULL, NULL, NULL, 1),
       (3, 'Organization', 'Organization', NULL, NULL, NULL, 1),
       (4, 'Student', 'Student', NULL, NULL, 1, 1),
       (5, 'Parent', 'Parent', NULL, NULL, 1, 1),
       (6, 'Staff', 'Staff', NULL, NULL, 1, 1),
       (7, 'Team', 'Team', NULL, NULL, 3, 1),
       (8, 'Sponsor', 'Sponsor', NULL, NULL, 3, 1);

-- modify civicrm_custom_group.extends column to varchar(64)
   ALTER TABLE  `civicrm_custom_group` MODIFY column extends varchar(64) collate utf8_unicode_ci DEFAULT 'Contact' COMMENT 'Type of object this group extends (can add other options later e.g. contact_address, etc.).'; 
    
-- CRM-5218
-- added menu for contact Subtypes in navigation
   SELECT @domain_id := min(id) FROM civicrm_domain;
   SELECT @nav_ol    := id FROM civicrm_navigation WHERE name = 'Option Lists';
   SELECT @nav_ol_wt := max(weight) from civicrm_navigation WHERE parent_id = @nav_ol;
   INSERT INTO `civicrm_navigation`
       ( domain_id, url, label, name,permission, permission_operator, parent_id, is_active, has_separator, weight ) 
   VALUES
       (  @domain_id,'civicrm/admin/options/subtype&reset=1', 'Contact Subtypes', 'Contact Subtypes', 'administer CiviCRM', '', @nav_ol, '1', NULL, @nav_ol_wt+1 ); 

-- make changes for CRM-5100 
   ALTER TABLE `civicrm_relationship_type` ADD `contact_sub_type_a` varchar(64) collate utf8_unicode_ci DEFAULT NULL AFTER `contact_type_b`;
   ALTER TABLE `civicrm_relationship_type` ADD `contact_sub_type_b` varchar(64) collate utf8_unicode_ci DEFAULT NULL AFTER `contact_sub_type_a`;
      
-- Upgrade FCKEditor to CKEditor CRM-5226

   UPDATE civicrm_option_value SET label = 'CKEditor' WHERE label = 'FCKEditor';