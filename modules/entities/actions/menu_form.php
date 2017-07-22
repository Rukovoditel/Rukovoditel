<?php

$obj = array();

if(isset($_GET['id']))
{
	$obj = db_find('app_entities_menu',$_GET['id']);
}
else
{
	$obj = db_show_columns('app_entities_menu');
}