<?php

class fieldtype_id
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name'=>TEXT_FIELDTYPE_ID_TITLE);
  }
  
  function output($options)
  {
    return $options['value'];
  }
}