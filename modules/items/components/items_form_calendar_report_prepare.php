<?php

$calendar_reports_id = str_replace('calendarreport','',$app_redirect_to);
$calendar_reports_query = db_query("select * from app_ext_calendar where id='" . db_input($calendar_reports_id) . "'");
if($calendar_reports = db_fetch_array($calendar_reports_query))
{
  $start_date_timestamp = ($_GET['start'])/1000;
  $end_date_timestamp = ($_GET['end'])/1000;
  
  $offset=date('Z');
      
  if($offset<0)
  {    
    $start_date_timestamp+=abs($offset);
    $end_date_timestamp+=abs($offset);
  }
  else
  {
    $start_date_timestamp-=abs($offset);
    $end_date_timestamp-=abs($offset);
  }
            
  if($_GET['view_name']=='month')
  {
    $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
    $obj['field_' . $calendar_reports['end_date']] = strtotime('-1 day',$end_date_timestamp);
  }  
  else
  { 
    $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
    $obj['field_' . $calendar_reports['end_date']] = $end_date_timestamp;       
  }    
}