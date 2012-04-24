-- CRM-9542 mailing detail report template
SELECT @option_group_id_report := MAX(id)     FROM civicrm_option_group WHERE name = 'report_template';
SELECT @weight                 := MAX(weight) FROM civicrm_option_value WHERE option_group_id = @option_group_id_report;
SELECT @mailCompId       := MAX(id)     FROM civicrm_component where name = 'CiviMail';
INSERT INTO civicrm_option_value
  (option_group_id, {localize field='label'}label{/localize}, value, name, weight, {localize field='description'}description{/localize}, is_active, component_id) VALUES
  (@option_group_id_report, {localize}'Mail Detail Report'{/localize}, 'mailing/detail', 'CRM_Report_Form_Mailing_Detail', @weight := @weight + 1, {localize}'Provides reporting on Intended and Successful Deliveries, Unsubscribes and Opt-outs, Replies and Forwards.'{/localize}, 0, @mailCompId);

INSERT INTO `civicrm_report_instance`
    ( `domain_id`, `title`, `report_id`, `description`, `permission`, `form_values`)
VALUES 
    ( {$domainID}, 'Mailing Detail Report', 'mailing/detail', 'Provides reporting on Intended and Successful Deliveries, Unsubscribes and Opt-outs, Replies and Forwards.', '', '{literal}a:30:{s:6:"fields";a:6:{s:9:"sort_name";s:1:"1";s:12:"mailing_name";s:1:"1";s:11:"delivery_id";s:1:"1";s:14:"unsubscribe_id";s:1:"1";s:9:"optout_id";s:1:"1";s:5:"email";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"id_min";s:0:"";s:6:"id_max";s:0:"";s:5:"id_op";s:3:"lte";s:8:"id_value";s:0:"";s:13:"mailing_id_op";s:2:"in";s:16:"mailing_id_value";a:0:{}s:18:"delivery_status_op";s:2:"eq";s:21:"delivery_status_value";s:0:"";s:18:"is_unsubscribed_op";s:2:"eq";s:21:"is_unsubscribed_value";s:0:"";s:12:"is_optout_op";s:2:"eq";s:15:"is_optout_value";s:0:"";s:13:"is_replied_op";s:2:"eq";s:16:"is_replied_value";s:0:"";s:15:"is_forwarded_op";s:2:"eq";s:18:"is_forwarded_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:9:"order_bys";a:1:{i:1;a:2:{s:6:"column";s:9:"sort_name";s:5:"order";s:3:"ASC";}}s:11:"description";s:21:"Mailing Detail Report";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:9:"parent_id";s:0:"";s:6:"groups";s:0:"";s:9:"domain_id";i:1;}{/literal}');

SELECT @reportlastID       := MAX(id) FROM civicrm_navigation where name = 'Reports';
SELECT @nav_max_weight     := MAX(ROUND(weight)) from civicrm_navigation WHERE parent_id = @reportlastID;

SET @instanceID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    ( {$domainID}, CONCAT('civicrm/report/instance/', @instanceID,'&reset=1'), '{ts escape="sql"}Mailing Detail Report{/ts}', 'Mailing Detail Report', 'administer CiviMail', 'OR', @reportlastID, '1', NULL, @nav_max_weight+1 );
UPDATE civicrm_report_instance SET navigation_id = LAST_INSERT_ID() WHERE id = @instanceID;

-- CRM-9600
ALTER TABLE `civicrm_custom_group` CHANGE `extends_entity_column_value` `extends_entity_column_value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'linking custom group for dynamic object.';

-- CRM-9534
ALTER TABLE `civicrm_prevnext_cache` ADD COLUMN is_selected tinyint(4) DEFAULT '0';

-- CRM-9834
-- add civicrm_batch table changes
-- add batch type and batch status option groups
-- add default profile for contribution and membership batch entry

-- CRM-9686
INSERT INTO `civicrm_state_province`(`country_id`, `abbreviation`, `name`) VALUES(1097, "LP", "La Paz");

-- CRM-9905
ALTER TABLE civicrm_contribution_page CHANGE COLUMN is_email_receipt is_email_receipt TINYINT(4) DEFAULT 0;

-- CRM-9850
ALTER TABLE `civicrm_contribution_page`
  DROP FOREIGN KEY `FK_civicrm_contribution_page_payment_processor_id`;

 ALTER TABLE civicrm_contribution_page DROP INDEX FK_civicrm_contribution_page_payment_processor_id;

 ALTER TABLE `civicrm_contribution_page` CHANGE `payment_processor_id` `payment_processor` VARCHAR( 128 ) NULL DEFAULT NULL COMMENT 'Payment Processor for this contribution Page ';

ALTER TABLE `civicrm_event`
  DROP FOREIGN KEY `FK_civicrm_event_payment_processor_id`;

 ALTER TABLE civicrm_event DROP INDEX FK_civicrm_event_payment_processor_id;

 ALTER TABLE `civicrm_event` CHANGE `payment_processor_id` `payment_processor` VARCHAR( 128 ) NULL DEFAULT NULL COMMENT 'Payment Processor for this event ';

-- CRM-9783
CREATE TABLE `civicrm_sms_provider` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'SMS Provider ID',
  `name` varchar(64) DEFAULT NULL COMMENT 'Provider internal name points to option_value of option_group sms_provider_name',
  `title` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Provider name visible to user',
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_type` int(10) unsigned NOT NULL COMMENT 'points to value in civicrm_option_value for group sms_api_type',
  `api_url` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `api_params` text COLLATE utf8_unicode_ci COMMENT 'the api params in xml, http or smtp format',
  `is_default` tinyint(4) DEFAULT '0',
  `is_active` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

ALTER TABLE `civicrm_mailing` ADD `sms_provider_id` int(10) unsigned NULL COMMENT 'FK to civicrm_sms_provider id ';
ALTER TABLE `civicrm_mailing` ADD CONSTRAINT `FK_civicrm_mailing_sms_provider_id` FOREIGN KEY (`sms_provider_id`) REFERENCES `civicrm_sms_provider` (`id`) ON DELETE SET NULL;

INSERT INTO 
   `civicrm_option_group` (`name`, `title`, `is_reserved`, `is_active`) 
VALUES 
   ('sms_provider_name', '{ts escape="sql"}Sms provider Internal Name{/ts}' , 1, 1);
SELECT @option_group_id_sms_provider_name := max(id) from civicrm_option_group where name = 'sms_provider_name';
    
INSERT INTO civicrm_option_value
     (option_group_id, {localize field='label'}label{/localize}, value, name, weight, filter, is_default, component_id)
VALUES
     (@option_group_id_sms_provider_name, 'Clickatell', 'Clickatell', 'Clickatell', 1, 0, NULL, NULL);

INSERT INTO 
   `civicrm_option_group` (`name`, `title`, `is_reserved`, `is_active`) 
VALUES 
    ( 'sms_api_type',  '{ts escape="sql"}Api Type{/ts}' , 1, 1 );
SELECT @option_group_id_sms_api_type := max(id) from civicrm_option_group where name = 'sms_api_type';
    
INSERT INTO civicrm_option_value
     (option_group_id, {localize field='label'}label{/localize}, value, name, weight, filter, is_default, is_reserved, component_id)
VALUES
     (@option_group_id_sms_api_type, 'http',  1, 'http',  1, NULL, 0, 1, NULL),
     (@option_group_id_sms_api_type, 'xml',   2, 'xml',   2, NULL, 0, 1, NULL),
     (@option_group_id_sms_api_type, 'smtp',  3, 'smtp',  3, NULL, 0, 1, NULL);

-- CRM-9784
SELECT @domainID := min(id) FROM civicrm_domain;
SELECT @adminSystemSettingsID := MAX(id) FROM civicrm_navigation where name = 'System Settings';

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    ( @domainID, 'civicrm/admin/sms/provider?reset=1', '{ts escape="sql" skip="true"}SMS Providers{/ts}', 'SMS Providers', 'administer CiviCRM', '', @adminSystemSettingsID, '1', NULL, 16 );

-- CRM-9799

SELECT @mailingsID := MAX(id) FROM civicrm_navigation where name = 'Mailings';

INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES    
    ( @domainID, 'civicrm/sms/send?reset=1', '{ts escape="sql" skip="true"}New SMS{/ts}', 'New SMS', 'administer CiviCRM', NULL, @mailingsID, '1', 1, 8 );

SELECT @fromEmailAddressesID := MAX(id) FROM civicrm_navigation where name = 'From Email Addresses';

UPDATE civicrm_navigation SET has_separator = 1 WHERE parent_id = @mailingsID AND name = 'From Email Addresses';

SELECT @option_group_id_act := max(id) from civicrm_option_group where name = 'activity_type';

INSERT INTO 
   `civicrm_option_value` (`option_group_id`, `label`, `value`, `name`, `grouping`, `filter`, `is_default`, `weight`, `description`, `is_optgroup`, `is_reserved`, `is_active`, `component_id`, `visibility_id`) 
VALUES
   (@option_group_id_act, '{ts escape="sql"}BULK SMS{/ts}', 34, 'BULK SMS', NULL, 1, NULL, 34, '{ts escape="sql"}BULK SMS{/ts}', 0, 1, 1, NULL, NULL);

ALTER TABLE `civicrm_mailing_recipients` ADD `phone_id` int(10) unsigned DEFAULT NULL;

ALTER TABLE `civicrm_mailing_recipients` ADD CONSTRAINT `FK_civicrm_mailing_recipients_phone_id` FOREIGN KEY (`phone_id`) REFERENCES `civicrm_phone` (`id`) ON DELETE CASCADE;

ALTER TABLE `civicrm_mailing_event_queue` ADD `phone_id` int(10) unsigned DEFAULT NULL;

ALTER TABLE `civicrm_mailing_event_queue` ADD CONSTRAINT `FK_civicrm_mailing_event_queue_phone_id` FOREIGN KEY (`phone_id`) REFERENCES `civicrm_phone` (`id`) ON DELETE CASCADE;

ALTER TABLE `civicrm_mailing_event_queue` CHANGE `email_id` `email_id` int(10) unsigned DEFAULT NULL;
ALTER TABLE `civicrm_mailing_recipients` CHANGE `email_id` `email_id` int(10) unsigned DEFAULT NULL;

-- CRM-9982
ALTER TABLE `civicrm_contribution_page` ADD COLUMN is_confirm_enabled tinyint(4) DEFAULT '1'; 

-- CRM-9980
{if $multilingual}
  {foreach from=$locales item=locale}
    ALTER TABLE civicrm_group ADD title_{$locale} VARCHAR(64);
    ALTER TABLE civicrm_group ADD UNIQUE KEY `UI_title_{$locale}` (title_{$locale});
    UPDATE civicrm_group SET title_{$locale} = title;
  {/foreach}

  ALTER TABLE civicrm_group DROP INDEX `UI_title`;
  ALTER TABLE civicrm_group DROP title;
{/if}

-- CRM-9780
SELECT @country_id   := max(id) from civicrm_country where iso_code = "AN";
DELETE FROM civicrm_state_province WHERE country_id = @country_id;
DELETE FROM civicrm_country WHERE id = @country_id;

SELECT @region_id   := max(id) from civicrm_worldregion where name = "America South, Central, North and Caribbean";

INSERT INTO civicrm_country (name,iso_code,region_id,is_province_abbreviated) VALUES("Curaçao", "CW", @region_id, 0);
INSERT INTO civicrm_country (name,iso_code,region_id,is_province_abbreviated) VALUES("Sint Maarten (Dutch Part)", "SX", @region_id, 0);
INSERT INTO civicrm_country (name,iso_code,region_id,is_province_abbreviated) VALUES("Bonaire, Saint Eustatius and Saba", "BQ", @region_id, 0);

-- CRM-9714
ALTER TABLE `civicrm_price_set` DROP INDEX `UI_title`;

ALTER TABLE `civicrm_price_set` ADD `is_quick_config` TINYINT( 4 ) NOT NULL DEFAULT '0' COMMENT 'Is set if information from the Regular Fees section is being stored as price set' AFTER `contribution_type_id`;


-- CRM-9714 create a default price set for contribution and membership
INSERT INTO `civicrm_price_set` ( `name`, `title`, `is_active`, `extends`, `is_quick_config`) 
VALUES ( 'default_contribution_amount', 'Contribution Amount', '1', '2', '1'),
 ( 'default_membership_type_amount', 'Membership Amount', '1', '3', '1');

SELECT @setID := max(id) FROM civicrm_price_set WHERE name = 'default_contribution_amount' AND extends = 2 AND is_quick_config = 1 ;

INSERT INTO `civicrm_price_field` (`price_set_id`, `name`, `label`, `html_type`,`weight`, `is_display_amounts`, `options_per_line`, `is_active`, `is_required`,`visibility_id` ) 
VALUES ( @setID, 'contribution_amount', 'Contribution Amount', 'Text', '1', '1', '1', '1', '1', '1' );

SELECT @fieldID := max(id) FROM civicrm_price_field WHERE name = 'contribution_amount' AND price_set_id = @setID;

INSERT INTO `civicrm_price_field_value` (  `price_field_id`, `name`, `label`, `amount`, `weight`, `is_default`, `is_active`) 
VALUES ( @fieldID, 'contribution_amount', 'Contribution Amount', '1', '1', '0', '1');
ALTER TABLE `civicrm_custom_group` CHANGE `extends_entity_column_value` `extends_entity_column_value` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT 'linking custom group for dynamic object.';

-- CRM-9714 create price fields for all membershiptype
SELECT @setID := max(id) FROM civicrm_price_set WHERE name = 'default_membership_type_amount' AND extends = 3 AND is_quick_config = 1 ; 

INSERT INTO civicrm_price_field ( price_set_id, name, label, html_type, help_pre, is_display_amounts, is_required )
SELECT @setID as price_set_id,  CONCAT_WS( '_', LOWER( REPLACE( TRIM( cc.sort_name ), ' ', '_' ) ), CAST(cc.id AS CHAR)) as name, cc.sort_name as label, 'Radio' as html_type, CONCAT('id_',CAST(cc.id AS CHAR)) as help_pre, 0 as is_display_amounts, 0 as is_required
FROM `civicrm_membership_type` cmt
LEFT JOIN civicrm_contact cc ON cmt.member_of_contact_id = cc.id
GROUP BY cc.id;

INSERT INTO civicrm_price_field_value ( price_field_id, name, label, description, amount, membership_type_id)
SELECT 
cpf.id, cmt.name as label1, cmt.name as label2, cmt.description, cmt.minimum_fee, cmt.id
FROM `civicrm_membership_type` cmt
LEFT JOIN civicrm_contact cc ON cmt.member_of_contact_id = cc.id
LEFT JOIN civicrm_price_field cpf ON cc.id = SUBSTRING(cpf.help_pre,4);

UPDATE civicrm_price_field SET help_pre = NULL WHERE price_set_id = @setID;

-- CRM-9714
SELECT @fieldID := cpf.id, @fieldValueID := cpfv.id FROM civicrm_price_set cps 
LEFT JOIN civicrm_price_field cpf ON  cps.id = cpf.price_set_id 
LEFT JOIN civicrm_price_field_value cpfv ON cpf.id = cpfv.price_field_id
WHERE cps.name = 'default_contribution_amount';

INSERT INTO civicrm_line_item ( entity_table, entity_id, price_field_id, label, qty, unit_price, line_total, participant_count, price_field_value_id )
SELECT 'civicrm_contribution', cc.id, @fieldID, 'Contribution Amount', ROUND(total_amount,0), '1.00', total_amount , 0, @fieldValueID 
FROM `civicrm_contribution` cc
LEFT JOIN civicrm_line_item cli ON cc.id=cli.entity_id and cli.entity_table = 'civicrm_contribution'
LEFT JOIN civicrm_membership_payment cmp ON cc.id = cmp.contribution_id
LEFT JOIN civicrm_participant_payment cpp ON cc.id = cpp.contribution_id
WHERE cli.entity_id IS NULL AND cc.contribution_page_id IS NULL AND cmp.contribution_id IS NULL AND cpp.contribution_id IS NULL
GROUP BY cc.id;

-- CRM-10071 contribution membership detail report template
SELECT @option_group_id_report := MAX(id)     FROM civicrm_option_group WHERE name = 'report_template';
SELECT @weight                 := MAX(weight) FROM civicrm_option_value WHERE option_group_id = @option_group_id_report;
SELECT @contributeCompId := max(id) FROM civicrm_component where name = 'CiviContribute';
INSERT INTO civicrm_option_value
  (option_group_id, {localize field='label'}label{/localize}, value, name, weight, {localize field='description'}description{/localize}, is_active, component_id) VALUES
  (@option_group_id_report, {localize}'Contribution and Membership Details'{/localize}, 'contribute/membershipDetail', 'CRM_Report_Form_Contribute_MembershipDetail', @weight := @weight + 1, {localize}'Contribution and Membership Details'{/localize}, 0, @contributeCompId);

INSERT INTO `civicrm_report_instance`
    ( `domain_id`, `title`, `report_id`, `description`, `permission`, `form_values`)
VALUES 
    ( @domainID, 'Contribution and Membership Details', 'contribute/membershipDetail', 'Contribution and Membership Details', '', '{literal}a:67:{s:6:"fields";a:12:{s:9:"sort_name";s:1:"1";s:5:"email";s:1:"1";s:5:"phone";s:1:"1";s:20:"contribution_type_id";s:1:"1";s:12:"receive_date";s:1:"1";s:12:"total_amount";s:1:"1";s:18:"membership_type_id";s:1:"1";s:21:"membership_start_date";s:1:"1";s:19:"membership_end_date";s:1:"1";s:9:"join_date";s:1:"1";s:22:"membership_status_name";s:1:"1";s:10:"country_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"id_min";s:0:"";s:6:"id_max";s:0:"";s:5:"id_op";s:3:"lte";s:8:"id_value";s:0:"";s:21:"receive_date_relative";s:1:"0";s:17:"receive_date_from";s:0:"";s:15:"receive_date_to";s:0:"";s:23:"contribution_type_id_op";s:2:"in";s:26:"contribution_type_id_value";a:0:{}s:24:"payment_instrument_id_op";s:2:"in";s:27:"payment_instrument_id_value";a:0:{}s:25:"contribution_status_id_op";s:2:"in";s:28:"contribution_status_id_value";a:0:{}s:16:"total_amount_min";s:0:"";s:16:"total_amount_max";s:0:"";s:15:"total_amount_op";s:3:"lte";s:18:"total_amount_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:13:"ordinality_op";s:2:"in";s:16:"ordinality_value";a:0:{}s:18:"join_date_relative";s:1:"0";s:14:"join_date_from";s:0:"";s:12:"join_date_to";s:0:"";s:30:"membership_start_date_relative";s:1:"0";s:26:"membership_start_date_from";s:0:"";s:24:"membership_start_date_to";s:0:"";s:28:"membership_end_date_relative";s:1:"0";s:24:"membership_end_date_from";s:0:"";s:22:"membership_end_date_to";s:0:"";s:23:"owner_membership_id_min";s:0:"";s:23:"owner_membership_id_max";s:0:"";s:22:"owner_membership_id_op";s:3:"lte";s:25:"owner_membership_id_value";s:0:"";s:6:"tid_op";s:2:"in";s:9:"tid_value";a:0:{}s:6:"sid_op";s:2:"in";s:9:"sid_value";a:0:{}s:17:"street_number_min";s:0:"";s:17:"street_number_max";s:0:"";s:16:"street_number_op";s:3:"lte";s:19:"street_number_value";s:0:"";s:14:"street_name_op";s:3:"has";s:17:"street_name_value";s:0:"";s:15:"postal_code_min";s:0:"";s:15:"postal_code_max";s:0:"";s:14:"postal_code_op";s:3:"lte";s:17:"postal_code_value";s:0:"";s:7:"city_op";s:3:"has";s:10:"city_value";s:0:"";s:12:"county_id_op";s:2:"in";s:15:"county_id_value";a:0:{}s:20:"state_province_id_op";s:2:"in";s:23:"state_province_id_value";a:0:{}s:13:"country_id_op";s:2:"in";s:16:"country_id_value";a:0:{}s:8:"tagid_op";s:2:"in";s:11:"tagid_value";a:0:{}s:11:"description";s:35:"Contribution and Membership Details";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:9:"domain_id";i:1;}{s:6:"fields";a:12:{s:9:"sort_name";s:1:"1";s:5:"email";s:1:"1";s:5:"phone";s:1:"1";s:20:"contribution_type_id";s:1:"1";s:12:"receive_date";s:1:"1";s:12:"total_amount";s:1:"1";s:18:"membership_type_id";s:1:"1";s:21:"membership_start_date";s:1:"1";s:19:"membership_end_date";s:1:"1";s:9:"join_date";s:1:"1";s:22:"membership_status_name";s:1:"1";s:10:"country_id";s:1:"1";}s:12:"sort_name_op";s:3:"has";s:15:"sort_name_value";s:0:"";s:6:"id_min";s:0:"";s:6:"id_max";s:0:"";s:5:"id_op";s:3:"lte";s:8:"id_value";s:0:"";s:21:"receive_date_relative";s:1:"0";s:17:"receive_date_from";s:0:"";s:15:"receive_date_to";s:0:"";s:23:"contribution_type_id_op";s:2:"in";s:26:"contribution_type_id_value";a:0:{}s:24:"payment_instrument_id_op";s:2:"in";s:27:"payment_instrument_id_value";a:0:{}s:25:"contribution_status_id_op";s:2:"in";s:28:"contribution_status_id_value";a:0:{}s:16:"total_amount_min";s:0:"";s:16:"total_amount_max";s:0:"";s:15:"total_amount_op";s:3:"lte";s:18:"total_amount_value";s:0:"";s:6:"gid_op";s:2:"in";s:9:"gid_value";a:0:{}s:13:"ordinality_op";s:2:"in";s:16:"ordinality_value";a:0:{}s:18:"join_date_relative";s:1:"0";s:14:"join_date_from";s:0:"";s:12:"join_date_to";s:0:"";s:30:"membership_start_date_relative";s:1:"0";s:26:"membership_start_date_from";s:0:"";s:24:"membership_start_date_to";s:0:"";s:28:"membership_end_date_relative";s:1:"0";s:24:"membership_end_date_from";s:0:"";s:22:"membership_end_date_to";s:0:"";s:23:"owner_membership_id_min";s:0:"";s:23:"owner_membership_id_max";s:0:"";s:22:"owner_membership_id_op";s:3:"lte";s:25:"owner_membership_id_value";s:0:"";s:6:"tid_op";s:2:"in";s:9:"tid_value";a:0:{}s:6:"sid_op";s:2:"in";s:9:"sid_value";a:0:{}s:17:"street_number_min";s:0:"";s:17:"street_number_max";s:0:"";s:16:"street_number_op";s:3:"lte";s:19:"street_number_value";s:0:"";s:14:"street_name_op";s:3:"has";s:17:"street_name_value";s:0:"";s:15:"postal_code_min";s:0:"";s:15:"postal_code_max";s:0:"";s:14:"postal_code_op";s:3:"lte";s:17:"postal_code_value";s:0:"";s:7:"city_op";s:3:"has";s:10:"city_value";s:0:"";s:12:"county_id_op";s:2:"in";s:15:"county_id_value";a:0:{}s:20:"state_province_id_op";s:2:"in";s:23:"state_province_id_value";a:0:{}s:13:"country_id_op";s:2:"in";s:16:"country_id_value";a:0:{}s:8:"tagid_op";s:2:"in";s:11:"tagid_value";a:0:{}s:11:"description";s:35:"Contribution and Membership Details";s:13:"email_subject";s:0:"";s:8:"email_to";s:0:"";s:8:"email_cc";s:0:"";s:10:"permission";s:1:"0";s:9:"domain_id";i:1;}{/literal}');

SELECT @reportlastID       := MAX(id) FROM civicrm_navigation where name = 'Reports';
SELECT @nav_max_weight     := MAX(ROUND(weight)) from civicrm_navigation WHERE parent_id = @reportlastID;

SET @instanceID:=LAST_INSERT_ID();
INSERT INTO civicrm_navigation
    ( domain_id, url, label, name, permission, permission_operator, parent_id, is_active, has_separator, weight )
VALUES
    ( @domainID, CONCAT('civicrm/report/instance/', @instanceID,'&reset=1'), '{ts escape="sql" skip="true"}Contribution and Membership Details{/ts}', 'Contribution and Membership Details', 'access CiviMember,access CiviContribute', 'AND', @reportlastID, '1', NULL, @instanceID+2 );
UPDATE civicrm_report_instance SET navigation_id = LAST_INSERT_ID() WHERE id = @instanceID;