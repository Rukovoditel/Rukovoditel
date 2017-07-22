<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('items/','path=' . $_GET['path']);
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
    $sql_data = array('reports_id'=>$_GET['reports_id'],
                      'fields_id'=>$_POST['fields_id'],
                      'filters_condition'=>$_POST['filters_condition'],                                              
                      'filters_values'=>$values,
                      );
        
    if(isset($_GET['id']))
    {        
      db_perform('app_reports_filters',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {               
      db_perform('app_reports_filters',$sql_data);                  
    }
        
    redirect_to('items/filters','reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path']);      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {      

        db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");
                            
        $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS,'success');
     
                
        redirect_to('items/filters','reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path']);  
      }
    break;   
}

$entity_info = db_find('app_entities',$current_entity_id);
$entity_cfg = entities::get_cfg($current_entity_id);

$entity_listing_heading = (strlen($entity_cfg['listing_heading'])>0 ? $entity_cfg['listing_heading'] : $entity_info['name']);