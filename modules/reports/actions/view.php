<?php

//check if report exist  
$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']). "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{  
  $alerts->add(TEXT_REPORT_NOT_FOUND,'error');
  redirect_to('reports/');
}

//check report access
if($reports_info['reports_type']=='common')
{
  //check access for common report
  $check_query = db_query("select r.* from app_reports r, app_entities e, app_entities_access ea  where r.id = '" . $reports_info['id'] . "' and  r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and find_in_set(" . $app_user['group_id'] . ",r.users_groups) and r.reports_type = 'common' order by r.dashboard_sort_order, r.name");
  if(!$check = db_fetch_array($check_query))
  {
    redirect_to('dashboard/access_forbidden');
  }
}
elseif($app_logged_users_id!=$reports_info['created_by'])
{  
  redirect_to('dashboard/access_forbidden');
}

//get report entity info
  $entity_info = db_find('app_entities',$reports_info['entities_id']);
  $entity_cfg = new entities_cfg($reports_info['entities_id']);
     
//get page title
if($reports_info['reports_type']=='entity_menu')
{	
  $page_title = (strlen($entity_cfg->get('menu_title'))>0 ? $entity_cfg->get('menu_title') : $entity_info['name']);
}
else
{
  $page_title = $reports_info['name'];
}

$app_title = app_set_title($page_title);