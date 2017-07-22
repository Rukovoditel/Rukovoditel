<?php

class fieldtype_users
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_USERS_TITLE);
  }
  
  function get_configuration($params = array())
  {
  	$entity_info = db_find('app_entities',$params['entities_id']);
  	
    $cfg = array();
    $cfg[] = array('title'=>TEXT_DISPLAY_USERS_AS, 
                   'name'=>'display_as',
                   'tooltip'=>TEXT_DISPLAY_USERS_AS_TOOLTIP,
                   'type'=>'dropdown',
                   'params'=>array('class'=>'form-control input-medium'),
                   'choices'=>array('dropdown'=>TEXT_DISPLAY_USERS_AS_DROPDOWN,'checkboxes'=>TEXT_DISPLAY_USERS_AS_CHECKBOXES,'dropdown_muliple'=>TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE));
    
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_NAME, 'name'=>'hide_field_name','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_NAME_TIP);
    
    $cfg[] = array('title'=>TEXT_DISABLE_NOTIFICATIONS, 'name'=>'disable_notification','type'=>'checkbox','tooltip_icon'=>TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO);
    
    if($entity_info['parent_id']>0)
    {
    	$cfg[] = array('title'=>TEXT_DISABLE_USERS_DEPENDENCY, 'name'=>'disable_dependency','type'=>'checkbox','tooltip_icon'=>TEXT_DISABLE_USERS_DEPENDENCY_INFO);
    }       
    
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    global $app_users_cache;
    
    $cfg = new fields_types_cfg($field['configuration']);
                                                    
    $entities_id = $field['entities_id'];
    
    //get access schema
    $access_schema = users::get_entities_access_schema_by_groups($entities_id); 
                
    //check if parent item has users fields and if users are assigned
    $has_parent_users = false;
    $parent_users_list = array();
    
    if(isset($params['parent_entity_item_id']) and $cfg->get('disable_dependency')!=1)
    {
	    if($params['parent_entity_item_id']>0)
	    {
	      $entity_info = db_find('app_entities',$entities_id);
	      $parent_entity_id = $entity_info['parent_id'];
	             
	      $parent_users_fields = array(); 
	      $parent_fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_users') and  f.entities_id='" . db_input($parent_entity_id) . "'");
	      while($parent_field = db_fetch_array($parent_fields_query))
	      {
	        $has_parent_users = true;
	        
	        $parent_users_fields[] = $parent_field['id'];
	      }
	      
	      $parent_item_info = db_find('app_entity_' . $parent_entity_id,$params['parent_entity_item_id']);
	      
	      foreach($parent_users_fields as $id)
	      {
	        $parent_users_list = array_merge(explode(',',$parent_item_info['field_' . $id]),$parent_users_list);
	      }        
	    }
    }
    
    
    
    //get users choices
    $choices = array();
    $order_by_sql = (CFG_APP_DISPLAY_USER_NAME_ORDER=='firstname_lastname' ? 'u.field_7, u.field_8' : 'u.field_8, u.field_7');
    $users_query = db_query("select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 order by " . $order_by_sql);
    while($users = db_fetch_array($users_query))
    {
      if(!isset($access_schema[$users['field_6']]))
      {
        $access_schema[$users['field_6']] = array();
      }
        
      if($users['field_6']==0 or in_array('view',$access_schema[$users['field_6']]) or in_array('view_assigned',$access_schema[$users['field_6']]))
      {
        if($has_parent_users and !in_array($users['id'],$parent_users_list)) continue;
        
        $group_name = (strlen($users['group_name'])>0 ? $users['group_name'] : TEXT_ADMINISTRATOR);
        $choices[$group_name][$users['id']] = $app_users_cache[$users['id']]['name'];
      } 
    }
        
    $value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ''); 
    
    if($cfg->get('display_as')=='dropdown')
    {
      $attributes = array('class'=>'form-control chosen-select input-large field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
      return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
    }
    elseif($cfg->get('display_as')=='checkboxes')
    {
      $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
      
      return '<div class="checkboxes_list ' . ($field['is_required']==1 ? ' required':'') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
    }
    elseif($cfg->get('display_as')=='dropdown_muliple')
    {      
      $attributes = array('class'=>'form-control input-xlarge chosen-select field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                          'multiple'=>'multiple',
                          'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
      return select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes);
    }
    
  }
  
  function process($options)
  {
    global $app_send_to,$app_send_to_new_assigned;
    
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    if($cfg->get('disable_notification')!=1)
    {
	    if(is_array($options['value']))
	    {
	      $app_send_to = array_merge($options['value'],$app_send_to);
	    }
	    else
	    {
	      $app_send_to[] = $options['value'];
	    }
    }
    
    $value = (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
    
    //check if value changed
    if($cfg->get('disable_notification')!=1)
    {
	    if(!$options['is_new_item'])
	    {            
	      if($value!=$options['current_field_value'])
	      {
	        foreach(array_diff(explode(',',$value),explode(',',$options['current_field_value'])) as $v)
	        {
	          $app_send_to_new_assigned[] = $v;
	        }                      
	      }
	    } 
    }
            
    return $value;
  }
  
  function output($options)
  {
    global $app_users_cache;
    
    if(isset($options['is_export']))
    {
      $users_list = array(); 
      foreach(explode(',',$options['value']) as $id)
      {
        if(isset($app_users_cache[$id]))
        {              
          $users_list[] = $app_users_cache[$id]['name'];
        }
      }
      
      return implode(', ',$users_list);
    }
    else
    {
      $users_list = array(); 
      foreach(explode(',',$options['value']) as $id)
      {
        if(isset($app_users_cache[$id]))
        {
          
          if(isset($options['display_user_photo']))
          {                    
            $photo = '<div class="user-photo-box">' . render_user_photo($app_users_cache[$id]['photo']) . '</div>';
            $is_photo_display = true;          
          }
          else
          {
            $photo = '';
            $is_photo_display = false;
          }
                  
          $users_list[] = $photo . ' <div class="user-name" ' . users::render_publi_profile($app_users_cache[$id],$is_photo_display). '>' . $app_users_cache[$id]['name'] . '</div> <div style="clear:both"></div>';
        }
      }
      
      return implode('',$users_list);
    }
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