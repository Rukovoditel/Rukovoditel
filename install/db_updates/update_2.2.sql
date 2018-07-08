ALTER TABLE `app_fields_choices` ADD `value` VARCHAR(64) NOT NULL AFTER `users`;

CREATE TABLE IF NOT EXISTS `app_access_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `choices` text NOT NULL,
  `users_groups` text NOT NULL,
  `access_schema` text NOT NULL,
  `fields_view_only_access` text NOT NULL,
  `comments_access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_access_rules_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `app_entities_menu` ADD `reports_list` TEXT NOT NULL AFTER `entities_list`;

CREATE TABLE IF NOT EXISTS `app_reports_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  `counters_list` text NOT NULL,
  `reports_list` text NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_reports_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `reports_groups_id` int(11) NOT NULL,
  `report_left` varchar(64) NOT NULL,
  `report_right` varchar(64) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_groups_id` (`reports_groups_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_access_groups` ADD `ldap_filter` VARCHAR(64) NOT NULL AFTER `is_ldap_default`;
