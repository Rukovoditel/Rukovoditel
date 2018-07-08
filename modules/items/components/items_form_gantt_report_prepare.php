<?php
$reports_query = db_query("select * from app_ext_ganttchart where id='" . str_replace('ganttreport','',$app_redirect_to) . "'");
if($reports = db_fetch_array($reports_query))
{
	$start_date_timestamp = ($_GET['start'])/1000;
	$end_date_timestamp = ($_GET['end'])/1000;
	
	$obj['field_' . $reports['start_date']] = $start_date_timestamp;
	$obj['field_' . $reports['end_date']] = strtotime('-1 day',$end_date_timestamp);	
}