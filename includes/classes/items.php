<?php

class items
{
	public static function get_info($entities_id, $items_id)
	{
		$item_query = db_query("select e.* " . fieldtype_formula::prepare_query_select($entities_id, '') . " from app_entity_" . $entities_id . " e where id='" . $items_id . "'");
		$item = db_fetch_array($item_query);
		
		return $item;
	}
	
	public static function delete($entities_id,$items_id)
	{
		//reset parent items
		$item_info = db_find("app_entity_" . $entities_id,$items_id);		
		db_query("update app_entity_" . $entities_id . "  set parent_id='" . $item_info['parent_id']. "' where parent_id='" . $items_id . "'");
		
		attachments::delete_attachments($entities_id,$items_id);
		
		db_delete_row('app_entity_' . $entities_id,$items_id);			
		
		comments::delete_item_comments($entities_id,$items_id);
		
		reports::delete_reports_by_item_id($entities_id,$items_id);
		
		choices_values::delete_by_item_id($entities_id,$items_id);
		
		related_records::delete_related_by_item_id($entities_id,$items_id);
		
		//delete notifications
		db_query("delete from app_users_notifications where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'");
		
		//delete timers
		if(class_exists('timer'))
		{
			db_query("delete from app_ext_timer where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'");
		}
		
		//delete gantt
		if(class_exists('ganttchart'))
		{
			db_query("delete from app_ext_ganttchart_depends where entities_id='" . $entities_id . "' and (item_id='" . db_input($items_id) . "' or depends_id='" . db_input($items_id) . "')");
		}
		
		//delete log changes
		if(class_exists('track_changes'))
		{
			track_changes::delete_log($entities_id,$items_id);
		}
		
	}
	
  public static function get_choices_by_entity($entity_id, $parent_entity_id, $add_empty = false)
  {            
    $listing_sql_query = '';
    $listing_sql_query_join = '';
    
    //add filters from defualt report
    $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($parent_entity_id). "' and reports_type='default'");
    if($default_reports = db_fetch_array($default_reports_query))
    {    
      $listing_sql_query = reports::add_filters_query($default_reports['id'],$listing_sql_query);
    }
          
    //check view assigned only access
    $listing_sql_query = items::add_access_query($parent_entity_id,$listing_sql_query);
  
    //include access to parent records
    $listing_sql_query .= items::add_access_query_for_parent_entities($parent_entity_id);
    
    $listing_sql_query .= items::add_listing_order_query_by_entity_id($parent_entity_id);
        
    //build query
    $listing_sql = "select e.* from app_entity_" . $parent_entity_id . " e "  . $listing_sql_query_join . "where e.id>0 " . $listing_sql_query;        
    $items_query = db_query($listing_sql);
    
    $choices = array();
    
    if($add_empty)
    {
    	$choices[''] = '';
    }
    
    while($item = db_fetch_array($items_query))
    {
      $path_info = items::get_path_info($parent_entity_id,$item['id']);
      
      //print_r($path_info);
            
      $parent_name = '';
      if(strlen($path_info['parent_name'])>0)
      {
        $parent_name = str_replace('<br>',' / ',$path_info['parent_name']) . ' / '; 
      }
                  
      $choices[$path_info['full_path'] . '/' . $entity_id] = $parent_name . self::get_heading_field($parent_entity_id,$item['id']); 
    }
    
    return $choices;
  }
  
  public static function get_heading_field($entity_id,$item_id, $item_info = null)
  {   
    global $app_users_cache;
                 
    if($entity_id==1)
    {
      return (isset($app_users_cache[$item_id]) ? $app_users_cache[$item_id]['name']:'');
    }
    
    $heading_field_id = fields::get_heading_id($entity_id);
    
    if($heading_field_id and !$item_info)
    {
    	$item_info = db_find('app_entity_' . $entity_id,$item_id);
    }
            
    return ($heading_field_id>0 ? self::get_heading_field_value($heading_field_id,$item_info) : $item_id);
  }
  
  public static function get_heading_field_value($heading_field_id,$item_info)
  {
    global $app_choices_cache, $app_users_cache, $app_heading_fields_cache;
            
    $heading_field_value = '';
    
    if(isset($item_info['field_' . $heading_field_id]))
    {
      $heading_field_value = $item_info['field_' . $heading_field_id];
    }
                
    if(isset($app_heading_fields_cache[$heading_field_id]))    
    {     
    	$field_info = $app_heading_fields_cache[$heading_field_id];
    	
      if(strlen($heading_field_value)==0 and !in_array($field_info['type'],array('fieldtype_id','fieldtype_created_by','fieldtype_date_added','fieldtype_text_pattern')))
      {
        return '';
      }
            
      switch($field_info['type'])
      {      	
        case 'fieldtype_text_pattern':                          
            $output_options = array('class'=>$field_info['type'],
                                    'value'=>'',
                                    'field'=>$field_info,
                                    'item'=>$item_info,
                                    'is_export'=>true,                              
                                    'path'=>$field_info['entities_id']);
                                               
            return fields_types::output($output_options);
          break;
        case 'fieldtype_id':
            return (strlen($field_info['name']) ? $field_info['name'] . ': ' . $item_info['id'] : $item_info['id']);
          break;
        case 'fieldtype_created_by':
              if(isset($app_users_cache[$item_info['created_by']]))
              {              
                return $app_users_cache[$item_info['created_by']]['name'];
              }
              else
              {
                return '';
              }
          break;
        case 'fieldtype_date_added':
            return format_date_time($item_info['date_added']);
          break;  
        case 'fieldtype_input_date':
            return format_date($heading_field_value);
          break;          
        case 'fieldtype_input_datetime':
            return format_date_time($heading_field_value);
          break;
        case 'fieldtype_checkboxes':
        case 'fieldtype_radioboxes':
        case 'fieldtype_dropdown':
        case 'fieldtype_dropdown_multiple':                
            
            $cfg = new fields_types_cfg($field_info['configuration']);
            
            if($cfg->get('use_global_list')>0)
            {
              return global_lists::render_value($heading_field_value,true);
            }
            else
            {
              return fields_choices::render_value($heading_field_value,true);
            }
          break;
        case 'fieldtype_grouped_users':            
            return fields_choices::render_value($heading_field_value,true);
          break;  
        case 'fieldtype_entity':
            $cfg = fields_types::parse_configuration($field_info['configuration']);
            
            $entity_heading_field_id = false;
            $fields_query = db_query("select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input($cfg['entity_id']) . "'");
            if($fields = db_fetch_array($fields_query))
            {
              $entity_heading_field_id = $fields['id'];
            }
    
            $output = array();
            foreach(explode(',',$heading_field_value) as $item_id)
            {
              $items_info_sql = "select e.* from app_entity_" . $cfg['entity_id'] . " e where e.id='" . db_input($item_id). "'";
              $items_query = db_query($items_info_sql);
              if($item = db_fetch_array($items_query))
              { 
                if($cfg['entity_id']==1)
                {
                  $output[] = $app_users_cache[$item['id']]['name'];
                }
                else
                {               
                  
                  if($entity_heading_field_id)
                  {
                    $output[] = self::get_heading_field_value($entity_heading_field_id,$item);               
                  }
                  else
                  {
                    $output[] = $item['id'];
                  }
                }
              }
            } 
            
            return implode(', ',$output);
             
          break;
        case 'fieldtype_users':
            $users_list = array(); 
            foreach(explode(',',$heading_field_value) as $id)
            {
              if(isset($app_users_cache[$id]))
              {              
                $users_list[] = $app_users_cache[$id]['name'];
              }
            }
            
            return implode(', ',$users_list);
          break;  
        default:
            return $heading_field_value;
          break;
      }  
    }
  }
  
  public static function get_breadcrumb_by_item_id($entity_id, $item_id)
  {
    $breadcrumb = array();
    $breadcrumb_html = array();
    
    $path_array = self::get_path_array($entity_id,$item_id);
    
    foreach($path_array as $v)
    {
      $breadcrumb[] = $v['name'];
      $breadcrumb_html[] = '<a href="' . url_for('items/info','path=' . $v['path'],true) . '">' . $v['name'] . '</a>'; 
    }
    
    return array('text'=>implode(' - ', $breadcrumb), 'html'=>implode(' - ', $breadcrumb_html));
  }
  
  public static function get_breadcrumb($path_array)
  {
    $breadcrumb = array();
    $path = '';
    
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      $entity_id = $vv[0];
      $item_id = (isset($vv[1]) ? $vv[1]:0); 
                  
      $entity_info = db_find('app_entities',$entity_id);
      $entity_cfg = entities::get_cfg($entity_id);      
      $heading_field_id = fields::get_heading_id($entity_id);            
      
      $entitiy_name = (strlen($entity_cfg['listing_heading'])>0 ? $entity_cfg['listing_heading'] : $entity_info['name']);
      
      //check if user have access to entity
      if(users::has_users_access_to_entity($entity_id))
      {	
      	$breadcrumb[] = array('url'=>url_for('items/items','path=' . $path . $entity_id),'title'=>$entitiy_name);
      }
      else
      {
      	$breadcrumb[] = array('title'=>$entitiy_name );
      }
      
      if($item_id>0)
      {
        $item_info = db_find('app_entity_' . $entity_id,$item_id);
        $item_name = ($heading_field_id>0 ? self::get_heading_field_value($heading_field_id,$item_info) : $item_info['id']);
        
        //check if user have access to entity
        if(users::has_users_access_to_entity($entity_id))
        {
        	$breadcrumb[] = array('url'=>url_for('items/info','path=' . $path . $entity_id . '-' . $item_id),'title'=>$item_name);
        }
        else
        {
        	$breadcrumb[] = array('title'=>$item_name);
        }
      }
          
      $path .= $entity_id . ($item_id>0 ? '-' . $item_id . '/':'');
    }
            
    return $breadcrumb;  
  }
  public static function render_breadcrumb($breadcrumb)
  {
    $html = '';
    foreach($breadcrumb as $v)
    {
     $html .= '
        <li>
          ' . (isset($v['url']) ? '<a href="' . $v['url']. '">' . $v['title'] . '</a>' : $v['title']) . '
          <i class="fa fa-angle-right"></i>
        </li>
      ';
    }
    
    return $html;  
  }
  
  public static function build_menu()
  {
    global $current_path,$current_path_array, $app_user;
                
    $entity_id = 0;
    $path_to_item = array();
    foreach($current_path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $entity_id = $vv[0];
        $item_id = $vv[1];
        
        $path_to_item[] = $v;
      }
    }
            
    $menu = array();
    
    
    
    if($entity_id>0)
    {        
      $parent_entity_cfg = new entities_cfg($entity_id);
      
      if(users::has_users_access_to_entity($entity_id))
      {	
      	$menu[] = array('title'=>(strlen($parent_entity_cfg->get('window_heading'))>0 ? $parent_entity_cfg->get('window_heading') : TEXT_INFO),'url'=>url_for('items/info','path=' . implode('/',$path_to_item)),'selected_id'=>$entity_id);
      }
      else
      {
      	$menu[] = array('title'=>(strlen($parent_entity_cfg->get('window_heading'))>0 ? $parent_entity_cfg->get('window_heading') : TEXT_INFO));
      }
      
      if($app_user['group_id']==0)
      {
        $entities_query = db_query("select e.* from app_entities e where parent_id='" . db_input($entity_id) . "' order by e.sort_order, e.name");
      }
      else
      {
        $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.parent_id = '" . db_input($entity_id) . "' order by e.sort_order, e.name");
      }
      
      
      while($entities = db_fetch_array($entities_query))
      {    
        $entity_cfg = new entities_cfg($entities['id']);
                
        //skip hidden in menu
        if($parent_entity_cfg->get('hide_subentity' . $entities['id'] . '_in_top_menu')==1) continue;
        
        $path = implode('/',$path_to_item) . '/' . $entities['id'];        
        
        $s = array();
        $s[] = array('title'=>TEXT_VIEW_ALL,'url'=>url_for('items/items','path=' . $path));
                
        if(users::has_access('create',users::get_entities_access_schema($entities['id'],$app_user['group_id'])))
        {
          $s[] = array('title'=>(strlen($entity_cfg->get('insert_button'))>0 ? $entity_cfg->get('insert_button') :TEXT_ADD),'url'=>url_for('items/form','path=' . $path),'modalbox'=>true);
        }        
        $menu[] = array('title'=>(strlen($entity_cfg->get('menu_title'))>0 ? $entity_cfg->get('menu_title') : $entities['name']),'url'=>url_for('items/items','path=' . $path), 'submenu'=>$s,'selected_id'=>$entities['id']);
      }
      
      $s = array();
      
      if(count($plugin_menu = plugins::include_menu('items_menu_reports'))>0)
      {
        $s = array_merge($s,$plugin_menu);
      }
      
      if(count($s)>0)
      {
        $menu[] = array('title'=>TEXT_REPORTS,'submenu'=>$s);
      }
    }
    
    return $menu;
  }
  

  public static function prepare_field_value_by_type($field,$item)
  {
    switch($field['type'])
    {
      case 'fieldtype_created_by':
          $value = $item['created_by'];
        break;
      case 'fieldtype_date_added':
          $value = $item['date_added'];                
        break;
      case 'fieldtype_action':                
      case 'fieldtype_id':
          $value = $item['id'];
        break;
      case 'fieldtype_parent_item_id':
          $value = '';
        break;
      default:
          $value = $item['field_' . $field['id']]; 
        break;
    }
    
    return $value;
  }
  
  public static function render_info_box($entity_id,$item_id,$users_id=false,$exclude_fields_types=true)
  {
    global $current_path, $app_user,$app_users_cache;
    
    $entity_cfg = new entities_cfg($entity_id);
    
    if($users_id>0)
    {
      $user_info = db_find('app_entity_1',$users_id);
      $fields_access_schema = users::get_fields_access_schema($entity_id,$user_info['field_6']);      
    }
    else
    { 
      $fields_access_schema = users::get_fields_access_schema($entity_id,$app_user['group_id']);
    }
           
    $fields_display_rules = array();
    
    $listing_sql_query_select = '';
      
    //prepare forumulas query
    $listing_sql_query_select = fieldtype_formula::prepare_query_select($entity_id, $listing_sql_query_select);
        
    $item_query = db_query("select e.* " . $listing_sql_query_select . " from app_entity_" . $entity_id . " e where id='" . $item_id . "'");
    $item = db_fetch_array($item_query);
    
    $html = '';
        
    
    /**
     * display entity fields
     */
    
    if($exclude_fields_types==true)
    {    	
	    $exclude_fields_types = ",'fieldtype_todo_list','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_attachments','fieldtype_image','fieldtype_related_records','fieldtype_parent_item_id','fieldtype_mapbbcode'";
    }
    else
    {    	
    	$exclude_fields_types = ",'fieldtype_related_records','fieldtype_parent_item_id'";
    }
    
    $hidden_fields_query = '';
    if(strlen($entity_cfg->get('item_page_hidden_fields','')))
    {
    	$hidden_fields_query = " and f.id not in (" . $entity_cfg->get('item_page_hidden_fields') . ")";
    }
                    
    $count = 0;
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {                    
    	$html_fields = '';
    	
      $fields_query = db_query("select f.*, t.name as tab_name, if(f.type in ('fieldtype_id','fieldtype_date_added','fieldtype_created_by'),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action' {$exclude_fields_types} ) {$hidden_fields_query} and (f.is_heading is null or f.is_heading=0) and f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by tab_sort_order, t.name, f.sort_order, f.name",false);
      while($field = db_fetch_array($fields_query))
      {            
        //check field access
        if(isset($fields_access_schema[$field['id']]))
        {
          if($fields_access_schema[$field['id']]=='hide') continue;
        }
        
        //prepare field value
        $value = self::prepare_field_value_by_type($field, $item);                        
        
        $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,
                            'display_user_photo'=>true,
                            'path'=>$current_path);
                            
        $cfg = new fields_types_cfg($field['configuration']);
        
        if(($cfg->get('hide_field_if_empty')==1 and strlen($value)==0) or ($cfg->get('hide_field_if_empty')==1 and in_array($field['type'],array('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_created_by','fieldtype_input_date','fieldtype_input_datetime')) and $value==0))
        {
          continue;
        }
        
        //check fields display rules
        $check_query = db_query("select * from app_forms_fields_rules where fields_id='" . $field['id'] . "'");
        if($check = db_fetch_array($check_query))
        {
        	$fields_display_rules[] = 'app_handle_forms_fields_display_rules(' . $field['id'] . ',"","' . $value . '"); ';
        }
				        
        
        $field_name = fields_types::get_option($field['type'],'name',$field['name']);
        
        if($field['type']=='fieldtype_section')
        {
        	$html_fields .='
            <tr class="form-group-' . $field['id'] . '">
              <th colspan="2" class="section-heading">' . $field_name . '</th>
            </tr>
          ';
        }
        elseif($field['type']=='fieldtype_dropdown_multilevel')
        {
        	$html_fields .= fieldtype_dropdown_multilevel::output_info_box($output_options);
        }
        //hide field name to save space to display value
        elseif($cfg->get('hide_field_name')==1)
        {
          $html_fields .='
            <tr class="form-group-' . $field['id'] . '">                          
              <td colspan="2">' . fields_types::output($output_options). '</td>
            </tr>
          ';
        }
        elseif($field['type']=='fieldtype_users')
        {
        	$html_fields .='
            <tr class="form-group-' . $field['id'] . '">
              <th colspan="2" ' . (strlen($field_name)>25 ? 'class="white-space-normal"':''). '>' .$field_name .'</th>
        	  </tr>
        	  <tr class="form-group-' . $field['id'] . '">            		
              <td colspan="2">' . fields_types::output($output_options). '</td>
            </tr>
          ';
        }
        else
        {         
        	
        	$field_name_html = '';
        	
        	//add dwonload All Attachments link if more then 1 files
        	if($field['type']=='fieldtype_attachments' and count(explode(',',$value))>1)
        	{
        		$field_name_html = '<br><span class="download-all-attachments"><a style="margin-left: 0; font-weight: normal" href="' . url_for('items/info','action=download_all_attachments&id=' . $field['id'] . '&path=' . $current_path). '"><i class="fa fa-download"></i> ' . TEXT_DOWNLOAD_ALL_ATTACHMENTS . '</a></span>';
        	}
        	
          $html_fields .='
            <tr class="form-group-' . $field['id'] . '">            
              <th ' . (strlen($field_name)>25 ? 'class="white-space-normal"':''). '>' . 
              	$field_name . $field_name_html . 
              '</th>
              <td>' . fields_types::output($output_options). '</td>
            </tr>
          ';
        }
      }
      
      //include TAB if there are fields in list
      if(strlen($html_fields))
      {
	      $html .= '
	      <div class="heading"><h4 class="media-heading">' . $tabs['name']. '</h4></div>
	      <div class="table-scrollable">
	      <table class="table table-bordered table-hover table-item-details">
	      		' . $html_fields . '
	      </table></div>';
      }
      
      $count++;
    }
    
    if(count($fields_display_rules))
    {
    	$html .= '<script>' . implode("\n",$fields_display_rules) . '</script>';
    }
    
    return $html;
  }
  
  public static function render_content_box($entity_id,$item_id,$users_id = false)
  {
    global $current_path,$app_user;
    
    if($users_id>0)
    {
      $user_info = db_find('app_entity_1',$users_id);
      $fields_access_schema = users::get_fields_access_schema($entity_id,$user_info['field_6']);      
    }
    else
    {
      $fields_access_schema = users::get_fields_access_schema($entity_id,$app_user['group_id']);
    }
    
            
    $item = db_find('app_entity_' . $entity_id,$item_id);
    
    $html = '';
    $count = 0;

    $html = '';  
    $fields_query = db_query("select f.* from app_fields f, app_forms_tabs t where f.type in ('fieldtype_todo_list','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_attachments','fieldtype_image','fieldtype_mapbbcode') and  f.entities_id='" . db_input($entity_id) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {   
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
            
      $value = $value_original = $item['field_' . $field['id']];
      
      if($field['type']=='fieldtype_attachments' and strlen($value)==0) continue;
      
                       
      $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,                            
                            'path'=>$current_path);
                                                                                    
      $value = fields_types::output($output_options);
    
      if(strlen($value)>0)
      {
        $field_name_html = '';
        
        //add dwonload All Attachments link if more then 1 files
        if($field['type']=='fieldtype_attachments' and count(explode(',',$value_original))>1)
        {
          $field_name_html = '<span class="download-all-attachments"><a href="' . url_for('items/info','action=download_all_attachments&id=' . $field['id'] . '&path=' . $current_path). '"><i class="fa fa-download"></i> ' . TEXT_DOWNLOAD_ALL_ATTACHMENTS . '</a></span>';
        }
        
        $html .='
          <div class="content_box_heading"><h4 class="media-heading">' . $field['name']  . $field_name_html .  '</h4></div>
          <div class="content_box_content ' . $field['type'] . '">' . $value . '</div>
        ';
      }
    }
        

    
    return $html;
  }  
  
  public static function get_fields_values_cache($fields_cache,$path_array,$current_entity_id)
  {
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      $entity_id = $vv[0];
      $item_id = (isset($vv[1]) ? $vv[1] : 0);             
      
      if($item_id==0 or $current_entity_id==$entity_id) break;
            
      $item_info = db_find('app_entity_' . $entity_id,$item_id);
      
      $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($entity_id) . "' order by f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {
        $fields_cache[$field['id']] = $item_info['field_' . $field['id']];
      }
           
    }
    
    return $fields_cache;
  }
  
  public static function get_path_info($entities_id, $items_id, $current_item_info = false)
  {    
  	$path_array = items::get_path_array($entities_id,$items_id, array(), $current_item_info);
        
    $path_array = array_reverse($path_array);
    
    $cout = 0;
    $paent_path_list = array();
    $path_list = array();
    $name_list = array();
    $path_to_entity = array();
    foreach($path_array as $v)
    {
      $path_list[] = $v['path'];
      
      
      if($cout!=(count($path_array)-1))
      { 
        $paent_path_list[] = $v['path'];
        $name_list[] = $v['name'];
      }
      
      if($cout==(count($path_array)-1))
      {
        $last = explode('-',$v['path']);
        $path_to_entity[] = $last[0];
      }
      else
      {
        $path_to_entity[] = $v['path'];
      }
      
      $cout++;
    }
    
    return array('parent_name'=>implode('<br>',$name_list),
                 'parent_path'=>implode('/',$paent_path_list),
                 'full_path'=>implode('/',$path_list),
                 'path_to_entity'=> implode('/',$path_to_entity),
                );
    
    //print_r($path_array);
  }
  
  public static function get_path_array($entities_id, $items_id,$path_array = array(),$current_item_info = false)
  {
  	global $app_entities_cache, $items_holder;
  	    
    $entities = $app_entities_cache[$entities_id];
        	
    if(!isset($items_holder[$entities_id][$items_id]))
    {	
    	if($current_item_info)
    	{
    		$items = $items_holder[$entities_id][$items_id] = $current_item_info;
    	}
    	else
    	{	
    		$items_query = db_query("select * from app_entity_" . $entities_id . " where id='" . $items_id . "'");
    		$items = $items_holder[$entities_id][$items_id] = db_fetch_array($items_query);
    	}
    }
    else 
    {
    	$items = $items_holder[$entities_id][$items_id];
    }
    
    if($heading_field_id = fields::get_heading_id($entities_id))
    {      
      $name = self::get_heading_field_value($heading_field_id,$items);      
    }
    else
    {
      $name = $items['id'];
    }   
             
    $path_array[] = array('path'=>$entities_id . '-' . $items_id,'name'=>$name);
            
    if($entities['parent_id']>0)
    {                         
      $path_array = items::get_path_array($entities['parent_id'],$items['parent_item_id'],$path_array);
    }
            
    return $path_array;
  }
  
  public static function parse_path($path)
  {
    $path_array = explode('/',$path);
    $item_array = explode('-',$path_array[count($path_array)-1]);
    
    $entity_id = $item_array[0];
    $item_id = (isset($item_array[1]) ? $item_array[1] : 0);
    
    if(count($path_array)>1)
    {
      $v = explode('-',$path_array[count($path_array)-2]);
      $parent_entity_id = $v[0];
      $parent_entity_item_id = $v[1];
    }
    else
    {
      $parent_entity_id = 0;
      $parent_entity_item_id = 0;
    }
    
    return array('entity_id' => $entity_id,
                 'item_id' => $item_id,
                 'path_array' => $path_array,
                 'parent_entity_id' => $parent_entity_id,
                 'parent_entity_item_id' => $parent_entity_item_id);
  }
  
  public static function get_paretn_entity_id_by_path($path)
  {
    $entity_id = 0;
    $path_array = explode('/',$path);
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $entity_id = $vv[0];                        
      }
    }
    
    return $entity_id;
  }
  
  public static function get_paretn_entity_item_id_by_path($path)
  {
    $item_id = 0;
    $path_array = explode('/',$path);
    foreach($path_array as $v)
    {
      $vv = explode('-',$v);
      
      $count = db_count('app_entities',$vv[0], 'parent_id');
            
      if($count>0 and isset($vv[1]))
      {
        $item_id = $vv[1];                        
      }
    }
    
    return $item_id;
  }
  
  public static function get_sub_entities_list_by_path($path)
  {   
    global $app_user;
    
    $parent_id = items::get_paretn_entity_id_by_path($path);
    
    $list = array();
    
    if($parent_id>0)
    {
      if($app_user['group_id']==0)
      {
        $entities_query = db_query("select e.* from app_entities e where parent_id='" . db_input($parent_id) . "' order by e.sort_order, e.name");
      }
      else
      {
        $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.parent_id = '" . db_input($parent_id) . "' order by e.sort_order, e.name");
      }
      
      
      while($entities = db_fetch_array($entities_query))
      {
        $list[] = $entities['id'];      
      }
    }
    
    return $list;
  }
  
  public static function add_access_query_for_parent_entities($entities_id,$listing_sql_query='')
  {
    global $app_user, $app_entities_cache;
    
    if($app_user['group_id']==0) return '';
        
    $entity_info  = $app_entities_cache[$entities_id];
            
    if($entity_info['parent_id']>0)
    {      
      $listing_sql_query = ' and e.parent_item_id in (select e.id from app_entity_' . $entity_info['parent_id']. ' e where e.id>0 '  . items::add_access_query($entity_info['parent_id'],'') . ' ' . items::add_access_query_for_parent_entities($entity_info['parent_id']) . ')'; 	
    } 
    
    return $listing_sql_query;
  }
  
  //check users tree access for entity 1
  public static function add_access_query_for_user_parent_entities($entities_id,$listing_sql_query='')
  {
  	global $app_user, $app_entities_cache;
  
  	if($app_user['group_id']==0) return '';
    	
  	$entity_info  = $app_entities_cache[$entities_id];
  
  	if($entity_info['parent_id']>0)
  	{
  		$listing_sql_query = ' and e.parent_item_id in (select e.id from app_entity_' . $entity_info['parent_id']. ' e where '  . ($entity_info['parent_id']==1 ? 'e.id=' . $app_user['id'] :'e.id>0') . ' ' . items::add_access_query_for_user_parent_entities($entity_info['parent_id']) . ')';
  	}
  
  	return $listing_sql_query;
  }
  
  public static function add_access_query($current_entity_id,$listing_sql_query, $force_access_query = false)
  {
    global $app_user, $current_path_array;
          
    $access_schema = users::get_entities_access_schema($current_entity_id,$app_user['group_id']);
    
    //get users entiteis tree
    $users_entities_tree = entities::get_tree(1);
    
    //get users entities id list
    $users_entities = array();
    foreach($users_entities_tree as $v)
    {
    	$users_entities[] = $v['id'];
    }
        
    //force check users entities tree access
    if(in_array($current_entity_id,$users_entities) and users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0)
    {
    	$listing_sql_query = self::add_access_query_for_user_parent_entities($current_entity_id);
    	
    	/*echo '<pre>';
    	print_r($users_entities);
    	print_r($listing_sql_query);
    	exit();*/
    }
    elseif((users::has_access('view_assigned',$access_schema) and $app_user['group_id']>0) or $force_access_query)
    {                    
      $users_fields = array(); 
      $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_users') and  f.entities_id='" . db_input($current_entity_id) . "'");
      while($fields = db_fetch_array($fields_query))
      {    
        $users_fields[] = $fields['id'];
      }
      
      $grouped_users_fields = array(); 
      $fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_grouped_users') and  f.entities_id='" . db_input($current_entity_id) . "'");
      while($fields = db_fetch_array($fields_query))
      {    
        $grouped_users_fields[] = $fields['id'];
      }
      
      //if exist fields then check access by fields + created_by
      if(count($users_fields)>0 or count($grouped_users_fields)>0)
      {
        $sql_query_array = array();
        foreach($users_fields as $id)
        {                    
          $sql_query_array[] = "(select count(*) as total from app_entity_" .$current_entity_id . "_values cv where  cv.items_id=e.id and cv.fields_id='" . $id . "' and cv.value='" . $app_user['id'] . "')>0";        	        	        	
        }
        
        foreach($grouped_users_fields as $id)
        {        
          $sql_query_array[] = "(select count(*) as total from app_entity_" .$current_entity_id . "_values cv where cv.items_id=e.id and cv.fields_id='" . $id . "' and cv.value in (select id from app_fields_choices fc where fc.fields_id='" . $id . "' and find_in_set(" . $app_user['id']  . ",fc.users)))>0";                  	        	        	        
        }
        
        $sql_query_array[] = "e.created_by='" . $app_user['id'] . "'";
        
        $listing_sql_query .= " and (" . implode(' or ', $sql_query_array). ") ";
      }
      else
      {
      	//check access in created_by only 
      	$listing_sql_query .= " and e.created_by='" . $app_user['id'] . "'";
      }
    }
    
    return $listing_sql_query;     
  
  }
  
  public static function add_listing_order_query_by_entity_id($entities_id,$order_cause='asc',$alias='e')
  {
    $listing_order_query = " order by ";
    
    //if entity is Users then order by firstname/lastname
    if($entities_id==1)
    {      
      $listing_order_query .= (CFG_APP_DISPLAY_USER_NAME_ORDER=='firstname_lastname' ? "{$alias}.field_7 {$order_cause}, {$alias}.field_8 {$order_cause}" : "{$alias}.field_8 {$order_cause}, {$alias}.field_7 {$order_cause}") ;
    }       
    //if exist haeading field then order by heading  
    elseif($heading_id = fields::get_heading_id($entities_id))
    { 
    	$field_info = db_find('app_fields',$heading_id);
    	
    	switch($field_info['type'])
    	{
    		case 'fieldtype_id':
    			$listing_order_query .= "{$alias}.id"  . ' ' . $order_cause;
    			break;
    		case 'fieldtype_date_added':
    			$listing_order_query .= "{$alias}.date_added"  . ' ' . $order_cause;
    			break;
    		case 'fieldtype_created_by':
    			$listing_order_query .= "{$alias}.created_by"  . ' ' . $order_cause;
    			break;
    		default:
    			$listing_order_query .= "{$alias}.field_{$heading_id} " . $order_cause;
    			break;
    	}
      
    }
    //default order by ID
    else
    {
      $listing_order_query .= "{$alias}.id"  . ' ' . $order_cause;
    }
    
    return $listing_order_query;
  }
  
  public static function check_unique($entities_id,$fields_id,$fields_value,$items_id = false)
  {  	
  	$field_info = db_find('app_fields',$fields_id);
  		
  	switch($field_info['type'])
  	{
  		case 'fieldtype_input_date':
  			$fields_value = get_date_timestamp($fields_value);
  			break;
  	}
  		
  	$check_query = db_query("select count(*) as total from app_entity_" . $entities_id .  " where field_" . $field_info['id'] . "='" . db_input($fields_value)  . "'" . ($items_id ? " and id!='" . db_input($items_id) . "'":""));
  	$check = db_fetch_array($check_query);
  		
  	return (int)$check['total'];
  }
  
  public static function get_send_to($entity_id, $item_id, $item=false)
  {
  	
  	if(!$item)
  	{
  		$item = db_find('app_entity_' . $entity_id,$item_id);
  	}
  	
  	//start build $send_to array
  	$send_to = array();
  	
  	//add assigned users to notification
  	$fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_grouped_users','fieldtype_users') and  f.entities_id='" . db_input($entity_id) . "' ");
  	while($field = db_fetch_array($fields_query))
  	{
  		$cfg = new fields_types_cfg($field['configuration']);
  		 
  		//skip fields with disabled notification
  		if($cfg->get('disable_notification')==1) continue;
  		 
  		$field_value = $item['field_' . $field['id']];
  		 
  		switch($field['type'])
  		{
  			case 'fieldtype_grouped_users':
  				if(strlen($field_value)>0)
  				{
  					foreach(explode(',',$field_value) as $choices_id)
  					{
  						$choice_query = db_query("select * from app_fields_choices where id='" . db_input($choices_id) . "'");
  						if($choice = db_fetch_array($choice_query))
  						{
  							foreach(explode(',',$choice['users']) as $id)
  							{
  								$send_to[] = $id;
  							}
  						}
  					}
  				}
  				break;
  			case 'fieldtype_users':
  				if(strlen($field_value)>0)
  				{
  					$send_to = array_merge($send_to,explode(',',$field_value));
  				}
  				break;
  		}
  	}
  	
  	$send_to = array_filter($send_to);
  	
  	return $send_to;
  }
  
  public static function send_new_item_nofitication($current_entity_id, $item_id, $app_send_to = false)
  {
  	
  	if(!$app_send_to)
  	{
  		$app_send_to = items::get_send_to($current_entity_id, $item_id);
  	}
  	  	
  	//sending sms
  	$modules = new modules('sms');
  	$sms = new sms($current_entity_id, $item_id);
  	$sms->send_to = $app_send_to;
  	$sms->send_insert_msg();
  	
  	
  	$breadcrumb = items::get_breadcrumb_by_item_id($current_entity_id, $item_id);
  	$item_name = $breadcrumb['text'];
  	
  	$entity_cfg = new entities_cfg($current_entity_id);
  	
  	//subject for new item
  	$subject = (strlen($entity_cfg->get('email_subject_new_item'))>0 ? $entity_cfg->get('email_subject_new_item') . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM . ' ' . $item_name);
  		
  	 
  	//Send notification if there are assigned users and items is new or there is changed fields or new assigned users
  	if(count($app_send_to)>0)
  	{
  		$users_notifications_type = 'new_item';
  			
  		//default email heading
  		$heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $current_entity_id . '-' . $item_id,true) . '"><h3>' . $subject . '</h3></a></div>','email_heading_content');
  	
  		//start sending email
  		foreach(array_unique($app_send_to) as $send_to)
  		{
  			//prepare body
  			//prepare body
  			if($entity_cfg->get('item_page_details_columns','2')==1)
  			{
  				$body = users::use_email_pattern('single_column',array('email_single_column'=>items::render_info_box($current_entity_id,$item_id,$send_to, false)));
  			}
  			else
  			{
  				$body = users::use_email_pattern('single',array('email_body_content'=>items::render_content_box($current_entity_id,$item_id,$send_to),'email_sidebar_content'=>items::render_info_box($current_entity_id,$item_id,$send_to)));
  			}
  	
  			//echo $subject . $body;
  			//exit();
  	
  	
  			if(users_cfg::get_value_by_users_id($send_to, 'disable_notification')!=1)
  			{
  				users::send_to(array($send_to),$subject,$heading . $body);
  			}
  				
  			//add users notification
  			users_notifications::add($subject, $users_notifications_type, $send_to, $current_entity_id, $item_id);
  	
  		}
  	}
  	
  	return $subject;  	
  }
    
  
}