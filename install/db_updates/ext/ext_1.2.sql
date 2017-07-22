CREATE TABLE IF NOT EXISTS `app_ext_functions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `functions_name` varchar(32) NOT NULL,
  `functions_formula` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;