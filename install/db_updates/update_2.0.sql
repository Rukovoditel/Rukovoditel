CREATE TABLE IF NOT EXISTS `app_forms_fields_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `choices` text NOT NULL,
  `visible_fields` text NOT NULL,
  `hidden_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_reports` ADD `in_dashboard_icon` TINYINT(1) NOT NULL AFTER `in_dashboard_counter`, ADD `in_dashboard_counter_color` VARCHAR(16) NOT NULL AFTER `in_dashboard_icon`, ADD `in_dashboard_counter_fields` VARCHAR(255) NOT NULL AFTER `in_dashboard_counter_color`;

ALTER TABLE `app_entities_access` CHANGE `access_schema` `access_schema` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `app_reports` ADD `in_header_autoupdate` TINYINT(1) NOT NULL AFTER `in_header`;