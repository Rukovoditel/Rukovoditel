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
		
//send notification		
	app_chat::send_notification();
