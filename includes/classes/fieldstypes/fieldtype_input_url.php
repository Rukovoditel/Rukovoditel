<?php

class fieldtype_input_url
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_URL_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    $cfg[] = array('title'=>TEXT_URL_PREVIEW_TEXT, 'name'=>'preview_text','type'=>'input','tooltip_icon'=>TEXT_URL_PREVIEW_TEXT_TIP,'params'=>array('class'=>'form-control input-medium'));
    $cfg[] = array('title'=>TEXT_URL_PREFIX, 'name'=>'prefix','type'=>'input','tooltip_icon'=>TEXT_URL_PREFIX_TIP,'params'=>array('class'=>'form-control input-small'));
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);        
    
    return $cfg;
  }
    
  function render($field,$obj,$params = array())
  {
    return input_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],array('class'=>'form-control input-large fieldtype_input_url field_' . $field['id'] . ($field['is_required']==1 ? ' required noSpace':'')));
  }
  
  function process($options)
  {
    return db_prepare_input($options['value']);
  }
  
  function output($options)
  {       
    
    $cfg = fields_types::parse_configuration($options['field']['configuration']);
    
    $url = $options['value'];
    $url_text = TEXT_VIEW;
    
    if($cfg['preview_text']=='none')
    {
      $url_text = $url;
    }
    elseif(strlen($cfg['preview_text'])>0)
    {      
      $url_text = $cfg['preview_text']; 
    }
    
    
    if(strlen($cfg['prefix'])>0)
    {
      $url = (!stristr($url,$cfg['prefix']) ? $cfg['prefix'] : '')  . $url;
    }
    elseif(!stristr($url,'://'))
    {
      $url = 'http://' . $url;
    }
    
     
    if(strlen($options['value'])>0)
    {
      if(isset($options['is_export']))
      {
        return  $url;
      }
      else
      {
        return '<a href="' . $url . '" target="blank">' . $url_text. '</a>';
      }
    }
    else
    {
      return '';
    }
  }
}