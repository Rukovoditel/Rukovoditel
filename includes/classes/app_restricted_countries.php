<?php

class app_restricted_countries
{
	static function is_enabled()
	{
		if(CFG_RESTRICTED_COUNTRIES_ENABLE==true and strlen(CFG_ALLOWED_COUNTRIES_LIST))
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
			include("includes/libs/maxmind/src/geoip.inc");
			
			$gi = geoip_open("includes/libs/maxmind/GeoIP.dat", GEOIP_STANDARD);
			
			$country_code = geoip_country_code_by_addr($gi, $_SERVER['REMOTE_ADDR']);
			
			geoip_close($gi);
			
			if(!in_array($country_code,array_map('trim',explode(',',CFG_ALLOWED_COUNTRIES_LIST))))
			{
				echo TEXT_ACCESS_FORBIDDEN;				
				exit();
			}
		}
	}
}