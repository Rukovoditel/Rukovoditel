<?php

if(strstr($app_redirect_to,'ganttreport'))
{
	$check_query = db_query("select id from app_entities where parent_id='" . $current_entity_id . "'");
	$check = db_fetch_array($check_query);
	
	if(users::has_access('delete') and !$check)
	{
		$extra_button = '<button id="gantt_delete_item_btn" type="button" class="btn btn-default" onclick="gantt_delete()"><i class="fa fa-trash-o"></i></button>';
	}
}