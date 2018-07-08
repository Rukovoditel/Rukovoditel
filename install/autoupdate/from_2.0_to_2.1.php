<?php

define('TEXT_UPDATE_VERSION_FROM','2.0');
define('TEXT_UPDATE_VERSION_TO','2.1');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
  $tables_array[] = current($tables);      
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('app_users_alerts',$tables_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
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