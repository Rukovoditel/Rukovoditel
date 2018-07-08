CREATE TABLE IF NOT EXISTS `app_users_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `location` varchar(16) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `assigned_to` text NOT NULL,
  `users_groups` text NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_users_alerts_viewed` (
  `users_id` int(11) NOT NULL,
  `alerts_id` int(11) NOT NULL,
  KEY `idx_ueser_alerts` (`users_id`,`alerts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;