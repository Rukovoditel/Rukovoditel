<?php

$current_path = false;

if(isset($_GET['path']))
{
  $current_path = $_GET['path']; 
}
elseif(isset($_POST['path']))
{
  $current_path = $_POST['path'];
}

if(!$current_path)
{
  redirect_to('dashboard/');
}

$current_path_array = explode('/',$current_path);
$current_item_array = explode('-',$current_path_array[count($current_path_array)-1]);

$current_entity_id = $current_item_array[0];
$current_item_id = (isset($current_item_array[1]) ? $current_item_array[1] : 0);

//check if entity exist
if($current_entity_id>0)
{
  $tables_list = array();
  $tables_query = db_query("show tables");
  while($tables = db_fetch_array($tables_query))
  {
    $tables_list[] =  current($tables);    
  }
  
  if(!in_array('app_entity_' . $current_entity_id,$tables_list))
  {
    redirect_to('dashboard/page_not_found');
  }    
}

//check if item exist
if($current_item_id>0)
{
  //check if item exist including access to parent item
  $item_query = db_query("select * from app_entity_" . $current_entity_id . " e where e.id='" . db_input($current_item_id) . "' " . items::add_access_query_for_parent_entities($current_entity_id));
  
  if(!db_fetch_array($item_query))
  {
    redirect_to('dashboard/page_not_found');
  }
      
//check path to item
  $path_info = items::get_path_info($current_entity_id,$current_item_id);
  
  if($current_path!=$path_info['full_path'])
  {
    redirect_to('items/info','path=' . $path_info['full_path']);
  }  
}
  
if(count($current_path_array)>1)
{
  $v = explode('-',$current_path_array[count($current_path_array)-2]);
  $parent_entity_id = $v[0];
  $parent_entity_item_id = $v[1];
  
//check path to entity  
  if($current_item_id==0)
  {
    $path_info = items::get_path_info($v[0],$v[1]);
  
    if($current_path!=$path_info['full_path'] . '/' . $current_entity_id)
    {       
     redirect_to('items/items','path=' . $path_info['full_path'] . '/' . $current_entity_id);
    }
    
    //if path is corret then check access to parent item including check access to other parent items
    $item_query = db_query("select * from app_entity_" . $parent_entity_id . " e where e.id='" . db_input($parent_entity_item_id) . "' "  . items::add_access_query($parent_entity_id,'') . ' '. items::add_access_query_for_parent_entities($parent_entity_id));

    if(!db_fetch_array($item_query))
    {
      redirect_to('dashboard/page_not_found');
    }
  }
}
else
{
  $parent_entity_item_id = 0;
}


$app_breadcrumb = items::get_breadcrumb($current_path_array); 

//get access schema for current entity
$current_access_schema = users::get_entities_access_schema($current_entity_id,$app_user['group_id']);

//get comments access schema for current entity
$current_comments_access_schema = users::get_comments_access_schema($current_entity_id,$app_user['group_id']);

//checking view access
if(!users::has_access('view') and  !users::has_access('view_assigned'))
{
  redirect_to('dashboard/access_forbidden');
}

//check assigned access
if(users::has_access('view_assigned') and $app_user['group_id']>0 and $current_item_id>0)
{
  if(!users::has_access_to_assigned_item($current_entity_id,$current_item_id))
  {
    redirect_to('dashboard/access_forbidden'); 
  }
}
