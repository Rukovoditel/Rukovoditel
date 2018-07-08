<?php

class fieldtype_input_email
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_EMAIL_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_DISPLAY_AS_LINK, 'name'=>'display_as_link','type'=>'checkbox');
    
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip_icon'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
                         
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
    
    $cfg[] = array('title'=>TEXT_IS_UNIQUE_FIELD_VALUE, 'name'=>'is_unique','type'=>'checkbox','tooltip_icon'=>TEXT_IS_UNIQUE_FIELD_VALUE_TIP);                     
    $cfg[] = array('title'=>TEXT_ERROR_MESSAGE, 'name'=>'unique_error_msg','type'=>'input','tooltip_icon'=>TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,'tooltip'=>TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR,'params'=>array('class'=>'form-control input-xlarge'));
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg =  new fields_types_cfg($field['configuration']);
    
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . 
                                 ' fieldtype_input field_' . $field['id'] . 
    														 ($field['is_heading']==1 ? ' autofocus':'') .
                                 ($field['is_required']==1 ? ' required email noSpace':'') .
                                 ($cfg->get('is_unique')==1 ? ' is-unique':''),
    										'type' => 'email'	
                                );
    $attributes = fields_types::prepare_uniquer_error_msg_param($attributes,$cfg);
    
    return input_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],$attributes);
  }
  
  function process($options)
  {
    return db_prepare_input($options['value']);
  }
  
  function output($options)
  {
  	$cfg = new fields_types_cfg($options['field']['configuration']);
  	
  	if(isset($options['is_export']))
  	{
  		return  $options['value'];
  	}
  	elseif($cfg->get('display_as_link')==1)
  	{
  		return '<a href="mailto:' . $options['value'] . '" target="_blank">' . $options['value'] . '</a>';
  	}
  	else
  	{
  		return $options['value'];
  	}
    
  }
}