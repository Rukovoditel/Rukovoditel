<?php

class fieldtype_dropdown_multiple
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();

    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium')); 
                   
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
    $cfg = new fields_types_cfg($field['configuration']);
            
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . ' chosen-select field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                        'multiple'=>'multiple',
                        'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
              
    //use global lists if exsit    
    if($cfg->get('use_global_list')>0)
    {
      $choices = global_lists::get_choices($cfg->get('use_global_list'),($field['is_required']==1 ? false:true));
      $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
    }
    else
    {                    
      $choices = fields_choices::get_choices($field['id'],($field['is_required']==1 ? false:true));
      $default_id = fields_choices::get_default_id($field['id']);
    }
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ($params['form']=='comment' ? '':$default_id)); 
    
    return select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes);
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
      return global_lists::render_value($options['value'], $is_export);
    }
    else
    {
      return fields_choices::render_value($options['value'], $is_export);
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