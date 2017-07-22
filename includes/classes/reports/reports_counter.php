<?php

class reports_counter
{
	function __construct()
	{
		
	}
	
	function render()
	{
		$html = '';
	
		$reports_query = db_query($this->reports_query());
		while($reports = db_fetch_array($reports_query))
		{
			$html .= '
				<div class="col-md-2 col-sm-4">
					<div class="stats-overview stat-block" onClick="location.href=\'' . url_for('reports/view','reports_id=' . $reports['id']) . '\'">
						<div class="display stat ok huge">							
							<div class="percent">
								' . $this->get_items_count($reports) . '
							</div>
						</div>
						<div class="details">
							<div class="title">
								 ' . $reports['name'] . '
							</div>
							<div class="numbers">
								 
							</div>
						</div>
					</div>
				</div>
					
      ';
		}
		
		if(strlen($html))
		{
			$html = '
					<h3 class="page-title">' .  TEXT_STATISTICS . '</h3>
					<div class="row stats-overview-cont">' . $html . '</div>';
		}
	
		return $html;
	}
	
	function get_items_count($report_info)
	{
		global $sql_query_having;
		
		$listing_sql_query_select = '';
		$listing_sql_query = '';
		$listing_sql_query_join = '';
		$listing_sql_query_having = '';
		$sql_query_having = array();
		
		//prepare formulas query
		$listing_sql_query_select = fieldtype_formula::prepare_query_select($report_info['entities_id'], $listing_sql_query_select);
	
		//prepare listing query
		$listing_sql_query = reports::add_filters_query($report_info['id'],$listing_sql_query);
		
		//prepare having query for formula fields
		if(isset($sql_query_having[$report_info['entities_id']]))
		{
			$listing_sql_query_having  = reports::prepare_filters_having_query($sql_query_having[$report_info['entities_id']]);
		}
	
		//check view assigned only access
		$listing_sql_query = items::add_access_query($report_info['entities_id'],$listing_sql_query);
		
		//add having query
		$listing_sql_query .= $listing_sql_query_having;
			
		$listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $report_info['entities_id'] . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query . " ";
		$items_query = db_query($listing_sql);
		$items_count = db_num_rows($items_query);
	
		return $items_count;
	}
	
	//build counter reports query with common reports
	function reports_query()
	{
		global $app_logged_users_id, $app_user, $app_users_cfg;
	
		$where_sql = '';
	
		//check hidden common reports
		if(strlen($app_users_cfg->get('hidden_common_reports'))>0)
		{
			$where_sql = " and r.id not in (" . $app_users_cfg->get('hidden_common_reports') . ")";
		}
		
		//get common reports list
		$common_reports_list = array();
		$reports_query = db_query("select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and find_in_set(" . $app_user['group_id'] . ",r.users_groups) and r.in_dashboard_counter=1 and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name");
		while($reports = db_fetch_array($reports_query))
		{
			$common_reports_list[] = $reports['id'];
		}
	
		//create reports query inclue common reports
		$reports_query = "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and ((r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and  r.in_dashboard_counter=1)  " . (count($common_reports_list)>0 ? " or r.id in(" . implode(',',$common_reports_list). ")" : "") . ") order by r.dashboard_counter_sort_order, r.dashboard_sort_order, r.name";
	
		return $reports_query;
	}
}