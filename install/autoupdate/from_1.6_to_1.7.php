<?php

define('TEXT_UPDATE_VERSION_FROM','1.6');
define('TEXT_UPDATE_VERSION_TO','1.7');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_reports");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('fields_in_listing',$columns_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
CREATE TABLE IF NOT EXISTS `app_choices_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_reports` ADD `in_header` TINYINT(1) NOT NULL DEFAULT '0' AFTER `in_dashboard`;

ALTER TABLE `app_reports` ADD `fields_in_listing` TEXT NULL AFTER `parent_item_id`, ADD `rows_per_page` INT NOT NULL DEFAULT '0' AFTER `fields_in_listing`;

ALTER TABLE `app_reports` ADD `menu_icon` VARCHAR(64) NOT NULL DEFAULT '' AFTER `name`;

ALTER TABLE `app_fields` ADD `tooltip_display_as` VARCHAR(16) NOT NULL DEFAULT '' AFTER `tooltip`;

CREATE TABLE IF NOT EXISTS `app_users_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_user_filters_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filters_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text,
  `filters_condition` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_filters_id` (`filters_id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_global_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_global_lists_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `lists_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_lists_id` (`lists_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
    
  db_query_from_content(trim($sql));
  
  //extra code for update
    
  $enitites_query = db_query("select * from app_entities order by name");
  while($enitites = db_fetch_array($enitites_query))
  {
    db_query("ALTER TABLE `app_entity_" . $enitites['id'] . "` ADD INDEX `idx_created_by` (`created_by`)");
  }


//if there are no any errors display success message    
  echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
  echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');