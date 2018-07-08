<?php


switch($app_module_action)
{
	case 'save':
				
		$sql_data = array(
			'entities_id'=>$_GET['entities_id'],
			'fields_id'=>$_POST['fields_id'],		
		);
	
		if(isset($_GET['id']))
		{
			$access_rules_fields_info = db_find('app_access_rules_fields',$_GET['id']);
			if($access_rules_fields_info['fields_id']!=$_POST['fields_id'])
			{
				db_delete_row('app_access_rules',$_GET['entities_id'],'entities_id');
			}
			
			db_perform('app_access_rules_fields',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
		}
		else
		{
			db_perform('app_access_rules_fields',$sql_data);			
		}
	
		redirect_to('access_rules/fields','entities_id=' . $_GET['entities_id']);
		break;
	
	case 'delete':
		
		if(isset($_GET['id']))
		{
			db_delete_row('app_access_rules_fields',$_GET['id']);
			db_delete_row('app_access_rules',$_GET['entities_id'],'entities_id');			
		}
		
		redirect_to('access_rules/fields','entities_id=' . $_GET['entities_id']);
		break;

}