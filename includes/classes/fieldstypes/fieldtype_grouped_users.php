<?php

class fieldtype_grouped_users
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_GROUPEDUSERS_TITLE,'has_choices'=>true);
  }
  
  function get_configuration($params = array())
  {
  
    $cfg = array();
                   
    $cfg[] = array('title'=>TEXT_DISPLAY_USERS_AS, 
                   'name'=>'display_as',
                   'tooltip'=>TEXT_DISPLAY_USERS_AS_TOOLTIP,
                   'type'=>'dropdown',
                   'choices'=>array('dropdown'=>TEXT_DISPLAY_USERS_AS_DROPDOWN,'checkboxes'=>TEXT_DISPLAY_USERS_AS_CHECKBOXES,'dropdown_muliple'=>TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE),
                   'params'=>array('class'=>'form-control input-medium'));  
                   
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_DEFAULT_TEXT,
					    		 'name'=>'default_text',
					    		 'type'=>'input',
					    		 'tooltip'=>TEXT_DEFAULT_TEXT_INFO,
					    		 'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_DISABLE_NOTIFICATIONS, 'name'=>'disable_notification','type'=>'checkbox','tooltip_icon'=>TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO);
    
    return $cfg;
  }   
    
  function render($field,$obj,$params = array())
  {                
    $attributes = array('class'=>'form-control input-medium field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
                        
    
    
    $value = $obj['field_' . $field['id']];
    $value = ($value>0 ? $value : fields_choices::get_default_id($field['id'])); 
            
    $cfg = new fields_types_cfg($field['configuration']);
    
    $display_as = (strlen($cfg->get('display_as'))>0 ? $cfg->get('display_as') : 'dropdown');
    
    if($display_as=='dropdown')
    {
    	$choices = fields_choices::get_choices($field['id'],(($field['is_required']==0 or strlen($cfg->get('default_text'))>0) ? true:false), $cfg->get('default_text'));
    }
    else
    {
    	$choices = fields_choices::get_choices($field['id'],false);
    }
    
    switch($display_as)
    {    
      case 'dropdown':
          $attributes = array('class'=>'form-control ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
          
          return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
        break;
      case 'checkboxes':      
          $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
          
          return '<div class="checkboxes_list ' . ($field['is_required']==1 ? ' required':'') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
        break;
      case 'dropdown_muliple':        
          $attributes = array('class'=>'form-control chosen-select ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                              'multiple'=>'multiple',
                              'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
          
          return select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes);
        break;
    }
  }
  
  function process($options)
  {
    global $app_send_to, $app_send_to_new_assigned;
    
    $value = (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
    
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    if($cfg->get('disable_notification')!=1)
    {
	    foreach(explode(',',$value) as $choices_id)
	    {
	      $choice_query = db_query("select * from app_fields_choices where id='" . db_input($choices_id) . "'");        
	      if($choice = db_fetch_array($choice_query))
	      {            
	        foreach(explode(',',$choice['users']) as $id)
	        {
	          $app_send_to[] = $id;
	        }
	        
	        //check if value changed
	        if(!$options['is_new_item'])
	        {                      
	          if(!in_array($choices_id,explode(',',$options['current_field_value'])))
	          {   
	            foreach(explode(',',$choice['users']) as $id)
	            {                    
	              $app_send_to_new_assigned[] = $id;
	            }                              
	          }
	        }
	        
	      }
	    } 
    }
                           
    return $value;
  }
  
  function output($options)
  {    
    $value = $options['value']; 
        
    return fields_choices::render_value($value);
  } 
  
	function reports_query($options)
  {  	  
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
      
		if(strlen($filters['filters_values'])>0)
    {  
      $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id=e.id and cv.fields_id='" . db_input($options['filters']['fields_id'])  . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition']=='include' ? '>0': '=0');
    }
    
    return $sql_query;
  } 
}