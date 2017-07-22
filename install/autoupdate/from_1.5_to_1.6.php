<?php

define('TEXT_UPDATE_VERSION_FROM','1.5');
define('TEXT_UPDATE_VERSION_TO','1.6');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_reports");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('users_groups',$columns_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
ALTER TABLE `app_reports` ADD `users_groups` TEXT NULL ;

ALTER TABLE `app_attachments` ADD `container` VARCHAR(16) NOT NULL DEFAULT '' ;

ALTER TABLE `app_reports` ADD `parent_entity_id` INT NOT NULL DEFAULT '0' , ADD `parent_item_id` INT NOT NULL DEFAULT '0' ;
ALTER TABLE `app_reports` ADD INDEX `idx_parent_id` (`parent_id`);
ALTER TABLE `app_reports` ADD INDEX `idx_parent_entity_id` (`parent_entity_id`);
ALTER TABLE `app_reports` ADD INDEX `idx_parent_item_id` (`parent_item_id`);
";
    
  db_query_from_content(trim($sql));
  
//extra code for update
  
  //update tables to InnoDB 
  $tables_query = db_query("show tables");
  while($tables = db_fetch_array($tables_query))
  {
    db_query("ALTER TABLE " . current($tables) . " ENGINE = InnoDB;");      
  }

//if there are no any errors display success message    
  echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
  echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');