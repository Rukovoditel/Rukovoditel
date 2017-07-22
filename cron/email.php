<?php

	chdir('../');

//load core
	require('includes/application_core.php');
	
//load app lagn
	if(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE))
	{
		require($v);
	}	
	
	$emails_limit = (int)CFG_MAXIMUM_NUMBER_EMAILS;
	
	$emails_query = db_query("select * from app_emails_on_schedule order by id limit " . ($emails_limit>0 ? $emails_limit : 1 ));
	while($emails = db_fetch_array($emails_query))
	{
		$options = array(
				'to'       => $emails['email_to'],
				'to_name'  => $emails['email_to_name'] ,
				'subject'  => $emails['email_subject'],
				'body'     => $emails['email_body'],
				'from'     => $emails['email_from'],
				'from_name'=> $emails['email_from_name'],				
				'send_directly' => true,
		);
		 
		users::send_email($options);
		
		db_delete_row('app_emails_on_schedule', $emails['id']);
	}