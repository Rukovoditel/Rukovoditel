<?php
  $error_list = array();
  
//check php version
  if(!version_compare(phpversion(), '5.4', '>='))
  { 
    $error_list[] =  "Error: requires PHP >= 5.4, Current version is " .phpversion();    
  }
   
//check gd lib
  if (!extension_loaded('gd') or !function_exists('gd_info')) 
  {
    $error_list[] = "PHP GD library is NOT installed on your web server";
  }
  
//check mbstring  
  if (!extension_loaded('mbstring')) { 
  	$error_list[] = "PHP mbstring extension is NOT installed on your web server";
  }
  
//check folder
  $check_folders = array('config','backups','log','tmp','uploads','uploads/attachments','uploads/users');
  
  foreach($check_folders as $v)
  {
    if(is_dir($v))
    {
      if(!is_writable($v))
      {
        $error_list[] = sprintf('Error: folder "%s" is not writable!',dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $v);
      }
    }
    else
    {
      $error_list[] = sprintf('Error: folder "%s" does not exist',dirname($_SERVER['SCRIPT_FILENAME']) . '/' . $v);
    }
  }

//check user privileges  
  $user_privileges_list = array();
  $user_privileges_query = db_query("SHOW PRIVILEGES");
  while($user_privileges = db_fetch_array($user_privileges_query))
  {
    $user_privileges_list[] = $user_privileges['Privilege'];
  }
      
  $required_privileges = array('Select','Insert','Update','Delete','Create','Drop','Alter');
  
  foreach($required_privileges as $v)
  {
    if(!in_array($v,$user_privileges_list))
    {
      $error_list[] = 'Error: "' . $v . '" privilege for mysql user is required. Please update privileges for user "' . DB_SERVER_USERNAME . '"';
    }
  }
    
//dispaly errors if exist  
  if(count($error_list))
  {
    echo '<p>Please fix following errors.</p>';
    foreach($error_list as $v)
    {
      echo '<div>' . $v . '</div>';
    }
        
    exit();
  }