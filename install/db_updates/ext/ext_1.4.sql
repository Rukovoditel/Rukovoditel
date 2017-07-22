CREATE TABLE IF NOT EXISTS `app_ext_pivotreports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `allowed_groups` text NOT NULL,
  `cfg_numer_format` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `reports_settings` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_pivotreports_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pivotreports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_name` varchar(64) NOT NULL,
  `cfg_date_format` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pivotreports_id` (`pivotreports_id`),
  KEY `idx_entitites_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_ext_entities_templates` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `app_ext_comments_templates` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `app_ext_export_templates` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `app_ext_export_templates` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `sort_order`;
ALTER TABLE `app_ext_entities_templates` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `sort_order`;
ALTER TABLE `app_ext_comments_templates` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `sort_order`;

ALTER TABLE `app_ext_ganttchart_depends` ADD `entities_id` INT NOT NULL AFTER `ganttchart_id`, ADD INDEX `idx_entities_id` (`entities_id`);