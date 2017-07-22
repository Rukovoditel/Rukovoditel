<?php

define('TEXT_UPDATE_VERSION_FROM','1.8');
define('TEXT_UPDATE_VERSION_TO','1.9');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_reports");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('displays_assigned_only',$columns_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
CREATE TABLE IF NOT EXISTS `app_users_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `date_added` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_uei` (`users_id`,`entities_id`) USING BTREE,
  KEY `idx_created_by` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_entities` ADD `notes` TEXT NOT NULL AFTER `name`;
ALTER TABLE `app_fields` ADD `notes` TEXT NOT NULL AFTER `tooltip_display_as`;

CREATE TABLE IF NOT EXISTS `app_comments_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_fields` ADD `comments_forms_tabs_id` INT NOT NULL DEFAULT '0' AFTER `forms_tabs_id`;
ALTER TABLE `app_fields` ADD INDEX `idx_comments_forms_tabs_id` (`comments_forms_tabs_id`);
ALTER TABLE `app_reports` ADD `displays_assigned_only` TINYINT(1) NOT NULL DEFAULT '0' AFTER `users_groups`;
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