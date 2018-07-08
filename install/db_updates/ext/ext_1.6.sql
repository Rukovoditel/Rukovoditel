CREATE TABLE IF NOT EXISTS `app_ext_public_forms` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `button_save_title` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `successful_sending_message` text NOT NULL,
  `user_agreement` text NOT NULL,
  `hidden_fields` text NOT NULL,
  `customer_name` varchar(64) NOT NULL,
  `customer_email` int(11) NOT NULL,
  `customer_message_title` varchar(255) NOT NULL,
  `customer_message` text NOT NULL,
  `admin_name` varchar(64) NOT NULL,
  `admin_email` varchar(64) NOT NULL,
  `admin_notification` tinyint(1) NOT NULL,
  `check_enquiry` tinyint(1) NOT NULL,
  `check_page_title` varchar(255) NOT NULL,
  `check_page_description` text NOT NULL,
  `check_button_title` varchar(64) NOT NULL,
  `check_page_fields` text NOT NULL,
  `check_page_comments` tinyint(1) NOT NULL,
  `check_page_comments_heading` varchar(255) NOT NULL,
  `check_page_comments_fields` text NOT NULL,
  `notify_field_change` int(11) UNSIGNED NOT NULL,
  `notify_message_title` varchar(255) NOT NULL,
  `notify_message_body` text NOT NULL,
  `check_enquiry_fields` varchar(255) NOT NULL,
  `form_css` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_processes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `confirmation_text` text NOT NULL,
  `allow_comments` tinyint(1) UNSIGNED NOT NULL,
  `preview_prcess_actions` tinyint(1) UNSIGNED NOT NULL,
  `notes` text NOT NULL,
  `payment_modules` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_processes_actions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `process_id` int(64) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_processes_actions_fields` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `actions_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_actions_id` (`actions_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_ext_calendar` ADD `in_menu` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `entities_id`;
ALTER TABLE `app_ext_timeline_reports` ADD `in_menu` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `entities_id`;

CREATE TABLE IF NOT EXISTS `app_ext_modules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(32) NOT NULL,
  `module` varchar(64) NOT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_modules_cfg` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `modules_id` int(10) UNSIGNED NOT NULL,
  `cfg_key` varchar(64) NOT NULL,
  `cfg_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_sms_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `modules_id` int(11) NOT NULL,
  `action_type` varchar(64) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `monitor_fields_id` int(11) NOT NULL,
  `phone` varchar(32) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_modules_id` (`modules_id`),
  KEY `idx_monitor_fields_id` (`monitor_fields_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `app_ext_track_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `position` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `color_insert` varchar(7) NOT NULL,
  `color_update` varchar(7) NOT NULL,
  `color_comment` varchar(7) NOT NULL,
  `rows_per_page` smallint(6) NOT NULL,
  `keep_history` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `track_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `comments_id` int(11) NOT NULL,
  `date_added` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_comments_id` (`comments_id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_track_changes_log_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_log_id` (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_export_templates` ADD `template_filename` VARCHAR(255) NOT NULL AFTER `is_active`, ADD `template_css` TEXT NOT NULL AFTER `template_filename`;
ALTER TABLE `app_ext_pivotreports` ADD `view_mode` TINYINT(1) NOT NULL DEFAULT '0' AFTER `reports_settings`;

CREATE TABLE IF NOT EXISTS `app_ext_file_storage_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_file_storage_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `modules_id` int(11) NOT NULL,
  `fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_modules_id` (`modules_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
