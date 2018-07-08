<?php

$app_process_info_query = db_query("select * from app_ext_processes where id='" . _get::int('id'). "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) and is_active=1");
if(!$app_process_info = db_fetch_array($app_process_info_query))
{
	redirect_to('dashboard/page_not_found');
}

switch($app_module_action)
{
	case 'confirmation':
		
		$module_query = db_query("select * from app_ext_modules where id='" .  _get::int('module_id') . "' and is_active=1");
		if($module = db_fetch_array($module_query))
		{
			$modules = new modules('payment');
			
			$payment_module = new $module['module'];
			
			echo $payment_module->confirmation($module['id'],_get::int('id'));
		}
		
		exit();
		break;
}