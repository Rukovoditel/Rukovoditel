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
  
  function form_tag($name,$action,$attributes = array())
  {
  	global $app_session_token;
  	
    $default = array('name'=>$name,'id'=>generate_id_from_name($name),'method'=>'post');
    
    return '<form action="' . $action . '" ' . tag_attributes_to_html($default,$attributes) . '> ' . input_hidden_tag('form_session_token',$app_session_token);
  }
  
  
  function input_tag($name='',$value='',$attributes=array())
  {
    $default = array('name'=>$name,'id'=>generate_id_from_name($name),'value'=>$value, 'type'=>'text');
    
    return '<input ' . tag_attributes_to_html($default,$attributes) . '>';
  }
  
  function submit_tag($value='',$attributes=array())
  {
    $attributes = array_merge(array('type'=>'submit','class'=>'btn btn-primary'),$attributes);
            
    return input_tag('',$value,$attributes);
  }
  
  function input_password_tag($name,$attributes=array())
  {
    $attributes = array_merge($attributes, array('type'=>'password'));
           
    return input_tag($name,'',$attributes);                        
  }
  
  function input_file_tag($name,$attributes=array())
  {
    $attributes = array_merge($attributes, array('type'=>'file'));
           
    return input_tag($name,'',$attributes);                        
  }
  
  function input_hidden_tag($name,$value='', $attributes=array())
  {
    $attributes = array_merge($attributes, array('type'=>'hidden'));
           
    return input_tag($name,$value,$attributes);                        
  }
  
  function input_checkbox_tag($name,$value='1', $attributes=array())
  {
    $attributes = array_merge($attributes, array('type'=>'checkbox'));
    
    if(isset($attributes['checked']))
    {
      if(is_numeric($attributes['checked']))
      {
        $attributes['checked'] = (bool)$attributes['checked'];
      }
    }
           
    return input_tag($name,$value,$attributes);                        
  }
  
  function input_radiobox_tag($name,$value='1', $attributes=array())
  {
    $attributes = array_merge($attributes, array('type'=>'radio'));
    
    if(isset($attributes['checked']))
    {
      if(is_numeric($attributes['checked']))
      {
        $attributes['checked'] = (bool)$attributes['checked'];
      }
    }
           
    return input_tag($name,$value,$attributes);                        
  }  
  
  function select_tag($name,$choices=array(),$value='',$attributes=array())
  {
    $default = array('name'=>$name,'id'=>generate_id_from_name($name));
     
    $html = '';
    
    if(!is_array($value))
    {    	
      $value = (strlen($value) ? explode(',',$value) : array());
    }
    
    foreach($choices as $k=>$v)
    {  
      if(is_array($v))
      {
        $html_optgroup = '';
        foreach($v as $kk=>$vv)
        {
          $html_optgroup .= '<option ' . (in_array($kk,$value) ? 'selected':''). ' value="' . $kk . '">' .  htmlspecialchars((string)$vv,ENT_QUOTES). '</option>';
        }
        
        $html .= '<optgroup label="' . htmlspecialchars((string)$k,ENT_QUOTES) . '">' . $html_optgroup . '</optgroup>';
      }
      else
      {                       
        $html .= '<option ' . (in_array($k,$value) ? 'selected':''). ' value="' . $k . '">' .  htmlspecialchars((string)$v,ENT_QUOTES). '</option>';
      }
    }
    
    return '<select ' . tag_attributes_to_html($default,$attributes) . '>' . $html . '</select>';
  }
  
  function select_checkboxes_tag($name,$choices=array(),$value='',$attributes=array())
  {         
    $html = '';  
    
    foreach($choices as $k=>$v)
    { 
      if(is_array($v))
      {
        $html .= '<div><strong>' . $k . '</strong></div>';
        
        foreach($v as $kk=>$vv)
        {        
          if(in_array($kk,explode(',',$value)))
          {
            $attributes['checked'] = true;
          }
          else
          {
            $attributes['checked'] = false;
          }
          
          $attributes['id'] = generate_id_from_name($name . '[' . $kk . ']');
          
          $html .= '<div><label for="' . generate_id_from_name($name . '[' . $kk . ']') . '">' . input_checkbox_tag($name . '[]',$kk,$attributes) . ' ' . $vv . '</label></div>';
        }
      
      }
      else                               
      {           
        if(strlen($value)==0)
        {
          $attributes['checked'] = false;
        }
        elseif(in_array($k,explode(',',$value)))
        {
          $attributes['checked'] = true;
        }
        else
        {
          $attributes['checked'] = false;
        }
        
        $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');
        
        $html .= '<div><label for="' . generate_id_from_name($name . '[' . $k . ']') . '">' . input_checkbox_tag($name . '[]',$k,$attributes) . ' ' . $v . '</label></div>';
      } 
    }
    
    return '<div class="select_checkboxes_tag">' . $html . '</div> <label for="' . $name . '[]" class="error"></label>';
  }
  
  function select_radioboxes_tag($name,$choices=array(),$value='',$attributes=array())
  {         
    $html = '';
    
    foreach($choices as $k=>$v)
    {            
      if(in_array($k,explode(',',$value)))
      {
        $attributes['checked'] = true;
      }
      else
      {
        $attributes['checked'] = false;
      }
      
      $attributes['id'] = generate_id_from_name($name . '[' . $k . ']');
      
      $html .= '<div><label>' . input_radiobox_tag($name,$k,$attributes) . ' ' . $v . '</label></div>'; 
    }
    
    return '<div class="select_checkboxes_tag">' . $html . '</div>';
  }  
  
  function textarea_tag($name,$value='', $attributes=array())
  {
    $default = array('name'=>$name,'id'=>generate_id_from_name($name), 'wrap'=>'soft');
    
    return '<textarea ' . tag_attributes_to_html($default,$attributes) . '>' . htmlspecialchars((string) $value, ENT_NOQUOTES, 'UTF-8') . '</textarea>';
  }
  
  function button_tag($value,$url,$is_dialog = true,$attributes=array(),$left_icon='', $right_icon='')
  {
    $default = array('class'=>'btn btn-primary','type'=>'button');
    
    if(strlen($left_icon)>0) $left_icon = '<i class="fa ' . $left_icon . '"></i> ';
    if(strlen($right_icon)>0) $right_icon = ' <i class="fa ' . $right_icon . '"></i>';
    
    return '<button ' . ($is_dialog ? 'onClick="open_dialog(\'' . $url . '\'); return false;"': (strlen($url)>0 ? 'onClick="location.href=\'' . $url . '\'"':'')) . ' ' . tag_attributes_to_html($default,$attributes) . '>' . $left_icon . $value . $right_icon . '</button>';
  }
  
  function button_icon($title,$class,$url,$is_dialog = true)
  {
  	if($is_dialog)
  	{	
    	return '<a ' . tag_attributes_to_html(array('title'=>$title)) . ' class="btn btn-default btn-xs purple" href="#" onClick="open_dialog(\'' . $url . '\'); return false;"><i class="' . $class . '"></i></a>';
  	}
  	else
  	{
  		return '<a ' . tag_attributes_to_html(array('title'=>$title)) . ' class="btn btn-default btn-xs purple" href="'  . $url . '"><i class="' . $class . '"></i></a>';
  	}
  }
  
  function button_icon_delete($url,$is_dialog = true)
  {
    return button_icon(TEXT_BUTTON_DELETE,'fa fa-trash-o',$url,$is_dialog);
  }
  
  function button_icon_edit($url,$is_dialog = true)
  {
    return button_icon(TEXT_BUTTON_EDIT,'fa fa-edit',$url,$is_dialog);
  }
  
  function image_tag($path,$attributes = array())
  {
    $default = array('border'=>'0');
    
    return '<img src="' . $path . '" ' . tag_attributes_to_html($default,$attributes) . '>';
  }
  
  function select_button_tag($choices=array(),$value='',$btn_class='btn-default')
  {

    
    $html = '
    <div class="btn-group">
			<button type="button" class="btn ' . $btn_class . '">' . $value . '</button>
			<button type="button" class="btn ' . $btn_class . ' dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"><i class="fa fa-angle-down"></i></button>
			<ul class="dropdown-menu" role="menu">
				<li>
				' . implode('</li><li>',$choices). '
				</li>
			</ul>
		</div>
    '; 
    
    return $html;
  }  
  
  