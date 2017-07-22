ALTER TABLE `app_reports` ADD `dashboard_sort_order` INT NULL AFTER `in_dashboard`;

CREATE TABLE IF NOT EXISTS `app_users_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL DEFAULT '',
  `configuration_value` text,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `app_comments_history` ADD INDEX `idx_comments_id` (`comments_id`);
ALTER TABLE `app_comments_history` ADD INDEX `idx_fields_id` (`fields_id`);
ALTER TABLE `app_fields_choices` ADD INDEX `idx_parent_id` (`parent_id`);