<?php

$app_title = app_set_title(TEXT_HEADING_REPORTS);

switch($app_module_action)
{
  case 'save':
    $sql_data = array('name'=>db_prepare_input($_POST['name']),
                      'entities_id'=>$_POST['entities_id'],
                      'reports_type'=>'standard',
                      'menu_icon'=>$_POST['menu_icon'],                                              
                      'in_menu'=>(isset($_POST['in_menu']) ? $_POST['in_menu']:0),
                      'in_dashboard'=>(isset($_POST['in_dashboard']) ? $_POST['in_dashboard']:0),
                      'in_dashboard_counter'=>(isset($_POST['in_dashboard_counter']) ? $_POST['in_dashboard_counter']:0),                      
                      'in_header'=>(isset($_POST['in_header']) ? $_POST['in_header']:0),
                      'created_by'=>$app_logged_users_id,
                      'notification_days'=>(isset($_POST['notification_days']) ? implode(',',$_POST['notification_days']):''),
                      'notification_time'=>(isset($_POST['notification_time']) ? implode(',',$_POST['notification_time']):''),
                      );
        
    if(isset($_GET['id']))
    {        
      
      $report_info = db_find('app_reports',$_GET['id']);
      
      //check reprot entity and if it's changed remove report filters and parent reports
      if($report_info['entities_id']!=$_POST['entities_id'])
      {
        db_query("delete from app_reports_filters where reports_id='" . db_input($_GET['id']) . "'");
        
        //delete paretn reports
        reports::delete_parent_reports($_GET['id']);
        $sql_data['parent_id']=0;
      }
      
      db_perform('app_reports',$sql_data,'update',"id='" . db_input($_GET['id']) . "' and created_by='" . $app_logged_users_id . "'");       
    }
    else
    {                     
      db_perform('app_reports',$sql_data);   
      
      $insert_id = db_insert_id();
      
      reports::auto_create_parent_reports($insert_id);               
    }
        
    redirect_to('reports/');      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {  
        $report_info_query = db_query("select * from app_reports where id='" . db_input($_GET['id']). "' and created_by='" . db_input($app_logged_users_id) . "'");
        if($report_info = db_fetch_array($report_info_query))
        {          
          reports::delete_reports_by_id($report_info['id']);
                           
          $alerts->add(TEXT_WARN_DELETE_REPORT_SUCCESS,'success');
        }
        else
        {
        
        }
                     
        redirect_to('reports/');  
      }
    break;   
}