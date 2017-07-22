<?php

class fieldtype_parent_item_id
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name'=>TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE);
  }
  
  function output($options)
  {
  	if(isset($options['is_export']))
  	{
  		return str_replace('<br>',' - ',$options['path_info']['parent_name']);
  	}
  	else
  	{
    	return '<a href="' . url_for('items/info', 'path=' . $options['path_info']['parent_path']) . '">' . $options['path_info']['parent_name'] . '</a>';
  	}
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    if(strlen($filters['filters_values'])>0)
    {      
      $sql_query[] = " e.parent_item_id " . ($filters['filters_condition']=='include' ? 'in': 'not in') . " (" . $filters['filters_values'] . ") ";
    }
              
    return $sql_query;
  }
}