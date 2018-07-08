<?php

$reports_groups_id  = (isset($_GET['id']) ? _get::int('id') :0);
$sections_query = db_query("select * from app_reports_sections where reports_groups_id='" . db_input($reports_groups_id) . "' and created_by='" . $app_user['id']. "' order by sort_order");
while($sections = db_fetch_array($sections_query))
{
		
	echo '
			<div class="row">
				<div class="col-md-6">	
			';
		
	$section_report = $sections['report_left'];	
	require(component_path('dashboard/sections_reports'));
	
	echo '
			</div>
			<div class="col-md-6">';
	
	$section_report = $sections['report_right'];
	require(component_path('dashboard/sections_reports'));
	
	echo '
			</div>
			</div>';
	$has_reports_on_dashboard = true;
}