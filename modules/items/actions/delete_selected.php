<?php

if(!users::has_access('delete'))
{
	redirect_to('dashboard/access_forbidden');
}

switch($app_module_action)
{
	case 'delete_selected':
			if(entities::has_subentities($current_entity_id)==0 and $current_entity_id!=1)
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
			
			switch($app_redirect_to)
			{
				case 'parent_item_info_page':
					redirect_to('items/info','path=' . app_path_get_parent_path($app_path));
					break;
				case 'dashboard':
					redirect_to('dashboard/',substr($gotopage,1));
					break;
				default:
			
					if(strstr($app_redirect_to,'report_'))
					{
						redirect_to('reports/view','reports_id=' . str_replace('report_','',$app_redirect_to));
					}
					else
					{
						redirect_to('items/items','path=' . $app_path);
					}
			
					break;
			}
			
			
		break;
}