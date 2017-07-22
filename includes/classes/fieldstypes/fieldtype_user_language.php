<?php

class fieldtype_user_language
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name' => TEXT_FIELDTYPE_USER_LANGUAGE_TITLE);
  }
  
  function render($field,$obj,$params = array())
  {
    $selected  = (strlen($obj['field_' . $field['id']])>0 ? $obj['field_' . $field['id']] : CFG_APP_LANGUAGE);
    return select_tag('fields[' . $field['id'] . ']',app_get_languages_choices(),$selected,array('class'=>'form-control input-medium required'));
  }
  
  function process($options)
  {
    return db_prepare_input($options['value']);
  }
  
  function output($options)
  {
    return $options['value'];
  }
}