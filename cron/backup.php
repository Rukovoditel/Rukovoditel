<?php

	chdir('../');

	define('IS_CRON',true);
	
//load core
	require('includes/application_core.php');
	
	$backup = new backup();
	
	$backup->create();