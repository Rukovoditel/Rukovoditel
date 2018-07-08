<?php

if(!users::has_reports_access())
{
	redirect_to('dashboard/access_forbidden');
}

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
                      'in_dashboard_icon'=>(isset($_POST['in_dashboard_icon']) ? $_POST['in_dashboard_icon']:0),
                      'in_dashboard_counter_color'=>$_POST['in_dashboard_counter_color'],
                      'in_dashboard_counter_fields'=>(isset($_POST['in_dashboard_counter_fields']) ? implode(',',$_POST['in_dashboard_counter_fields']):''),
                      'in_header'=>(isset($_POST['in_header']) ? $_POST['in_header']:0),
                      'in_header_autoupdate'=>(isset($_POST['in_header_autoupdate']) ? $_POST['in_header_autoupdate']:0),                      
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
  case 'get_numeric_fields':
  	
  	$fields_access_schema = users::get_fields_access_schema(_post::int('entities_id'),$app_user['group_id']);
  	  	  	
  	$choices = array();
  	$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_input_numeric','fieldtype_input_numeric_comments','fieldtype_formula') and f.entities_id='" . _post::int('entities_id') . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
  	while($fields = db_fetch_array($fields_query))
  	{
  		if(isset($fields_access_schema[$fields['id']]))
  		{
  			if($fields_access_schema[$fields['id']] == 'hide') continue;
  		}
  		
  		$choices[$fields['id']] = $fields['name'];
  	}
  	
  	$html = '';
  	
  	if(count($choices))
  	{
  	 
  	$obj = db_find('app_reports',_get::int('id'));
  	 
  	$html = '
  					
  			<div class="form-group">
			  	<label class="col-md-4 control-label" for="hidden_fields">' .  tooltip_icon(TEXT_DASHBOARD_REPORT_EXTRA_FIELDS_INFO) . TEXT_EXTRA_FIELDS  . '</label>
			    <div class="col-md-8">' .  select_tag('in_dashboard_counter_fields[]',$choices,$obj['in_dashboard_counter_fields'],array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple'))  . '
			    </div>
			  </div>
  	
			  
			  <script>
			    appHandleChosen()
			    $(\'[data-toggle="tooltip"]\').tooltip()
			  </script>
  		';
  	}
  	
  	echo $html;
  	exit();
  	break;
}