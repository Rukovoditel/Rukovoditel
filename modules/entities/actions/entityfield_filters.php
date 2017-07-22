<?php

$fields_info_query = db_query("select * from app_fields where id='" . $_GET['fields_id']. "'");
if(!$fields_info = db_fetch_array($fields_info_query))
{
	redirect_to('entities/fields','entities_id=' . $_GET['entities_id']);
}	

$cfg = new fields_types_cfg($fields_info['configuration']);

$entity_id = $cfg->get('entity_id');

$reports_type = 'entityfield' . $_GET['fields_id']; 
$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($entity_id). "' and reports_type='{$reports_type}'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  $sql_data = array('name'=>'',
                    'entities_id'=>$entity_id,
                    'reports_type'=>$reports_type,                                              
                    'in_menu'=>0,
                    'in_dashboard'=>0,
                    'created_by'=>0,
                    );
  db_perform('app_reports',$sql_data);
  
  redirect_to('entities/entityfield_filters','entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id']);
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
        
    redirect_to('entities/entityfield_filters','reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id']);      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {      

        db_query("delete from app_reports_filters where id='" . db_input($_GET['id']) . "'");
                            
        $alerts->add(TEXT_WARN_DELETE_FILTER_SUCCESS,'success');
     
                
        redirect_to('entities/entityfield_filters','reports_id=' . $_GET['reports_id'] . '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id']);  
      }
    break;   
}
