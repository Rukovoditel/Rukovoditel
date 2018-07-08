<?php

$app_title = app_set_title(TEXT_USERS_ALERTS);

switch($app_module_action)
{
  case 'save':
    $sql_data = array(
    	'is_active'	=> (isset($_POST['is_active']) ? 1:0),
    	'type'	=> $_POST['type'],
    	'title'	=> $_POST['title'],
    	'description'	=> $_POST['description'],
    	'location' => $_POST['location'],
    	'start_date' => (int)get_date_timestamp($_POST['start_date']),
    	'end_date' => (int)get_date_timestamp($_POST['end_date']),
    	'users_groups' => (isset($_POST['users_groups']) ? implode(',',$_POST['users_groups']):''),
    	'assigned_to' => (isset($_POST['assigned_to']) ? implode(',',$_POST['assigned_to']):''),
    	'created_by' => $app_user['id'],
    	
    );
        
    if(isset($_GET['id']))
    {                  
      db_perform('app_users_alerts',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {                     
      db_perform('app_users_alerts',$sql_data);                             
    }
        
    redirect_to('users_alerts/');      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {  
        db_query("delete from app_users_alerts where id='" . _get::int('id') . "'");
        db_query("delete from app_users_alerts_viewed where alerts_id='" . _get::int('id') . "'");
                     
        redirect_to('users_alerts/');  
      }
    break; 

}