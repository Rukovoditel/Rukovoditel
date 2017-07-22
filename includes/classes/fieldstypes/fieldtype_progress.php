<?php

class fieldtype_progress
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_PROGRESS_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();    
    $cfg[] = array('title'=>TEXT_SETP, 
                   'name'=>'step',
                   'type'=>'dropdown',
                   'choices'=>array('5'=>5,'10'=>10,'1'=>1),                   
                   'params'=>array('class'=>'form-control input-small'));      
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg = fields_types::parse_configuration($field['configuration']);
    
    $attributes = array('class'=>'form-control input-small fieldtype_input field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
    
    $value = $obj['field_' . $field['id']];
    
    $values = array();    
    if($params['form']=='comment')
    {
      $values['']=''; 
      $value = '';     
    }
    else
    {
      $values['0']='0%';
    }
    
    
    
    for($i=$cfg['step'];$i<=100;$i+=$cfg['step'])
    {
      $values[$i]=$i . '%';
    }
    
    return select_tag('fields[' . $field['id'] . ']',$values,$value,$attributes);
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    if(strlen($options['value'])>0)
    {
      return $options['value'] . '%';
    }
    else
    {
      return '';
    }
  }
}