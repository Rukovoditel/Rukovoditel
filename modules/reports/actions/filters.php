<?php

$current_reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$current_reports_info = db_fetch_array($current_reports_info_query))
{
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('reports/');
}

switch($app_module_action)
{
  case 'save':
    
		    $values = '';
		    
		    if(isset($_POST['values']))
		    {
		      if(is_array($_POST['values']))
		      {
		        $values = implode(',',$_POST['values']);
		      }
		      else
		      {
		        $values = $_POST['values'];
		      }
		    }
		    $sql_data = array('reports_id'=>(isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id']:$_GET['reports_id']),
		                      'fields_id'=>$_POST['fields_id'],
		                      'filters_condition'=>isset($_POST['filters_condition']) ? $_POST['filters_condition']: '',                                              
		                      'filters_values'=>$values,
		    									'is_active'=>$_POST['is_active'],
		                      );
		        
		    if(isset($_GET['id']))
		    {        
		      db_perform('app_reports_filters',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
		      $filters_id = $_GET['id'];
		    }
		    else
		    {               
		      db_perform('app_reports_filters',$sql_data);
		      $filters_id = db_insert_id();
		    }
		    
		    if(isset($_POST['save_as_template']))
		    {
		    	$filters_info = db_find('app_reports_filters',$filters_id);
		    
		    	$check_query = db_query("select count(*) as total from app_reports_filters_templates where fields_id='" . db_input($filters_info['fields_id']) . "' and filters_condition='" . db_input($filters_info['filters_condition']) . "' and filters_values='" . db_input($filters_info['filters_values']) . "' and users_id='" . db_input($app_logged_users_id) . "'");
		    	$check = db_fetch_array($check_query);
		    
		    	if($check['total']==0 and strlen($filters_info['filters_values'])>0)
		    	{
		    		$sql_data = array(
		    				'fields_id'=>$filters_info['fields_id'],
		    				'filters_condition'=>$filters_info['filters_condition'],
		    				'filters_values'=>$filters_info['filters_values'],
		    				'users_id'=>$app_logged_users_id,
		    		);
		    			
		    		db_perform('app_reports_filters_templates',$sql_data);
		    	}		    
		    }
                 
  	break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        if($_GET['id']=='all')
        {
          db_query("delete from app_reports_filters where reports_id='" . db_input((isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id']:$_GET['reports_id'])) . "'");
          $alerts->add(TEXT_WARN_DELETE_ALL_FILTERS_SUCCESS,'success');
        }
        else
        {
          db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");          
          //$alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS,'success');
        }
         
      }
    break;  

   case 'use_filters_template':
		   	if(isset($_GET['templates_id']))
		   	{
		   		$template_info = db_find('app_reports_filters_templates',$_GET['templates_id']);
		   		
		   		if(isset($_GET['id']))
		   		{
			   		$sql_data = array(			   				
			   				'filters_condition'=>$template_info['filters_condition'],
			   				'filters_values'=>$template_info['filters_values'],			   				
			   		);
			   				   		
			   		db_perform('app_reports_filters',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
		   		}
		   	}
  		break;
   case 'delete_filters_templates':
   		
   			db_query("delete from app_reports_filters_templates where id='" . db_input($_GET['templates_id']) . "' and users_id='" . db_input($app_logged_users_id) . "'");
   			
   		exit();
   	break;
}

if(strlen($app_module_action)>0)
{
	plugins::handle_action('filters_redirect');
	
	switch($app_redirect_to)
	{
		case 'listing':
			redirect_to('items/items','path=' . $app_path);
			break;
		case 'report':
			redirect_to('reports/view','reports_id=' . $_GET['reports_id']);
			break;
		default:
			redirect_to('reports/filters','reports_id=' . $_GET['reports_id']);
			break;
	}	
}
