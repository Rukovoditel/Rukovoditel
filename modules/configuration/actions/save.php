<?php

if(isset($_POST['delete_logo']))
{
  if(is_file(DIR_FS_UPLOADS . CFG_APP_LOGO))
  {
    unlink(DIR_FS_UPLOADS . CFG_APP_LOGO);
  }
  
  $_POST['CFG']['APP_LOGO'] = '';
  
}

if(isset($_POST['delete_login_page_background']))
{
  if(is_file(DIR_FS_UPLOADS . CFG_APP_LOGIN_PAGE_BACKGROUND))
  {
    unlink(DIR_FS_UPLOADS . CFG_APP_LOGIN_PAGE_BACKGROUND);
  }
  
  $_POST['CFG']['APP_LOGIN_PAGE_BACKGROUND'] = '';
}

if(isset($_POST['delete_login_maintenance_background']))
{
	if(is_file(DIR_FS_UPLOADS . CFG_APP_LOGIN_MAINTENANCE_BACKGROUND))
	{
		unlink(DIR_FS_UPLOADS . CFG_APP_LOGIN_MAINTENANCE_BACKGROUND);
	}

	$_POST['CFG']['APP_LOGIN_MAINTENANCE_BACKGROUND'] = '';
}


if(isset($_POST['CFG']))
{
  foreach($_POST['CFG'] as $k=>$v)
  {
    $k = 'CFG_' . $k;
        
    if($k=='CFG_APP_LOGO')
    {                                                                            
      if(strlen($_FILES['APP_LOGO']['name'])>0)
      {                        
        if(is_image($_FILES['APP_LOGO']['tmp_name']))
        {
          $pathinfo = pathinfo($_FILES['APP_LOGO']['name']);
          $filename = 'app_logo_' . time() . '.' . $pathinfo['extension'];
          
          move_uploaded_file($_FILES['APP_LOGO']['tmp_name'], DIR_FS_UPLOADS  . $filename);
          $v = $filename;
        }
      }      
    }
    
    
    if($k=='CFG_APP_LOGIN_PAGE_BACKGROUND')
    {                                                                            
      if(strlen($_FILES['APP_LOGIN_PAGE_BACKGROUND']['name'])>0)
      {                        
        if(is_image($_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name']))
        {
          $pathinfo = pathinfo($_FILES['APP_LOGIN_PAGE_BACKGROUND']['name']);
          $filename = 'app_bg_' . time() . '.' . $pathinfo['extension'];
          
          move_uploaded_file($_FILES['APP_LOGIN_PAGE_BACKGROUND']['tmp_name'], DIR_FS_UPLOADS  . $filename);
          $v = $filename;
        }
      }      
    }
    
    if($k=='CFG_APP_LOGIN_MAINTENANCE_BACKGROUND')
    {
    	if(strlen($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['name'])>0)
    	{
    		if(is_image($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['tmp_name']))
    		{
    			$pathinfo = pathinfo($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['name']);
    			$filename = 'app_bg_' . time() . '.' . $pathinfo['extension'];
    
    			move_uploaded_file($_FILES['APP_LOGIN_MAINTENANCE_BACKGROUND']['tmp_name'], DIR_FS_UPLOADS  . $filename);
    			$v = $filename;
    		}
    	}
    }
    
    //handle arrays
    if(is_array($v))
    {
    	$v = implode(',',$v);
    }
    
    switch($k)
    {	
    	case 'CFG_APP_NUMBER_FORMAT':
    		  $value = $v;
    		break;
    	default:
    			$value = trim($v);
    		break;
    }
            
    $cfq_query = db_query("select * from app_configuration where configuration_name='" . $k . "'");
    if(!$cfq = db_fetch_array($cfq_query))
    {
      db_perform('app_configuration',array('configuration_value'=>$value,'configuration_name'=>$k));
    }
    else
    {
      db_perform('app_configuration',array('configuration_value'=>$value),'update',"configuration_name='" . $k . "'");
    }
  }
  
  $alerts->add(TEXT_CONFIGURATION_UPDATED,'success');
      
  redirect_to($app_redirect_to);
  
}