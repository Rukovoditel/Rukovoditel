<?php

class maintenance_mode
{
	static function login_message()
	{
		$html = '';
		
		if(CFG_MAINTENANCE_MODE==1)
		{
			$html = '
					<div class="alert alert-block alert-warning fade in">
						<h4>' . (strlen(CFG_MAINTENANCE_MESSAGE_HEADING)>0 ? CFG_MAINTENANCE_MESSAGE_HEADING : TEXT_MAINTENANCE_MESSAGE_HEADING) . '</h4>
						<p>' . (strlen(CFG_MAINTENANCE_MESSAGE_CONTENT)>0 ? CFG_MAINTENANCE_MESSAGE_CONTENT : TEXT_MAINTENANCE_MESSAGE_CONTENT). '</p>
					</div>
					';
		}
		
		return $html;
	}
	
	static function header_message()
	{
		$html = '';
		
		if(CFG_MAINTENANCE_MODE==1)
		{
			$html = '
					<span class="label label-warning">' . TEXT_MAINTENANCE_MODE . '</span>
					';
		}
		
		return $html;
	}
	
	static function check()
	{
		global $app_user, $app_module_path, $alerts;
					
		if(app_session_is_registered('app_logged_users_id') and $app_module_path!='users/login')
		{
			if(CFG_MAINTENANCE_MODE==1 and $app_user['group_id']!=0)
			{
				$alerts->add(TEXT_ACCESS_FORBIDDEN,'error');	
				redirect_to('users/login&action=logoff');
			}
		}
	}
}