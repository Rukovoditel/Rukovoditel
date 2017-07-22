<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_global_lists',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_global_lists');
}