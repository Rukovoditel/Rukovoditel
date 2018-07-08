<?php
  //check access
  if($app_user['group_id']>0)
  {
    redirect_to('dashboard/access_forbidden');
  }
  
  $app_title = app_set_title(TEXT_FORMS_FIELDS_DISPLAY_RULES);
  
  //check if entity exist
  if(isset($_GET['entities_id']))
  {
  	$check_query = db_query("select * from app_entities where id='" . db_input($_GET['entities_id']) . "'");
  	if(!$check = db_fetch_array($check_query))
  	{
  		redirect_to('entities/entities');
  	}
  }