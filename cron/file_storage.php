<?php

chdir('../');

define('IS_CRON',true);

//load core
require('includes/application_core.php');

//load ext core if installed
if(is_file($v = 'plugins/ext/application_core.php'))
{
	require($v);
}

//load app lang
if(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE))
{
	require($v);
}

//load ext lang
if(is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE))
{
	require($v);
}

$app_user = array('id'=>0,'group_id'=>0,'language'=>CFG_APP_LANGUAGE);

$modules = new modules('file_storage');

file_storage::upload_from_queue();