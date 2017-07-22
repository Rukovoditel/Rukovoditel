<?php

class users_cfg
{
  public $cfg;
  
  function __construct()
  {
    global $app_user;
    
    $cfg_query = db_query("select * from app_users_configuration where users_id='" . db_input($app_user['id']) . "'");   
    while ($v = db_fetch_array($cfg_query)) 
    {          
      $this->cfg[$v['configuration_name']] = $v['configuration_value']; 
    }
  }
  
  function get($key,$default = '')
  {        
    if(isset($this->cfg[$key]))
    {
      return $this->cfg[$key];
    }
    else
    {
      return $default;
    }
  }
  
  function set($key,$value)
  {
    global $app_user;
    
    if(strlen($key)>0)
    {
      $cfg_query = db_query("select * from app_users_configuration where users_id='" . db_input($app_user['id']) . "' and configuration_name='" . db_input($key). "'");   
      if($cfg = db_fetch_array($cfg_query))
      {
        db_query("update app_users_configuration set configuration_value='" . db_input($value) . "' where users_id='" . db_input($app_user['id']) . "' and configuration_name='" . db_input($key). "'");
      }
      else
      {
        db_perform('app_users_configuration',array('configuration_name'=>$key,'configuration_value'=>trim($value),'users_id'=>$app_user['id']));
      }
    } 
  }
  
  static function get_value_by_users_id($users_id, $key, $default = '')
  {
  	$cfg_query = db_query("select * from app_users_configuration where users_id='" . db_input($users_id) . "' and configuration_name='" . db_input($key) . "'");
  	if($cfg = db_fetch_array($cfg_query))
  	{
  		return $cfg['configuration_value'];
  	}
  	else 
  	{
  		return $default;
  	}
  }
}