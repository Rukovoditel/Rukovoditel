<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('dashboard/');
}

$fields_in_listing = array();

if(strlen($reports_info['fields_in_listing'])>0)
{
  $fields_in_listing = explode(',',$reports_info['fields_in_listing']);
}
else
{
  $fields_query = db_query("select f.* from app_fields f where f.listing_status=1 order by f.listing_sort_order, f.name");
  while($fields = db_fetch_array($fields_query))
  {
    $fields_in_listing[] = $fields['id'];      
  }
}

switch($app_module_action)
{
  case 'set_listing_fields':                 
        
        if(strlen($_POST['fields_for_listing'])>0)
        {
          $fields_for_listing = str_replace('form_fields_','',$_POST['fields_for_listing']);
          db_query("update app_reports set fields_in_listing='" . db_input($fields_for_listing) . "' where id='" . db_input($_GET['reports_id']) . "'");
        }
        else
        {
          db_query("update app_reports set fields_in_listing='' where id='" . db_input($_GET['reports_id']) . "'");
        } 
      exit();
    break;
    
  case 'set_rows_per_page':
  		
  		db_query("update app_reports set rows_per_page='" . db_input($_POST['rows_per_page']) . "' where id='" . db_input($_GET['reports_id']) . "'");  	
  		exit();
  		
  	break;

}