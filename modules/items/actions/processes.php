<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('id'). "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) and is_active=1");
if(!$app_process_info = db_fetch_array($app_process_info_query))
{
	redirect_to('dashboard/page_not_found');
}

switch($app_module_action)
{
	case 'run':
		$app_send_to = array();
		
		$processes = new processes($current_entity_id);
		$processes->items_id = $current_item_id;
		$processes->run($app_process_info,(isset($_POST['reports_id']) ? _post::int('reports_id'):false));								
		break;
}