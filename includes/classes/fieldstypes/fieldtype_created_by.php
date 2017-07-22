<?php

class fieldtype_created_by
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name'=>TEXT_FIELDTYPE_CREATEDBY_TITLE);
  }
  
  function output($options)
  {
    global $app_users_cache;
    
    
    if($options['field']['entities_id']==1 and $options['value']==0)
    {
    	return TEXT_PUBLIC_REGISTRATION;
    }
    elseif(isset($options['is_export']) and isset($app_users_cache[$options['value']]))
    {
      return $app_users_cache[$options['value']]['name'];
    }
    elseif(isset($app_users_cache[$options['value']]))
    {
      return '<span ' . users::render_publi_profile($app_users_cache[$options['value']]). '>' .$app_users_cache[$options['value']]['name'] . '</span>';
    }
    else
    {
      return '';
    }
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = array();
    
    if(strlen($filters['filters_values'])>0)
    {
      $sql_query[] = "(e.created_by " . ($filters['filters_condition']=='include' ? 'in' : 'not in') . " (" . $filters['filters_values'] . "))";
    }
                  
    return $sql_query;
  }
}