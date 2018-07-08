<?php

define('TEXT_UPDATE_VERSION_FROM','1.6');
define('TEXT_UPDATE_VERSION_TO','1.7');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
  $tables_array[] = current($tables);      
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_currencies',$tables_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
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