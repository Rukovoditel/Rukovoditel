<?php

	chdir('../');

//load core
	require('includes/application_core.php');
	
	$backup = new backup();
	
	$backup->create();