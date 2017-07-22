<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_fields_choices',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_fields_choices');
}

$fields_info = db_find('app_fields',$_GET['fields_id']);