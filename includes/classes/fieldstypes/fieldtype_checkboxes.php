<?php

class fieldtype_checkboxes
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_CHECKBOXES_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    //cfg global list if exist
    if(count($choices = global_lists::get_lists_choices())>0)
    {              
      $cfg[] = array('title'=>TEXT_USE_GLOBAL_LIST, 
                     'name'=>'use_global_list',
                     'type'=>'dropdown',
                     'choices'=>$choices,
                     'tooltip'=>TEXT_USE_GLOBAL_LIST_TOOLTIP,
                     'params'=>array('class'=>'form-control input-medium'));
    }     
            
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {                
    $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
                  
    $cfg = new fields_types_cfg($field['configuration']);
                  
    //use global lists if exsit    
    if($cfg->get('use_global_list')>0)
    {
      $choices = global_lists::get_choices($cfg->get('use_global_list'),false);
      $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
    }
    else
    {                    
      $choices = fields_choices::get_choices($field['id'],false);
      $default_id = fields_choices::get_default_id($field['id']);
    }
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : $default_id); 
    
    return '
    		<div class="' . (count($choices)==1 ? 'checkbox-list-singe':'') . ($field['is_required']==1 ? ' required':'') . '">' . 
    			select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . 
   		 '</div>';
  }
  
  function process($options)
  {            
    return (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
  }
  
  function output($options)
  {    
    $is_export = isset($options['is_export']);  
  
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    //render global list value
    if($cfg->get('use_global_list')>0)
    {
      return global_lists::render_value($options['value'],$is_export);
    }
    else
    {
      return fields_choices::render_value($options['value'],$is_export);
    }    
  }  
  
  function reports_query($options)
  {  	  	
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
  	if(strlen($filters['filters_values'])>0)
    {  
      $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id=e.id and cv.fields_id='" . db_input($options['filters']['fields_id'])  . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition']=='include' ? '>0': '=0');
    }
                     
    return $sql_query;
  }
}