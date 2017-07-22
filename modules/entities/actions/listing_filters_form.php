<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "' and reports_type='default'");
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
}