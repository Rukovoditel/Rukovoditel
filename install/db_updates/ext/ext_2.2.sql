CREATE TABLE IF NOT EXISTS `app_ext_kanban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `heading_template` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `group_by_field` int(11) NOT NULL,
  `fields_in_listing` text NOT NULL,
  `sum_by_field` text NOT NULL,
  `width` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_export_templates` ADD `page_orientation` VARCHAR(16) NOT NULL AFTER `template_css`;

ALTER TABLE `app_ext_public_forms` ADD `after_submit_action` VARCHAR(32) NOT NULL AFTER `successful_sending_message`, ADD `after_submit_redirect` VARCHAR(255) NOT NULL AFTER `after_submit_action`;
ALTER TABLE `app_ext_public_forms` ADD `disable_submit_form` TINYINT(1) NOT NULL AFTER `check_enquiry`;

CREATE TABLE IF NOT EXISTS `app_ext_smart_input_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_modules_id` (`modules_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;