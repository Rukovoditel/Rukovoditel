<?php

class entities_cfg
{
  public $cfg;
  
  public $entities_id;
  
  function __construct($entities_id)
  {
    
    $this->entities_id = $entities_id;
    
    $info_query = db_fetch_all('app_entities_configuration',"entities_id='" . db_input($this->entities_id). "'");
    while($info = db_fetch_array($info_query))
    {      
      $this->cfg[$info['configuration_name']] = $info['configuration_value'];
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
      $cfq_query = db_query("select * from app_entities_configuration where configuration_name='" . db_input($key) . "' and entities_id='" . db_input($this->entities_id) . "'");
      if(!$cfq = db_fetch_array($cfq_query))
      {
        db_perform('app_entities_configuration',array('configuration_value'=>$value,'configuration_name'=>$key,'entities_id'=>$this->entities_id));
      }
      else
      {
        db_perform('app_entities_configuration',array('configuration_value'=>$value),'update',"configuration_name='" . db_input($key) . "' and entities_id='" . db_input($this->entities_id) . "'");
      }
    } 
  }
}
