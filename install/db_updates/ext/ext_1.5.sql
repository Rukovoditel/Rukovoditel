CREATE TABLE IF NOT EXISTS `app_ext_timeline_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL DEFAULT '',
  `allowed_groups` text NOT NULL,
  `use_background` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `app_ext_chat_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_chat_conversations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `menu_icon_color` varchar(16) NOT NULL,
  `assigned_to` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_chat_conversations_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `conversations_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachments` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_conversations_id` (`conversations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachments` text NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_users_assigned` (`users_id`,`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_chat_unread_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `messages_id` int(11) NOT NULL,
  `conversations_id` int(11) NOT NULL,
  `notification_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_assigned_to` (`assigned_to`),
  KEY `idx_messages_id` (`messages_id`),
  KEY `idx_conversations_id` (`conversations_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_chat_users_online` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `date_check` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_calendar` ADD `use_background` INT NOT NULL AFTER `heading_template`;
ALTER TABLE `app_ext_calendar` ADD `fields_in_popup` TEXT NOT NULL AFTER `use_background`;
ALTER TABLE `app_ext_ipages` ADD `is_menu` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sort_order`;
ALTER TABLE `app_ext_ipages` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `app_ext_functions` ADD `notes` TEXT NOT NULL AFTER `name`;
ALTER TABLE `app_ext_ganttchart` ADD `use_background` INT NOT NULL DEFAULT '0' AFTER `fields_in_listing`;