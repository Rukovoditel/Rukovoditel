<?php

if($app_module_action=='change_skin')
{
  $skin = $_GET['set_skin'];
    
  if(is_file('css/skins/' . $skin . '/' . $skin . '.css'))
  {
    db_query("update app_entity_1 set field_14='" . db_input($skin). "' where id='" . $app_logged_users_id . "'");
    
    setcookie('user_skin', $skin, time()+ (365 * 24 * 3600), $_SERVER['HTTP_HOST'], '', (is_ssl() ? 1 : 0));
        
    redirect_to('dashboard/');
  }
}

