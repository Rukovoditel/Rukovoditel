<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input((isset($_GET['parent_reports_id']) ? $_GET['parent_reports_id']:$_GET['reports_id'])). "' and created_by='" . db_input($app_logged_users_id) . "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  echo TEXT_REPORT_NOT_FOUND;
  exit();
}

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_reports_filters',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_reports_filters');
  $obj['is_active'] = 1;
}