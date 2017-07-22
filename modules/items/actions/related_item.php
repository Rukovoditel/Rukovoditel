<?php

if(!users::has_access('update'))
{        
  redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{  
  case 'remove_related_items';
      if(isset($_POST['items']))
      {
      	$table_info = related_records::get_related_items_table_name($current_entity_id,$_POST['related_entities_id']);
      	
        foreach($_POST['items'] as $id)
        {
          db_delete_row($table_info['table_name'], $id);
        }
      }
      
      redirect_to('items/info','path=' . $_GET['path']);
    break;
  case 'remove_related_item':
    	
  	  $table_info = related_records::get_related_items_table_name($current_entity_id,$_GET['related_entity_id']);
  		db_delete_row($table_info['table_name'], $_GET['id']);            
        
      redirect_to('items/info','path=' . $_GET['path']);
    
    exit();
  break;
    
  case 'add_related_item':
                
      if(isset($_POST['items']) and isset($_POST['related_entities_id']))
      {
        $related_entities_id = (int)$_POST['related_entities_id'];
        
        $table_info = related_records::get_related_items_table_name($current_entity_id,$related_entities_id);
                
        foreach($_POST['items'] as $related_items_id)
        {
          $check_query = db_query("select * from " . $table_info['table_name'] . " where entity_" . $current_entity_id . "_items_id=" . (int)$current_item_id . " and entity_" . $related_entities_id  . $table_info['sufix'] . "_items_id = " . (int)$related_items_id . "");
          if(!$check = db_fetch_array($check_query))
          {            
            $sql_data = array('entity_' . $current_entity_id . '_items_id' => $current_item_id,                              
                              'entity_' . $related_entities_id  . $table_info['sufix'] . '_items_id' => $related_items_id);
                              
            db_perform($table_info['table_name'],$sql_data);
                        
          }
        }
      }
      
      redirect_to('items/info','path=' . $_GET['path']);
      
    break; 
}