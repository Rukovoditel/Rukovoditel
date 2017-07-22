<?php

class fieldtype_textarea_wysiwyg
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
        
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {                
    $attributes = array('class'=> 'form-control editor field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
    
    return textarea_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],$attributes);
  }
  
  function process($options)
  {
    return db_prepare_html_input($options['value']);
  }
  
  function output($options)
  {
    return auto_link_text($options['value']);
  }
}