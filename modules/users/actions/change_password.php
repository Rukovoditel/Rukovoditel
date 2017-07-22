<?php
	if(in_array($app_user['group_id'], explode(',',CFG_APP_DISABLE_CHANGE_PWD)) and strlen(CFG_APP_DISABLE_CHANGE_PWD)>0)
	{
		redirect_to('users/account');
	}	

  switch($app_module_action)
  {
    case 'change':
        
        $password = $_POST['password_new'];
        $password_confirm = $_POST['password_confirmation'];
        
        $error = false;
        
        if($password!=$password_confirm)
        {
          $error = true;
          $alerts->add(TEXT_ERROR_PASSOWRD_CONFIRMATION,'error');
        }
        
         if(strlen($password)<CFG_PASSWORD_MIN_LENGTH)
        {
          $error = true;
          $alerts->add(TEXT_ERROR_PASSOWRD_LENGTH,'error');
        }
        
        
        
        if(!$error)
        {
          $hasher = new PasswordHash(11, false);
          
          $sql_data = array();  
          $sql_data['password']=$hasher->HashPassword($password);
          
          db_perform('app_entity_1',$sql_data,'update',"id='" . db_input($app_logged_users_id). "'");
          
          $alerts->add(TEXT_PASSWORD_UPDATED,'success');       
        }
        
        
        redirect_to('users/change_password');
      break;
  }