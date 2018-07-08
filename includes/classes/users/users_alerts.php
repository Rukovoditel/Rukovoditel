<?php

class users_alerts
{
	static function get_types_choices()
	{
		$choices = array(				
				'warning' => TEXT_ALERT_WARNING,
				'danger' => TEXT_ALERT_DANGER,
				'success' => TEXT_ALERT_SUCCESS,
				'info' => TEXT_ALERT_INFO,
		);
		
		return $choices;
	}
	
	static function get_type_by_name($name)
	{
		$types = self::get_types_choices();
		
		return (isset($types[$name]) ? $types[$name] : '');
	}
	
	static function get_location_choices()
	{
		$choices = array(
				'all' => TEXT_LOCATION_ON_ALL_PAGES,
				'dashboard' => TEXT_LOCATION_ON_DASHBOARD,				
		);
	
		return $choices;
	}
	
	static function output()
	{
		global $app_module_path, $app_user;
		
		$where_sql = '';
		
		$where_sql .= " and ((FROM_UNIXTIME(ua.start_date,'%Y-%m-%d')<=date_format(now(),'%Y-%m-%d') or ua.start_date=0) and (FROM_UNIXTIME(ua.end_date,'%Y-%m-%d')>=date_format(now(),'%Y-%m-%d') or ua.end_date=0))";
		
		if($app_module_path=='dashboard/dashboard')
		{
			$where_sql .= " and (ua.location='dashboard' or ua.location='all')";
		}
		else
		{
			$where_sql .= " and ua.location!='dashboard'";
		}
		
		$where_sql .= " and ua.id not in (select uav.alerts_id from app_users_alerts_viewed uav where uav.users_id='" . $app_user['id'] . "')";
				
		$html = '';
		
		$alerts_query = db_query("select * from app_users_alerts ua where ua.is_active=1 and ((length(ua.users_groups)=0 and length(ua.assigned_to)=0) or (find_in_set(" . $app_user['group_id'] . ",ua.users_groups) or find_in_set(" . $app_user['id'] . ",ua.assigned_to)) ) {$where_sql} order by ua.id desc");
		while($alerts = db_fetch_array($alerts_query))
		{
			$html .= '
				<div class="alert alert-' . $alerts['type'] . '"><button type="button" class="close users-alers-close" data-id="' . $alerts['id'] . '" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4>' . $alerts['title'] . '</h4>' . $alerts['description'] . '</div>		
			';
		}
		
		$html .= '
			<script>
				$(function(){
					$(".users-alers-close").click(function(){
						id = $(this).attr("data-id")
						$.ajax({method:"POST",url:"' . url_for('dashboard/','action=set_users_alers_viewed'). '",data:{id:id}})
					})
				})	
			</script>		
		';
		
		return $html;
	}
}