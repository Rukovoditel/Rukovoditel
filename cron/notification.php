<?php

	chdir('../');

//load core
	require('includes/application_core.php');

//load ext core if installed
	if(is_file($v = 'plugins/ext/application_core.php'))
	{
		require($v);
	}	
	
//load app lagn	
	if(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE))
	{
		require($v);
	}
			
	$app_user = array();
			
	$reports_notification = new reports_notification();
	
	$reports_notification->send();