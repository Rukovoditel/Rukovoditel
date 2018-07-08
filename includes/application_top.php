<?php
  define('PROJECT_VERSION','2.2.1');
  define('PROJECT_VERSION_DEV','');
  
//check if installed
  if(!is_file('config/database.php'))
  {
    header('Location: install/index.php');    
    exit();
  }  
  
// start the timer for the page parse time log
  define('PAGE_PARSE_START_TIME', microtime(true));

//set utf by default  
  header('Content-type: text/html; charset=utf-8');
  
//is AJAX request
  define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
      
//load core  
  require('includes/application_core.php');
  
//run check before start
  require('includes/check.php');  
    
//set off session warning
  if (function_exists('ini_set'))
  {
  	ini_set('session.bug_compat_warn', 0);
  	ini_set('session.bug_compat_42', 0);
  }  
          
// set the session name and save path
  app_session_name(SESSION_NAME);
  app_session_save_path(SESSION_WRITE_DIRECTORY);
  
// set the session cookie parameters
   if (function_exists('session_set_cookie_params')) {
    session_set_cookie_params(0, SESSION_COOKIE_PATH, SESSION_COOKIE_DOMAIN);
  } elseif (function_exists('ini_set')) {
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.cookie_path', SESSION_COOKIE_PATH);
    ini_set('session.cookie_domain', SESSION_COOKIE_DOMAIN);
  }

  @ini_set('session.use_only_cookies', (SESSION_FORCE_COOKIE_USE) ? 1 : 0);

// set the session ID if it exists
   if(isset($_GET[app_session_name()]) ) {
     app_session_id($_GET[app_session_name()]);
   }

// start the session
  $session_started = false;
  if (SESSION_FORCE_COOKIE_USE) 
  {
    setcookie('cookie_test', 'please_accept_for_session', time()+60*60*24*30, SESSION_COOKIE_PATH, SESSION_COOKIE_DOMAIN);

    if (isset($_COOKIE['cookie_test'])) {
      app_session_start();
      $session_started = true;
    }
  } 
  else 
  {
    app_session_start();
    $session_started = true;
  }

  if ( ($session_started == true) && function_exists('ini_get') && (ini_get('register_globals') == false) ) {
    extract($_SESSION, EXTR_OVERWRITE+EXTR_REFS);
  }
  

  if (!app_session_is_registered('uploadify_attachments')) 
  {
    $uploadify_attachments = array();
    app_session_register('uploadify_attachments');    
  }  
  
    
  if (!app_session_is_registered('uploadify_attachments_queue'))
  {
  	$uploadify_attachments_queue = array();
  	app_session_register('uploadify_attachments_queue');
  }
  
  
// create the alerts object
  if (!app_session_is_registered('alerts') || !is_object($alerts)) 
  {
    app_session_register('alerts');
    $alerts = new alerts;
  }    
  
  if (!app_session_is_registered('app_send_to')) 
  {
    app_session_register('app_send_to');
    $app_send_to = array();
  } 
    
  if (!app_session_is_registered('app_session_token'))
  {
  	$app_session_token = users::get_random_password(10);
  	app_session_register('app_session_token');  	
  }
  
  if (!app_session_is_registered('app_current_users_filter'))
  {
  	$app_current_users_filter = array();
  	app_session_register('app_current_users_filter');
  }
        

  if(!isset($_GET['module']))
  {
    redirect_to('dashboard/');
  }
  
//get module info  
  $module_array = explode('/',$_GET['module']);
  if(count($module_array)==2)
  {
    $app_plugin_path = '';
    $app_module = $module_array[0]; 
    $app_action = (strlen($module_array[1])>0 ? $module_array[1]:$module_array[0]);
    $app_module_path = $app_module . '/' . $app_action;     
  }
  elseif(count($module_array)==3)
  {
    $app_plugin_path = 'plugins/' . $module_array[0] . '/'; 
    $app_module = $module_array[1]; 
    $app_action = (strlen($module_array[2])>0 ? $module_array[2]:$module_array[1]);
    $app_module_path = $module_array[0] . '/' . $app_module . '/' . $app_action;
  }
  else
  {
    redirect_to('dashboard/');
  }
    
    
  
//set page title
  $app_title = (strlen(CFG_APP_SHORT_NAME)>0 ? CFG_APP_SHORT_NAME:CFG_APP_NAME);
  
//set module action
  $app_module_action = (isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : ''));
  
//set module redirect  
  $app_redirect_to = (isset($_GET['redirect_to']) ? $_GET['redirect_to'] : (isset($_POST['redirect_to']) ? $_POST['redirect_to'] : ''));    
  
//set app rapth  
  $app_path = (isset($_GET['path']) ? $_GET['path'] : (isset($_POST['path']) ? $_POST['path'] : ''));  
  
//set default layout
  $app_layout  = 'layout.php';   

    
//check if user logged
  $allowed_modules = array('users/login','users/restore_password','users/ldap_login','ext/calendar/icalexport','ext/public/form','ext/public/check','dashboard/vpic');
  
  if(CFG_USE_PUBLIC_REGISTRATION==1)
  {
  	$allowed_modules[] = 'users/registration';
  	$allowed_modules[] = 'users/validate_form';  	  	
  }
  
  if (!app_session_is_registered('app_logged_users_id') and !in_array($_GET['module'],$allowed_modules)) 
  { 
    //allows redirect user to current page after login if there is no any actions       
    if(!isset($_GET['action']) and !isset($_POST['action']) and !IS_AJAX)
    {
      setcookie('app_login_redirect_to', $_SERVER['QUERY_STRING'], time()+10*60,'/');
    }
            
    if(isset($_COOKIE["app_remember_me"]) and isset($_COOKIE["app_stay_logged"]))
    {      
      users::login(base64_decode($_COOKIE["app_remember_user"]),'',1,base64_decode($_COOKIE["app_remember_pass"]));
    }
    else
    {                          
      redirect_to('users/login');
    }
  }
  elseif(app_session_is_registered('app_logged_users_id'))
  {            
    $user_query = db_query("select * from app_entity_1 where id='" . db_input($app_logged_users_id) . "' and field_5=1");
    if($user = db_fetch_array($user_query))
    {
      if(strlen($user['field_10'])>0)
      {        
        $file = attachments::parse_filename($user['field_10']);
        $photo = $file['file_sha1'];
      }
      else
      {
        $photo = '';
      } 
      
      $app_user = array('id'=>$user['id'],
                        'group_id'=>(int)$user['field_6'],
                        'name'=> users::output_heading_from_item($user),
                        'username'=>$user['field_12'],
                        'email'=>$user['field_9'],
                        'photo'=>$photo,
                        'language'=>$user['field_13'],
                        'skin'=>$user['field_14'],
                        ); 
      
      //generat users access to entities schema
      if($app_user['group_id']>0)
      {	
      	$app_users_access = users::get_users_access_schema($app_user['group_id']);
      }
      else 
      {
      	$app_users_access = array();
      }
                             
    }
    else
    {
      app_session_unregister('app_logged_users_id');
      redirect_to('users/login');
    }
  }
  
  //get users configuration
  $app_users_cfg = new users_cfg();
  
  if (!app_session_is_registered('app_current_version')) 
  {
    $app_current_version = '';
    app_session_register('app_current_version');    
  } 
  
  if(CFG_DISABLE_CHECK_FOR_UPDATES==1)
  {
  	$app_current_version = '';
  }
  
  
  if (!app_session_is_registered('app_selected_items')) 
  {
    $app_selected_items = array();
    app_session_register('app_selected_items');    
  } 
    
      
  //include language file   
  if(isset($app_user))
  { 
    if(is_file($v = 'includes/languages/' . $app_user['language'] ))
    {
      require($v);
    }
    elseif(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE ))
    {
      require($v);
    }
  }
  elseif(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE ))
  {    
    require($v);
  } 
  
//set default language short code if not defined in language file
  if(!defined('APP_LANGUAGE_SHORT_CODE')) define('APP_LANGUAGE_SHORT_CODE','en');  
  
//set text direction if not defined in language file
  if(!defined('APP_LANGUAGE_TEXT_DIRECTION')) define('APP_LANGUAGE_TEXT_DIRECTION','ltr');
  

//check if maintenance mode enabled
  maintenance_mode::check();  
    
    
//set skin
  if(strlen(CFG_APP_SKIN)>0)
  {
    $app_skin = CFG_APP_SKIN . '/' . CFG_APP_SKIN .'.css';
  }
  elseif(isset($app_user))
  {
    if(strlen($app_user['skin'])>0)
    {
      $app_skin = $app_user['skin'] . '/' . $app_user['skin'] . '.css';
    }
    else
    {
      $app_skin = 'default/default.css';  
    }
  }
  elseif(isset($_COOKIE['user_skin']))
  {
    $app_skin = $_COOKIE['user_skin'] . '/' . $_COOKIE['user_skin'] . '.css';
  }
  else
  {
    $app_skin = 'default/default.css';
  }  
  
  if(!defined('IS_CRON'))
  {
  	$app_users_cache  = users::get_cache();
  }
  
              