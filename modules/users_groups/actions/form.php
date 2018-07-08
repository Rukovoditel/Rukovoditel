<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_access_groups',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_access_groups');
}