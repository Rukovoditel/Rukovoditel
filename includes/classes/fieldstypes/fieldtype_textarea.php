<?php

class fieldtype_textarea
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_TEXTAREA_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
                             
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg = fields_types::parse_configuration($field['configuration']);
            
    $attributes = array('rows'=>'3',
                        'class'=>'form-control ' . $cfg['width'] .  ($field['is_heading']==1 ? ' autofocus':'') . ' fieldtype_textarea field_' . $field['id'] . ($field['is_required']==1 ? ' required noSpace':''));
    
    return textarea_tag('fields[' . $field['id'] . ']',str_replace(array('&lt;','&gt;'),array('<','>'),$obj['field_' . $field['id']]),$attributes);
  }
  
  function process($options)
  {
    return str_replace(array('<','>'),array('&lt;','&gt;'),$options['value']);
  }
  
  function output($options)
  {
  	if(isset($options['is_export']))
  	{
  	 	return (!isset($options['is_print']) ? str_replace(array('&lt;','&gt;'),array('<','>'),$options['value']) : nl2br($options['value']));	
  	}
  	else
  	{
    	return auto_link_text(nl2br($options['value']));
  	}
  }
}