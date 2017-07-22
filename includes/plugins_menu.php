<?php

//to include menus from plugins
  $app_plugin_menu = array();
  
//include menu if not ajax query  
  if(defined('AVAILABLE_PLUGINS') and isset($app_user) and !IS_AJAX)
  {
    foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
    {                        
      if(is_file('plugins/' . $plugin .'/menu.php'))
      {
        require('plugins/' . $plugin .'/menu.php');
      }
    }
  } 