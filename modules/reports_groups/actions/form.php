<?php

$obj = array();

if(isset($_GET['id']))
{
	$obj = db_find('app_reports_groups',$_GET['id']);
}
else
{
	$obj = db_show_columns('app_reports_groups');
}