<?php

//check if report exist  
$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{  
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('dashboard/');
}

switch($app_module_action)
{
  case 'use':
      $users_filters = new users_filters($_GET['reports_id']);
      $users_filters->use_filters($_GET['id']);
      $users_filters->ser_reports_settings($_GET['id']);      
    break;
  case 'save':                  
    $filters_id = (isset($_POST['filters_id']) ? (int)$_POST['filters_id'] : 0);
        
    if($filters_id>0)
    { 
      $sql_data = array(
      		'listing_order_fields' => $reports_info['listing_order_fields'],
        	'fields_in_listing' => $reports_info['fields_in_listing']      		
      );
      
      if(strlen($_POST['name']))
      {
        $sql_data['name'] = db_prepare_input($_POST['name']);        
      } 
      
      db_perform('app_users_filters',$sql_data,'update',"id='" . db_input($filters_id) . "' and users_id='" . db_input($app_user['id']) . "'");
    }
    else
    { 
      $sql_data = array('reports_id'=>$_GET['reports_id'],
                        'users_id'=>$app_user['id'],
                        'name'=>db_prepare_input($_POST['name']),
      									'listing_order_fields' => $reports_info['listing_order_fields'],
      									'fields_in_listing' => $reports_info['fields_in_listing'],
                        );              
      db_perform('app_users_filters',$sql_data);
      
      $filters_id = db_insert_id();                  
    }
    
    $users_filters = new users_filters($_GET['reports_id']);
    $users_filters->set_filters($filters_id);
    
    $alerts->add(TEXT_MESSAGE_FILTER_SAVED,'success');
                            
    break;
  case 'delete':      
      if(isset($_POST['filters']))
      {        
        $filters_query = db_query("select * from app_users_filters where id in (" . implode(',',$_POST['filters']) . ") and users_id='" . db_input($app_user['id']) . "'");
        while($filters = db_fetch_array($filters_query))
        {
          db_query("delete from app_users_filters where id='" . db_input($filters['id']) . "'");
          db_query("delete from app_user_filters_values where filters_id='" . db_input($filters['id']) . "'");
        }
      }    
    break;
}

plugins::handle_action('filters_redirect');

switch($app_redirect_to)
{
  case 'listing':
      redirect_to('items/items','path=' . $app_path);
    break;
  case 'report':
      redirect_to('reports/view','reports_id=' . $_GET['reports_id']);
    break;
}