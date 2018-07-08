<?php

$field_info_query = db_query("select * from app_fields where id='" . _get::int('fields_id'). "' and entities_id='" . _get::int('entities_id'). "'");
if(!$field_info = db_fetch_array($field_info_query))
{
	redirect_to('access_rules/fields','entities_id=' . _get::int('entities_id'));
}

switch($app_module_action)
{
	case 'save':
				
		$sql_data = array(
			'entities_id'=>$_GET['entities_id'],
			'fields_id'=>_get::int('fields_id'),
			'choices' => (isset($_POST['choices']) ? implode(',',$_POST['choices']) : ''),			
			'users_groups' => (isset($_POST['users_groups']) ? implode(',',$_POST['users_groups']) : ''),
			'access_schema' => (isset($_POST['access_schema']) ? implode(',',$_POST['access_schema']) : ''),
			'fields_view_only_access' => (isset($_POST['fields_view_only_access']) ? implode(',',$_POST['fields_view_only_access']) : ''),
			'comments_access_schema'=>str_replace('_',',',$_POST['comments_access_schema']),			
		);
	
		if(isset($_GET['id']))
		{
			db_perform('app_access_rules',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
		}
		else
		{
			db_perform('app_access_rules',$sql_data);			
		}
	
		redirect_to('access_rules/rules','entities_id=' . $_GET['entities_id']. '&fields_id=' . _get::int('fields_id'));
		break;
	
	case 'delete':
		
		if(isset($_GET['id']))
		{
			db_delete_row('app_access_rules',$_GET['id']);					
		}
		
		redirect_to('access_rules/rules','entities_id=' . $_GET['entities_id']. '&fields_id=' . _get::int('fields_id'));
		break;
		
}