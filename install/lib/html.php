<?php

  function tag_attributes_to_html($default,$attributes = array())
  {
    $attributes = array_merge($default, $attributes);
    
    return implode('', array_map('tag_attributes_to_html_callback', array_keys($attributes), array_values($attributes)));
  }
  
  function tag_attributes_to_html_callback($k, $v)
  {
    return false === $v || null === $v || ('' === $v && 'value' != $k) ? '' : sprintf(' %s="%s"', $k, htmlspecialchars((string)$v,ENT_QUOTES));
  }
  
  function generate_id_from_name($name)
  {
    // check to see if we have an array variable for a field name
    if (strstr($name, '['))
    {
      $name = str_replace(array('[]', '][', '[', ']'), array('', '_', '_', ''), $name);
    }
    
    // remove illegal characters
    $name = preg_replace(array('/^[^A-Za-z]+/', '/[^A-Za-z0-9\:_\.\-]/'), array('', '_'), $name);

    return $name;
  }
  

  function select_tag($name,$choices=array(),$value='',$attributes=array())
  {
    $default = array('name'=>$name,'id'=>generate_id_from_name($name));
     
    $html = '';
    
    foreach($choices as $k=>$v)
    {  
      if(is_array($v))
      {
        $html_optgroup = '';
        foreach($v as $kk=>$vv)
        {
          $html_optgroup .= '<option ' . ($kk==$value ? 'selected':''). ' value="' . $kk . '">' .  htmlspecialchars((string)$vv,ENT_QUOTES). '</option>';
        }
        
        $html .= '<optgroup label="' . htmlspecialchars((string)$k,ENT_QUOTES) . '">' . $html_optgroup . '</optgroup>';
      }
      else
      {
        $html .= '<option ' . ($k==$value ? 'selected':''). ' value="' . $k . '">' .  htmlspecialchars((string)$v,ENT_QUOTES). '</option>';
      }
    }
    
    return '<select ' . tag_attributes_to_html($default,$attributes) . '>' . $html . '</select>';
  }
  


