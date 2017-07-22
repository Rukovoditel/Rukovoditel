<?php

$obj = array();

if(isset($_GET['id']))
{
	$obj = db_find('app_comments_forms_tabs',$_GET['id']);
}
else
{
	$obj = db_show_columns('app_comments_forms_tabs');
}