<?php

$users_groups_info_query = db_query("select * from app_access_groups where id='" . _get::int('id') . "'");
if(!$users_groups_info = db_fetch_array($users_groups_info_query))
{
	redirect_to('users_groups/users_groups');
}

switch($app_module_action)
{
	case 'copy_selected':
		if(isset($_POST['copy_to_group_id']))
		{
			$copy_to_group_id = _post::int('copy_to_group_id');
			
			foreach(explode(',',$_POST['selected_items']) as $entities_id)
			{
				$access_schema = '';
				
				//check if access exit
				$acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . $entities_id . "' and access_groups_id='" . $users_groups_info['id'] . "'");
				if($acess_info = db_fetch_array($acess_info_query))
				{
					$access_schema = $acess_info['access_schema'];
				}
				
				$sql_data = array('access_schema'=>$access_schema);
					
				//update access
				$acess_info_copy_query = db_query("select access_schema from app_entities_access where entities_id='" . $entities_id . "' and access_groups_id='" . $copy_to_group_id . "'");
				if($acess_info_copy = db_fetch_array($acess_info_copy_query))
				{
					db_perform('app_entities_access',$sql_data,'update',"entities_id='" . $entities_id . "' and access_groups_id='" . $copy_to_group_id . "'");
				}
				else
				{
					//insert new access
					$sql_data['entities_id'] = $entities_id;
					$sql_data['access_groups_id'] = $copy_to_group_id;
					db_perform('app_entities_access',$sql_data);
				}
				
				//reset fields access
				db_query("delete from app_fields_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . db_input($copy_to_group_id). "'");
				
				//insert new fields access
				$sql_data = array();
				$fields_access_query = db_query("select * from app_fields_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . db_input($users_groups_info['id']). "'");
				while($fields_access = db_fetch_array($fields_access_query))
				{
					$sql_data[] = array(
							'access_schema'=>$fields_access['access_schema'],
							'entities_id' => $entities_id,
							'access_groups_id' => $copy_to_group_id,
							'fields_id' => $fields_access['fields_id'],
					);																	
				}
				
				if(count($sql_data))
				{
					db_batch_insert('app_fields_access',$sql_data);
				}
				
				//copy comments access
				
				$access_schema = '';
				
				//check if comments access exist
				$acess_info_query = db_query("select access_schema from app_comments_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . $users_groups_info['id']. "'");
				if($acess_info = db_fetch_array($acess_info_query))
				{
					$access_schema = $acess_info['access_schema'];
				}
				
				$sql_data = array('access_schema'=>str_replace('_',',',$access_schema));
				
				$acess_info_query = db_query("select access_schema from app_comments_access where entities_id='" . db_input($entities_id) . "' and access_groups_id='" . $copy_to_group_id. "'");
				if($acess_info = db_fetch_array($acess_info_query))
				{
					db_perform('app_comments_access',$sql_data,'update',"entities_id='" . db_input($entities_id) . "' and access_groups_id='" . $copy_to_group_id. "'");
				}
				else
				{
					$sql_data['entities_id'] = $entities_id;
					$sql_data['access_groups_id'] = $copy_to_group_id;
					db_perform('app_comments_access',$sql_data);
				}
			}
			
			$alerts->add(TEXT_ACCESS_UPDATED,'success');
			
		  redirect_to('users_groups/pivot_access_table','id=' . $copy_to_group_id);
		}
		else
		{
			redirect_to('users_groups/users_groups');
		}
		break;
}