<?php

class fieldtype_user_accessgroups
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE,'title' => TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {     
  	global $app_user, $app_module_path;
  	
    if(($default_group_id = access_groups::get_default_group_id())>0 and strlen($obj['field_' . $field['id']])==0)
    {
      $value = $default_group_id;
    }
    else
    {
      $value = $obj['field_' . $field['id']];
    }
    
    if($app_module_path=='users/registration')
    {
    	$choices = array();
    	$groups_query = db_fetch_all('app_access_groups','id in (' . CFG_PUBLIC_REGISTRATION_USER_GROUP . ')','sort_order, name');
    	while($v = db_fetch_array($groups_query))
    	{
    		$choices[$v['id']] = $v['name'];
    	}
    }
    else
    {
    	$include_administrator = ($app_user['group_id']>0 ? false : true);
    	$choices = access_groups::get_choices($include_administrator);
    }
    
    return select_tag('fields[' . $field['id'] . ']',$choices,$value,array('class'=>'form-control input-medium field_' . $field['id']));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    return access_groups::get_name_by_id($options['value']);
  }
  
  function reports_query($options)
  {
  	$filters = $options['filters'];
  	$sql_query = $options['sql_query'];
  
  	$sql = array();
  
  	if(strlen($filters['filters_values'])>0)
  	{
  		$sql_query[] = "(e.field_6 " . ($filters['filters_condition']=='include' ? 'in' : 'not in') . " (" . $filters['filters_values'] . "))";
  	}
  
  	return $sql_query;
  }  
}