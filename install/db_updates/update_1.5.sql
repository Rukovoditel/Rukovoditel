CREATE TABLE IF NOT EXISTS `app_related_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `related_entities_id` int(11) NOT NULL,
  `related_items_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_related_entities_id` (`related_entities_id`),
  KEY `idx_related_items_id` (`related_items_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `app_reports` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `app_entities` ADD `display_in_menu` TINYINT(1) NULL DEFAULT '0' AFTER `name`;

CREATE TABLE IF NOT EXISTS `app_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_token` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;