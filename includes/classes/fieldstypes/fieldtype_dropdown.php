<?php

class fieldtype_dropdown
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_DROPDOWN_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_NOTIFY_WHEN_CHANGED, 'name'=>'notify_when_changed','type'=>'checkbox','tooltip_icon'=>TEXT_NOTIFY_WHEN_CHANGED_TIP);
    
    $cfg[] = array('title'=>TEXT_DEFAULT_TEXT, 
                   'name'=>'default_text',
                   'type'=>'input',                   
                   'tooltip_icon'=>TEXT_DEFAULT_TEXT_INFO,
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip_icon'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_USE_SEARCH, 
                   'name'=>'use_search',
                   'type'=>'dropdown',
                   'choices'=>array('0'=>TEXT_NO,'1'=>TEXT_YES),
                   'tooltip'=>TEXT_USE_SEARCH_INFO,
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
            
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':'') . ($cfg->get('use_search')==1 ? ' chosen-select':''),
                        'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
    
//use global lists if exsit    
    if($cfg->get('use_global_list')>0)
    {
      $choices = global_lists::get_choices($cfg->get('use_global_list'),(($field['is_required']==0 or strlen($cfg->get('default_text'))>0) ? true:false), $cfg->get('default_text'));
      $default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
    }
    else
    {                    
      $choices = fields_choices::get_choices($field['id'],(($field['is_required']==0 or strlen($cfg->get('default_text'))>0) ? true:false), $cfg->get('default_text'));
      $default_id = fields_choices::get_default_id($field['id']); 
    }
    
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ($params['form']=='comment' ? '':$default_id)); 
    
    return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
  }
  
  function process($options)
  {
    global $app_changed_fields, $app_choices_cache;
    
    if(!$options['is_new_item'])
    {
      $cfg = new fields_types_cfg($options['field']['configuration']);
      
      if($options['value']!=$options['current_field_value'] and $cfg->get('notify_when_changed')==1)
      {
        $app_changed_fields[] = array('name'=>$options['field']['name'],'value'=>$app_choices_cache[$options['value']]['name']);
      }
    }
  
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
    
    $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');
  
    $sql_query[] = $prefix . '.field_' . $filters['fields_id'] .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters['filters_values'] . ') ';
    
    return $sql_query;
  }
}