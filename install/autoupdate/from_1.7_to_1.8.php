<?php

define('TEXT_UPDATE_VERSION_FROM','1.7');
define('TEXT_UPDATE_VERSION_TO','1.8');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_reports");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('in_dashboard_counter',$columns_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
ALTER TABLE `app_reports` ADD `in_dashboard_counter` TINYINT(1) NOT NULL DEFAULT '0' AFTER `in_dashboard`;
ALTER TABLE `app_reports` ADD `dashboard_counter_sort_order` INT NOT NULL DEFAULT '0' AFTER `dashboard_sort_order`;
ALTER TABLE `app_reports` ADD `notification_days` VARCHAR(32) NOT NULL, ADD `notification_time` VARCHAR(255) NOT NULL AFTER `notification_days`;
ALTER TABLE `app_reports` ADD `header_sort_order` INT NOT NULL DEFAULT '0' AFTER `dashboard_sort_order`;

ALTER TABLE `app_reports_filters` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `filters_condition`;
ALTER TABLE `app_user_filters_values` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `filters_condition`;

ALTER TABLE `app_users_filters` ADD `fields_in_listing` TEXT NOT NULL AFTER `name`, ADD `listing_order_fields` TEXT NOT NULL AFTER `fields_in_listing`;

CREATE TABLE IF NOT EXISTS `app_reports_filters_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cidx` (`fields_id`,`users_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_items_export_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `templates_fields` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cidx` (`entities_id`,`users_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `entities_list` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(64) NOT NULL,
  `date_added` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_fields_choices` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `app_global_lists_choices` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE IF NOT EXISTS `app_emails_on_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` int(11) NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `email_to_name` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `email_from_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_users_search_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_users_reports_id` (`users_id`,`reports_id`),
  KEY `idx_reports_id` (`reports_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
    
  db_query_from_content(trim($sql));
  

//if there are no any errors display success message    
  echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
  echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');