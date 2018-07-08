<?php
$reports_id = str_replace(entities_menu::get_reports_types(),'',$section_report);

switch(true)
{
	case strstr($section_report,'standard'):	
		$reports_query = db_query("select * from app_reports where created_by='" . db_input($app_logged_users_id) . "' and id='" . db_input($reports_id) . "'");
		if($reports = db_fetch_array($reports_query))
		{	
			require(component_path('dashboard/render_standard_reports'));
		}
		break;
	case strstr($section_report,'graphicreport'):
		$reports_query = db_query("select * from app_ext_graphicreport where id='" . $reports_id. "'");
		if($reports = db_fetch_array($reports_query))
		{
			if(in_array($app_user['group_id'],explode(',',$reports['allowed_groups'])) or $app_user['group_id']==0)
			{
				echo '<h3 class="page-title"><a href="' . url_for('ext/graphicreport/view','id=' . $reports['id']) . '">' . $reports['name'] . '</a></h3>';
				
				require(component_path('ext/graphicreport/view'));
			}
		}
		break;
	case strstr($section_report,'funnelchart'):
		$reports_query = db_query("select * from app_ext_funnelchart where id='" . $reports_id. "'");
		while($reports = db_fetch_array($reports_query))
		{
			if(in_array($app_user['group_id'],explode(',',$reports['users_groups'])) or $app_user['group_id']==0)
			{
				echo '<h3 class="page-title"><a href="' . url_for('ext/funnelchart/view','id=' . $reports['id']) . '">' . $reports['name'] . '</a></h3>';
				require(component_path('ext/funnelchart/view'));
			}
		}
		break;
	case strstr($section_report,'calendar_personal'):
		  echo '<h3 class="page-title"><a href="' . url_for('ext/calendar/personal') . '">' . TEXT_EXT_MY_СALENDAR . '</a></h3>';
			require(component_path('ext/calendar/personal'));
		break;
	case strstr($section_report,'calendar_public'):
			echo '<h3 class="page-title"><a href="' . url_for('ext/calendar/public') . '">' . TEXT_EXT_СALENDAR . '</a></h3>';
			require(component_path('ext/calendar/public'));
		break;
	case strstr($section_report,'calendarreport'):
		if($app_user['group_id']>0)
		{
			$reports_query = db_query("select c.* from app_ext_calendar c, app_entities e, app_ext_calendar_access ca where c.id='" . $reports_id. "' and e.id=c.entities_id and c.id=ca.calendar_id and ca.access_groups_id='" . db_input($app_user['group_id']) . "' order by c.name");
		}
		else
		{
			$reports_query = db_query("select c.* from app_ext_calendar c, app_entities e where c.id='" . $reports_id. "' and  e.id=c.entities_id order by c.name");
		}
		if($reports = db_fetch_array($reports_query))
		{
			echo '<h3 class="page-title"><a href="' . url_for('ext/calendar/report','id=' . $reports['id']) . '">' . $reports['name'] . '</a></h3>';
			require(component_path('ext/calendar/report'));
		}
		break;
}