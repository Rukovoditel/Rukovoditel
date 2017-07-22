<?php

	//check security settings if they are enabled
	app_restricted_countries::verify();
	app_restricted_ip::verify();
	
	if(app_session_is_registered('app_logged_users_id'))
	{
		redirect_to('users/login','action=logoff');
	}
	
	$app_layout = 'login_layout.php';
	
	if(CFG_LDAP_USE!=1)
	{
	  $alerts->add(TEXT_LDAP_IS_NOT_ENABLED,'warning');
	  redirect_to('users/login');
	}

  switch($app_module_action)
  {
    case 'login':
       if(!$ldap_default_group_id = access_groups::get_ldap_default_group_id())
       {
         redirect_to('users/ldap_login'); 
       }
              
       if(app_recaptcha::is_enabled())
       {
       	if(!app_recaptcha::verify())
       	{
       		$alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT,'error');
       		redirect_to('users/ldap_login');
       	}
       }
                               
       $username = $_POST['username'];
       $password = $_POST['password'];
       
       $ldap = new ldap_login();
       
       $user_attr = $ldap->do_ldap_login($username, $password);
                
        if($user_attr['status']==true)
        {                                
          $user_email = $username . '@localhost.com';
          
          if(strlen($user_attr['email'])>0)
          {
            $user_email = $user_attr['email']; 
          }
          
          if(strlen($user_attr['name'])>0)
          {
            $first_name = $user_attr['name']; 
          }
          

          $check_query = db_query("select count(*) as total from app_entity_1 where field_12='" . db_input($username) . "' ");
          $check = db_fetch_array($check_query);
          
          if($check['total']==0)
          {
            $hasher = new PasswordHash(11, false);
                     
            $sql_data = array('password'    =>  $hasher->HashPassword($password),
                              'field_12'    =>  $username,
                              'field_5'     =>  1,
                              'field_6'     =>  $ldap_default_group_id,
                              'field_7'     =>  $first_name,
                              'field_9'     =>  $user_email,
                              'date_added'  =>  time());
            
            db_perform('app_entity_1',$sql_data);
            $users_id = db_insert_id();
                        
            
            if(!strstr($user_email,'localhost.com'))
            {
              $options = array('to' => $user_email,
                               'to_name' => $first_name,
                               'subject'=>(strlen(CFG_REGISTRATION_EMAIL_SUBJECT)>0 ? CFG_REGISTRATION_EMAIL_SUBJECT :TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT),
                               'body'=>CFG_REGISTRATION_EMAIL_BODY . '<p><b>' . TEXT_LOGIN_DETAILS . '</b></p><p>' . TEXT_USERNAME .': ' . $username . '<br>' . TEXT_PASSWORD . ': ' . $password . '</p><p><a href="' . url_for('users/login','',true) . '">' . url_for('users/login','',true). '</a></p>',
                               'from'=> 'noreply@' . $_SERVER['HTTP_HOST'],
                               'from_name'=>'noreply' );
                               
              users::send_email($options);
            }
          
            app_session_register('app_logged_users_id',$users_id);  
            
            redirect_to('users/account');        
          }
          else
          {
            users::login($_POST['username'],$_POST['password'],0);
          }
          
                                                        
        }
        else
        {
          $alerts->add($user_attr['msg'],'warning');
          redirect_to('users/ldap_login');
        }
      break;
  }