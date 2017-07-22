<?php

	//check security settings if they are enabled 
	app_restricted_countries::verify();
	app_restricted_ip::verify();
	
	if(app_session_is_registered('app_logged_users_id'))
	{
		$app_module_action = 'logoff';
	}

  $app_layout = 'login_layout.php';
  
  switch($app_module_action)
  {
    case 'logoff':
        app_session_unregister('app_logged_users_id');
        app_session_unregister('app_current_version');
        
        setcookie('app_stay_logged','',time() - 3600,'/');
        setcookie('app_remember_user','',time() - 3600,'/'); 
        setcookie('app_remember_pass','',time() - 3600,'/');
        
        redirect_to('users/login');
      break;
    case 'login':                
        
    		//check reaptcha
        if(app_recaptcha::is_enabled())
        {
        	if(!app_recaptcha::verify())
        	{
        		$alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT,'error');
        		redirect_to('users/login');
        	}
        }
        
        users::login($_POST['username'],$_POST['password'],(isset($_POST['remember_me']) ? 1 :0));
        
      break;
  }