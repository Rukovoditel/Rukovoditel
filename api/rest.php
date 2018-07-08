<?php

chdir('../');

//load core
require('includes/application_core.php');

//include ext plugins
require('plugins/ext/application_core.php');

//load app lagn
if(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE))
{
	require($v);
}

if(is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE))
{
	require($v);
}

//check if API enabled
if(CFG_USE_API==1)
{
		
	$api_key = api::_post('key');
	
	if(strlen(CFG_API_KEY) and CFG_API_KEY==$api_key)
	{
		$api = new api();
		$api->request();
	}
	else
	{
		api::response_error('API Key mismatch');
	}
}
else
{
	api::response_error('API is not enabled');
}