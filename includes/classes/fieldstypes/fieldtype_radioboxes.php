<?php

class fieldtype_radioboxes
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_RADIOBOXES_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
    
    $cfg[] = array('title'=>TEXT_DISPLAY_CHOICES_VALUES, 'name'=>'display_choices_values','type'=>'checkbox','tooltip_icon'=>TEXT_DISPLAY_CHOICES_VALUES_TIP);
    
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
           
    $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),'data-raido-list'=>$field['id']);
               
//use global lists if exsit       
    if($cfg->get('use_global_list')>0)
    {
      $choices = global_lists::get_choices($cfg->get('use_global_list'),false);
      $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
    }
    else
    {                         
      $choices = fields_choices::get_choices($field['id'],false,'',$cfg->get('display_choices_values'));
      $default_id = fields_choices::get_default_id($field['id']);
    }
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : $default_id); 
    
    return '<div class="radio-list radio-list-' . $field['id'] . '">' . select_radioboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
  }
  
  function process($options)
  {            
    return $options['value'];
  }
  
  function output($options)
  {
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    //render global list value
    if($cfg->get('use_global_list')>0)
    {
      return global_lists::render_value($options['value']);
    }
    else
    {    
      return fields_choices::render_value($options['value']);
    }
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql_query[] = 'field_' . $filters['fields_id'] .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters['filters_values'] . ') ';
    
    return $sql_query;
  }  
}