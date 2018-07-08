<?php

$obj = array();

if(isset($_GET['id']))
{
	$obj = db_find('app_users_alerts',$_GET['id']);
}
else
{
	$obj = db_show_columns('app_users_alerts');
}
