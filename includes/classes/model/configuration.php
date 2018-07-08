<?php

class configuration
{
	static function set($k,$value)
	{
		$cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
		if(!$cfq = db_fetch_array($cfq_query))
		{
			db_perform('app_configuration',array('configuration_value'=>$value,'configuration_name'=>$k));
		}
		else
		{
			db_perform('app_configuration',array('configuration_value'=>$value),'update',"configuration_name='" . $k . "'");
		}
	}	
}