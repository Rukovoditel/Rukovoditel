<?php

class fieldtype_boolean
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_BOOLEAN_TITLE);
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
                   
                   
    $cfg[] = array('title'=>TEXT_BOOLEAN_TRUE_VALUE, 
                   'name'=>'text_boolean_true',
                   'type'=>'input',                   
                   'tooltip_icon'=>TEXT_BOOLEAN_TRUE_VALUE_INFO,
                   'params'=>array('class'=>'form-control input-small'));
    
    $cfg[] = array('title'=>TEXT_BOOLEAN_FALSE_VALUE, 
                   'name'=>'text_boolean_false',
                   'type'=>'input',                   
                   'tooltip_icon'=>TEXT_BOOLEAN_FALSE_VALUE_INFO,
                   'params'=>array('class'=>'form-control input-small'));
                                 
    
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    $cfg = new fields_types_cfg($field['configuration']);
            
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
           
    $add_empty = (($field['is_required']==0 or strlen($cfg->get('default_text'))>0) ? true:false);
                        
    $choices = self::get_choices($field, $add_empty);
        
    $default_id = (!$add_empty ? 'true':'');
                 
    $value = (strlen($obj['field_' . $field['id']])>0 ? $obj['field_' . $field['id']] : $default_id); 
    
    return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
  }
  
  static function get_choices($field, $add_empty=false)
  {
    $cfg = new fields_types_cfg($field['configuration']);
            
    $choices = array();
    
    if($add_empty)
    {
      $choices[''] = $cfg->get('default_text');
    }    
            
    $choices['true'] = (strlen($cfg->get('text_boolean_true'))>0 ? $cfg->get('text_boolean_true') : TEXT_BOOLEAN_TRUE);
    $choices['false'] = (strlen($cfg->get('text_boolean_true'))>0 ? $cfg->get('text_boolean_false') : TEXT_BOOLEAN_FALSE);
    
    return $choices;        
  }
  
  static function get_boolean_value($field, $value)
  {
    $cfg = new fields_types_cfg($field['configuration']);
    
    switch($value)
    {
      case 'true': 
          return (strlen($cfg->get('text_boolean_true'))>0 ? $cfg->get('text_boolean_true') : TEXT_BOOLEAN_TRUE);
        break;
      case 'false':
          return (strlen($cfg->get('text_boolean_true'))>0 ? $cfg->get('text_boolean_false') : TEXT_BOOLEAN_FALSE);
        break;
      default:
          return '';
        break;  
        
    }
  }
  
  function process($options)
  {
    global $app_changed_fields, $app_choices_cache;
    
    if(!$options['is_new_item'])
    {
      $cfg = new fields_types_cfg($options['field']['configuration']);
      
      if($options['value']!=$options['current_field_value'] and $cfg->get('notify_when_changed')==1)
      {
        $app_changed_fields[] = array('name'=>$options['field']['name'],'value'=>self::get_boolean_value($options['field'], $options['value']));
      }
    }
  
    return $options['value'];
  }
  
  function output($options)
  {                
    return self::get_boolean_value($options['field'], $options['value']);    
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
    
    $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');
  
    $sql_query[] = $prefix . '.field_' . $filters['fields_id'] .  ($filters['filters_condition']=='include' ? ' = ': ' != ') . "'" . $filters['filters_values'] . "'";
    
    return $sql_query;
  }
}