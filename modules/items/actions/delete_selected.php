<?php

if(!users::has_access('delete'))
{
	redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{
	case 'delete_selected':
			if(entities::has_subentities($current_entity_id)==0)
			{
				if(!isset($app_selected_items[$_GET['reports_id']])) $app_selected_items[$_GET['reports_id']] = array();
				
				if(count($app_selected_items[$_GET['reports_id']])>0)
				{
					foreach($app_selected_items[$_GET['reports_id']] as $items_id)
					{
						items::delete($current_entity_id, $items_id);
					}
				}
			}
			
			redirect_to('items/items','path=' . $app_path);
		break;
}