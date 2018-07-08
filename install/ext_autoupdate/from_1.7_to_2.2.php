<?php

define('TEXT_UPDATE_VERSION_FROM','1.7');
define('TEXT_UPDATE_VERSION_TO','2.2');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
  $tables_array[] = current($tables);      
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_kanban',$tables_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
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
";
    
  db_query_from_content(trim($sql));
  
//extra code for update
   
//if there are no any errors display success message    
  echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
  echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');