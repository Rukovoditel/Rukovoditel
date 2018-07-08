<?php

require(component_path('dashboard/style_custumizer'));

app_reset_selected_items();

$has_reports_on_dashboard = false;

$reports_counter = new reports_counter;
$html = $reports_counter->render();
if(strlen($html))
{
	echo $html;
	
	$has_reports_on_dashboard = true;
}	

//include sections
require(component_path('dashboard/sections'));

$reports_query = db_query("select * from app_reports where created_by='" . db_input($app_logged_users_id) . "' and in_dashboard=1 and reports_type in ('standard') order by dashboard_sort_order, name");
while($reports = db_fetch_array($reports_query))
{
	$check_query = db_query("select id from app_reports_sections where (report_left='standard{$reports['id']}' or report_right='standard{$reports['id']}') and reports_groups_id=0 and created_by='" . $app_user['id'] . "'");
	if($check = db_fetch_array($check_query))
	{
		echo '
			<div class="row">
        <div class="col-md-12"><h3 class="page-title"><a href="' . url_for('reports/view','reports_id=' . $reports['id']) . '">' . $reports['name'] . '</a></h3></div>
      </div>
			<div class="alert alert-warning">' . TEXT_REPORT_ALREADY_ASSIGNED . '</div>';
	}
	else
	{
		require(component_path('dashboard/render_standard_reports'));
	}
 	     
  $has_reports_on_dashboard = true;  
}

//include common reports
require(component_path('dashboard/common_reports'));

//display default dashboard msg
if(!$has_reports_on_dashboard and $app_user['group_id']==0)
{
	echo TEXT_DASHBOARD_DEFAULT_ADMIN_MSG;
}
elseif(!$has_reports_on_dashboard) 
{
	echo TEXT_DASHBOARD_DEFAULT_MSG;
}
	
require(component_path('items/load_items_listing.js'));

