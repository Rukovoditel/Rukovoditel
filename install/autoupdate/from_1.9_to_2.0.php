<?php

define('TEXT_UPDATE_VERSION_FROM','1.9');
define('TEXT_UPDATE_VERSION_TO','2.0');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_reports");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('in_dashboard_icon',$columns_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
CREATE TABLE IF NOT EXISTS `app_forms_fields_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `choices` text NOT NULL,
  `visible_fields` text NOT NULL,
  `hidden_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `app_reports` ADD `in_dashboard_icon` TINYINT(1) NOT NULL AFTER `in_dashboard_counter`, ADD `in_dashboard_counter_color` VARCHAR(16) NOT NULL AFTER `in_dashboard_icon`, ADD `in_dashboard_counter_fields` VARCHAR(255) NOT NULL AFTER `in_dashboard_counter_color`;
ALTER TABLE `app_entities_access` CHANGE `access_schema` `access_schema` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `app_reports` ADD `in_header_autoupdate` TINYINT(1) NOT NULL AFTER `in_header`;
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