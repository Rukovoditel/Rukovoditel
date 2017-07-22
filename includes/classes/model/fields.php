<?php

class fields
{
	public static function get_choices($entities_id)
	{
		$choices = array();
		$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id='" . $entities_id . "' and  f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
		while($v = db_fetch_array($fields_query))
		{
			$choices[$v['id']] = fields_types::get_option($v['type'],'name',$v['name']);		
		}	
		
		return $choices;
	}
	
  public static function get_available_fields($entities_id,$required_types,$warn_message)
  { 
    $html = '';   
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . $required_types . ") and f.entities_id='" . $entities_id . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
    while($fields = db_fetch_array($fields_query))
    {
      $html .= '
        <tr>
          <td>' . $fields['id'] . '</td>
          <td>' . $fields['name'] . '</td>
        </tr>
      ';
    }
    
    if(strlen($html)>0)
    {
      return '
        <table class="table">
          <tr>
            <th>' . TEXT_ID . '</th>
            <th>' . TEXT_NAME . '</th>
          </tr>
          ' . $html  . '
        </table>
      ';
    }
    else
    {
      return '<div class="alert alert-warning">' . $warn_message . '</div>'; 
    }
  }
  
  public static function check_before_delete($id)
  {     
    return '';
  }
  
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_fields',$id);
    
    return $obj['name'];
  }
  
  public static function get_name_cache()
  {
    $cache = array();
    $fields_query = db_query("select * from app_fields");
    while($fields = db_fetch_array($fields_query))
    {
      switch($fields['type'])
      {
        case 'fieldtype_date_added':
            $cache[$fields['id']] = TEXT_FIELDTYPE_DATEADDED_TITLE;
          break;
        default:
            $cache[$fields['id']] = $fields['name'];
          break;
      }
    }
    
    return $cache;
    
  }
  
  public static function get_heading_id($entity_id)
  {
    $info_query = db_query("select * from app_fields where entities_id='" . db_input($entity_id) . "' and is_heading=1");
    if($v = db_fetch_array($info_query))
    {
      return $v['id'];
    }
    else
    {
      return false;
    }
    
    
  }
  
  public static function get_last_sort_number($forms_tabls_id)
  {
    $v = db_fetch_array(db_query("select max(sort_order) as max_sort_order from app_fields where forms_tabs_id = '" . db_input($forms_tabls_id) . "'"));
    
    return $v['max_sort_order'];
  } 
  
  public static function render_required_messages($entities_id)
  {
    $html = '';
    
    $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($entities_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      if(strlen($v['required_message'])>0)
      {
        switch($v['type'])
        {          
          case 'fieldtype_checkboxes':
              $name = 'fields[' . $v['id'] . '][]';
            break;
          default:
              $name = 'fields[' . $v['id'] . ']';
            break;
        }
        $html .='\'' . $name . '\':{required: "' . str_replace(array("\n","\r","\n\r",'<br><br>'),"<br>",htmlspecialchars($v['required_message'])) . '"},' . "\n";
      }
    }
    
    return $html;
  }
  
  public static function render_required_ckeditor_ruels($entities_id)
  {
    $html = '';
    
    $fields_query = db_query("select f.* from app_fields f where f.type = 'fieldtype_textarea_wysiwyg' and is_required=1 and  f.entities_id='" . db_input($entities_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
        $html .='
          "fields[' . $v['id'] . ']": { 
            required: function(element){
              CKEDITOR_holders["fields_' . $v['id'] . '"].updateElement();              
              return true;             
            }
          },' . "\n";
    }
    
    return $html;
  }
    
  public static function get_search_feidls($entity_id)
  {
    global $fields_access_schema;
    
    $search_fields = array();
        
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$v['id']]))
      { 
        if($fields_access_schema[$v['id']]=='hide') continue;
      }
      
      $cfg = fields_types::parse_configuration($v['configuration']);      
      if(isset($cfg['allow_search']))
      {
        $search_fields[] = array('id'=>$v['id'],'name'=>fields_types::get_option($v['type'],'name',$v['name']),'is_heading'=>$v['is_heading']); 
      }
    } 
    
    return $search_fields; 
  }
  
  public static function get_filters_choices($entity_id, $show_parent_item_fitler = true)
  {
    global $app_user;
    
    $entity_info = db_find('app_entities',$entity_id);
    
    $fields_access_schema = users::get_fields_access_schema($entity_id,$app_user['group_id']);
    
    $types_for_filters_list = fields_types::get_types_for_filters_list();
    
    //include fieldtype_parent_item_id only for sub entities
    if($entity_info['parent_id']>0 and $show_parent_item_fitler)
    {
      $types_for_filters_list .= ", 'fieldtype_parent_item_id'";
    }
    
    //include special filters for Users
    if($entity_id==1)
    {
    	$types_for_filters_list .= ", 'fieldtype_user_accessgroups', 'fieldtype_user_status'";
    }
                
    $choices = array();
    $choices[''] = '';    
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in (" . $types_for_filters_list . ")  and f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$v['id']]))
      { 
        if($fields_access_schema[$v['id']]=='hide') continue;
      }
        
      $choices[$v['id']] = fields_types::get_option($v['type'],'name',$v['name']); 
    } 
    
    return $choices;
  }
  
  public static function check_if_type_changed($field_id, $new_type)
  {
    $field_info_query = db_query("select * from app_fields where id='" . db_input($field_id) . "'");
    if($field_info = db_fetch_array($field_info_query))
    {
      //check if field type changed
      if($field_info['type']!=$new_type)
      {        
 				//prepare db field type
 				db_query("ALTER TABLE app_entity_" . $field_info['entities_id']. " CHANGE field_" . $field_info['id'] . " field_" . $field_info['id'] . " " . entities::prepare_field_type($new_type) . " NOT NULL;");
      	
        //delete all filters for this field type since they are will not work correclty
        db_delete_row('app_reports_filters',$field_id,'fields_id');
      }
    }                         
  }
  
  public static function get_items_fields_data_by_id($item, $fields_list = '',$entities_id, $fields_access_schema)
  {
    global $app_choices_cache, $app_users_cache;
    
    $data = array();
    
    if(strlen($fields_list)>0)
    {
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where  f.id in (" . $fields_list . ") and  f.entities_id='" . db_input($entities_id) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {   
        //check field access
        if(isset($fields_access_schema[$field['id']]))
        {
          if($fields_access_schema[$field['id']]=='hide') continue;
        }
                   
        if(in_array($field['type'],fields_types::get_reserved_data_types()))
        {
          $value = $item[fields_types::get_reserved_filed_name_by_type($field['type'])];
        }
        else
        {
          $value = $item['field_' . $field['id']];
        }
      
        $output_options = array('class'=>$field['type'],
                                'value'=>$value,
                                'field'=>$field,
                                'item'=>$item,
                                'is_listing'=>true,
                                'is_export' => true,                                
                                'redirect_to' => '',
                                'reports_id'=> 0,
                                'path'=> '');
                                
        $data[] = array('name'=> fields_types::get_option($field['type'],'name',$field['name']),'value'=>fields_types::output($output_options));
      }
    }
    
    return $data;
  } 
        
}