<?php

class app_restricted_ip
{
	static function is_enabled()
	{
		if(CFG_RESTRICTED_BY_IP_ENABLE==true and strlen(CFG_ALLOWED_IP_LIST))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	static function verify()
	{		
		if(self::is_enabled())
		{
			if(!in_array($_SERVER['REMOTE_ADDR'],array_map('trim',explode(',',CFG_ALLOWED_IP_LIST))))
			{
				echo TEXT_ACCESS_FORBIDDEN;
				exit();
			}
		}
	}
}