<?php

class fieldtype_user_skin
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_SKIN_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {
    return select_tag('fields[' . $field['id'] . ']',app_get_skins_choices(false),$obj['field_' . $field['id']],array('class'=>'form-control input-medium'));
  }
  
  function process($options)
  {
    return $options['value'];
  }
  
  function output($options)
  {
    return $options['value'];
  }
}