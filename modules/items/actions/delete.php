<?php
  
  $msg = '';   
   
  if($current_entity_id==1 and $_GET['id']==$app_logged_users_id)
  {
    $msg = TEXT_ERROR_USER_DELETE;
  } 
  
  if(!users::has_access('delete'))
  {
    $msg = TEXT_NO_ACCESS;
  }
  
  $subEntities = array();
  $entities_query = db_query("select * from app_entities where parent_id='" . $_GET['entity_id'] . "' order by sort_order, name");
  while($entities = db_fetch_array($entities_query))
  {
    if(db_count('app_entity_' . $entities['id'],$_GET['id'],'parent_item_id')>0)
    {
      $subEntities[] = $entities['name'];
    }
  }
  
  if(count($subEntities)>0)
  {
    $msg = sprintf(TEXT_ERROR_ITEM_HAS_SUB_ITEM,implode(', ',$subEntities));
  }
    
  if(strlen($msg)>0)
  {
    $heading = TEXT_WARNING;
    $content = $msg;
    $button_title = 'hide-save-button';
  }
  else
  {
    $item_info = db_find('app_entity_' . $_GET['entity_id'],$_GET['id']);    
    $heading_field_id = fields::get_heading_id($_GET['entity_id']);    
    $name = ($heading_field_id>0 ? items::get_heading_field_value($heading_field_id,$item_info) : $item_info['id']);
            
    $heading = TEXT_HEADING_DELETE; 
    $content =  sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION,$name);
    $button_title = TEXT_BUTTON_DELETE;
  }
  