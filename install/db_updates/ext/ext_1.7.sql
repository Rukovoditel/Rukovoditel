CREATE TABLE IF NOT EXISTS `app_ext_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_default` tinyint(1) NOT NULL,
  `title` varchar(64) NOT NULL,
  `code` varchar(16) NOT NULL,
  `symbol` varchar(16) NOT NULL,
  `value` float(13,8) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_ext_ganttchart_depends` ADD `type` VARCHAR(1) NOT NULL AFTER `depends_id`;

CREATE TABLE IF NOT EXISTS `app_ext_funnelchart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(16) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `group_by_field` int(11) NOT NULL,
  `sum_by_field` text NOT NULL,
  `users_groups` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_processes_actions_fields` ADD `enter_manually` TINYINT(1) NOT NULL DEFAULT '0' AFTER `value`;