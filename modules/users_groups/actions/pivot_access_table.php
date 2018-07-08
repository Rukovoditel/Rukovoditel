<?php

$users_groups_info_query = db_query("select * from app_access_groups where id='" . _get::int('id') . "'");
if(!$users_groups_info = db_fetch_array($users_groups_info_query))
{
	redirect_to('users_groups/users_groups');
}

switch($app_module_action)
{
	case 'set_access':						
			if(isset($_POST['access'][$_GET['entities_id']]))
			{
				$access_schema = $_POST['access'][$_GET['entities_id']];
												
				if((in_array('view_assigned',$access_schema) or in_array('action_with_assigned',$access_schema)) and !in_array('view',$access_schema))
				{
					$access_schema[] = 'view';
				}
				
				//check with selected
				if(in_array('update_selected',$access_schema) and !in_array('update',$access_schema))
				{
					$access_schema[] = 'update';
				}
				
				if(in_array('delete_selected',$access_schema) and !in_array('delete',$access_schema))
				{
					$access_schema[] = 'delete';
				}
				
				if(in_array('export_selected',$access_schema) and !in_array('export',$access_schema))
				{
					$access_schema[] = 'export';
				}
								
				$sql_data = array('access_schema'=>implode(',',$access_schema));
				
				$acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $users_groups_info['id']. "'");
				if($acess_info = db_fetch_array($acess_info_query))
				{
					db_perform('app_entities_access',$sql_data,'update',"entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $users_groups_info['id']. "'");
				}
				else
				{
					$sql_data['entities_id'] = $_GET['entities_id'];
					$sql_data['access_groups_id'] = $users_groups_info['id'];
					db_perform('app_entities_access',$sql_data);
				}
			}
						
			if(isset($_POST['comments_access']))
			{				
				$access = $_POST['comments_access'][$_GET['entities_id']];
				
				$sql_data = array('access_schema'=>str_replace('_',',',$access));
		
				$acess_info_query = db_query("select access_schema from app_comments_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $users_groups_info['id']. "'");
				if($acess_info = db_fetch_array($acess_info_query))
				{
					db_perform('app_comments_access',$sql_data,'update',"entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $users_groups_info['id']. "'");
				}
				else
				{
					$sql_data['entities_id'] = $_GET['entities_id'];
					$sql_data['access_groups_id'] = $users_groups_info['id'];
					db_perform('app_comments_access',$sql_data);
				}										
			}
			
			exit();
		break;
}