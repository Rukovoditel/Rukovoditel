<?php

class entities
{
	public static function has_subentities($entities_id)
	{
		return db_count('app_entities',$entities_id,'parent_id');
	}
	
  public static function delete($id)
  {
    $fields_query = db_fetch_all('app_fields',"entities_id='" . db_input($id). "'");
    while($fields = db_fetch_array($fields_query))
    {
      db_delete_row('app_fields',$fields['id']);  
      db_delete_row('app_fields_choices',$fields['id'],'fields_id');
    }
    
    db_delete_row('app_forms_tabs',$id,'entities_id');
    db_delete_row('app_entities_configuration',$id,'entities_id');
    db_delete_row('app_entities',$id);
    
    $reports_query = db_query("select * from app_reports where entities_id='" . $id . "'");
    while($v = db_fetch_array($reports_query))
    {
      db_delete_row('app_reports_filters',$v['id'],'reports_id');
    }
    
    db_delete_row('app_reports',$id,'entities_id');
    
    //delete notifications
    db_query("delete from app_users_notifications where entities_id='" . $id . "'");
    
    //delete timers
    if(class_exists('timer'))
    {
    	db_query("delete from app_ext_timer where entities_id='" . $id . "'");
    }
  }
  
  public static function insert_default_form_tab($id)
  {
    $sql_data = array('name'=>TEXT_INFO,'entities_id'=>$id);
    
    db_perform('app_forms_tabs',$sql_data);
    
    return db_insert_id();
  }
  
  public static function insert_reserved_fields($id,$forms_tabs_id)
  {
    $sort_order = 0;
    foreach(fields_types::get_reserved_types() as $type)
    {
      $sql_data = array('forms_tabs_id'=>$forms_tabs_id,
                        'entities_id'=>$id,
                        'name'=>'',
                        'listing_status'=>1, 
                        'sort_order'=>$sort_order,
                        'listing_sort_order'=>$sort_order,                       
                        'type'=>$type);
      db_perform('app_fields',$sql_data);
      
      $sort_order++;
    }
  }
  
  public static function get_listing_heading($entities_id)
  {
    $cfg = entities::get_cfg($entities_id);
    
    if(strlen($cfg['listing_heading'])>0)
    {
      return $cfg['listing_heading'];
    }
    else
    {
      return entities::get_name_by_id($entities_id);
    } 
  }
  
  public static function set_cfg($k,$v,$entities_id)
  {
    $cfq_query = db_query("select * from app_entities_configuration where configuration_name='" . db_input($k) . "' and entities_id='" . db_input($entities_id) . "'");
    if(!$cfq = db_fetch_array($cfq_query))
    {
      db_perform('app_entities_configuration',array('configuration_value'=>$v,'configuration_name'=>$k,'entities_id'=>$entities_id));
    }
    else
    {
      db_perform('app_entities_configuration',array('configuration_value'=>$v),'update',"configuration_name='" . db_input($k) . "' and entities_id='" . db_input($entities_id) . "'");
    }  
  }
  
  public static function get_cfg($id)
  {
    $cfg = array();
    $info_query = db_fetch_all('app_entities_configuration',"entities_id='" . db_input($id). "'");
    while($info = db_fetch_array($info_query))
    {
      $cfg[$info['configuration_name']] = $info['configuration_value'];
    }
    
    $cfg_keys = array('menu_title',
                      'menu_icon',
                      'listing_heading',
                      'window_heading',
                      'insert_button',
                      'use_editor_in_comments',
                      'use_comments',
                      'email_subject_new_item',
                      'email_subject_updated_item',
                      'email_subject_new_comment',
                      'number_fixed_field_in_listing');
    
    foreach($cfg_keys as $k)
    {
      if(!isset($cfg[$k]))
      { 
        $cfg[$k]='';                
      }
    }
    
    return $cfg;
  }
  
  public static function check_before_delete($id)
  {
    $msg = '';
    $name = entities::get_name_by_id($id);    
    
    //check if entity is Users
    if($id==1)
    {
      $msg = sprintf(TEXT_WARN_DELETE_ENTITY_USERS,$name);
    } 
    //check if there are sub entities
    elseif(db_count('app_entities',$id,'parent_id')>0)
    {
      $msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_PARENT,$name);
    }    
    //chec if there is items
    elseif(db_count('app_entity_' . $id)>0)
    {
      $msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_ITEMS,$name);
    }
    //check if there are relationship with other entities
    else
    {
    	$relationship = array();
    	$fields_query = db_query("select * from app_fields where entities_id!='" . db_input($id). "' and type in ('fieldtype_entity','fieldtype_related_records')");
    	while($fields = db_fetch_array($fields_query))
    	{
    		$cfg = new fields_types_cfg($fields['configuration']);
    		if($cfg->get('entity_id')==$id)
    		{
    			$relationship[] = entities::get_name_by_id($fields['entities_id']) . ': ' . $fields['name'];
    		}
    	}
    	    	    
    	if(count($relationship)>0)
    	{
    		if(!defined(TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP))
    		{
    			define('TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP','You can\'t delete entity <b>%s</b> because it has relationship with: <br>%s.<br><br>Delete all fields which related to this entity.');
    		}
    			
    		$msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP,$name, implode('<br>',$relationship));
    	}
    }	
    
    return $msg;
  }
    
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_entities',$id);
    
    return $obj['name'];
  }
  
  public static function get_name_cache()
  {
    $cache = array();
    $entities_query = db_query("select * from app_entities");
    while($entities = db_fetch_array($entities_query))
    {
      $cache[$entities['id']] = $entities['name']; 
    }
    
    return $cache;
  }
  
  
  public static function get_choices_with_empty($empty_text = TEXT_VIEW_ALL)
  {
    $choices = self::get_choices();
    $choices = array('0'=>$empty_text)+$choices;
    
    return $choices;
  }
  
  
  public static function get_choices()
  {
    $choices = array();
    
    foreach(entities::get_tree() as $v)
    {
      $choices[$v['id']] = str_repeat('- ', $v['level']) . $v['name'];
    }
    
    return $choices;
  }
  
  
  public static function get_tree($parent_id=0,$tree=array(),$level=0,$path = array())
  {
    global $app_user;
      
    if($app_user['group_id']==0)
    {
      $entities_query = db_query("select * from app_entities where parent_id='" . $parent_id . "' order by sort_order, name");
    }
    else
    {      
      $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.parent_id='" . $parent_id . "' and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' order by e.sort_order, e.name");
    }
    
    while($entities = db_fetch_array($entities_query))
    {
      $tree[] = array('id'=>$entities['id'],
                      'name'=>$entities['name'],
      								'notes'=>$entities['notes'],	
                      'sort_order'=>$entities['sort_order'],  
                      'level'=>$level,
                      'path'=>$path,
                      );
                      
      $tree = entities::get_tree($entities['id'],$tree,$level+1,array_merge($path,array($entities['id'])));
    }
    
    return $tree;
  }
  
  public static function get_parents($entities_id, $parents = array())
  {
    $entities_query = db_query("select * from app_entities where id='" . $entities_id . "'");
    if($entities = db_fetch_array($entities_query))
    {
      if($entities['parent_id']>0)
      {
        $parents[] = $entities['parent_id'];
        
        $parents = self::get_parents($entities['parent_id'],$parents);
      }
    }
    
    return $parents;
  }
  
  public static function prepare_tables($entities_id)
  {
    $sql = '
      CREATE TABLE IF NOT EXISTS app_entity_' . (int)$entities_id . ' (
        id int(11) NOT NULL auto_increment,
        parent_id int(11) default 0,
        parent_item_id int(11) default 0,
        linked_id int(11) default 0,
        date_added int(11) NOT NULL,
        created_by int(11) default NULL,
        sort_order int(11) default 0,
        PRIMARY KEY  (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ';
    
    db_query($sql);
    
    $sql = 'ALTER TABLE app_entity_' . (int)$entities_id . ' ADD INDEX idx_parent_id (parent_id);';
    db_query($sql);
    
    $sql = 'ALTER TABLE app_entity_' . (int)$entities_id . ' ADD INDEX idx_parent_item_id (parent_item_id);';
    db_query($sql);
    
    $sql = 'ALTER TABLE app_entity_' . (int)$entities_id . ' ADD INDEX idx_created_by (created_by);';
    db_query($sql);
    
    $sql = '
    		CREATE TABLE IF NOT EXISTS app_entity_' . (int)$entities_id . '_values (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  items_id int(11) NOT NULL,
				  fields_id int(11) NOT NULL,
				  value int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `idx_items_id` (`items_id`),
				  KEY `idx_fields_id` (`fields_id`),
    			KEY `idx_items_fields_id` (`items_id`,`fields_id`),
    			KEY `idx_value_id` (`value`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    	';
    
    db_query($sql);
    
  }
  
  public static function delete_tables($entities_id)
  {
    $sql = 'DROP TABLE IF EXISTS app_entity_' . (int)$entities_id;
    db_query($sql);
    
    $sql = 'DROP TABLE IF EXISTS app_entity_' . (int)$entities_id . '_values';
    db_query($sql);
    
  }
  
  public static function prepare_field_type($type)
  {
  	switch($type)
  	{
  		case 'fieldtype_input_numeric':
  		case 'fieldtype_input_numeric_comments':
  			$db_type = 'VARCHAR(64)';
  			break;
  		case 'fieldtype_boolean':
  			$db_type = 'VARCHAR(8)';
  			break;
  		case 'fieldtype_input_date':
  		case 'fieldtype_input_datetime':
  		case 'fieldtype_dropdown':
  		case 'fieldtype_radioboxes':
  		case 'fieldtype_progress':
  			$db_type = 'INT(11)';
  			break;
  		case 'fieldtype_input_vpic':
  		case 'fieldtype_barcode':
  		case 'fieldtype_image':
  		case 'fieldtype_input_file':
  			$db_type = 'VARCHAR(255)';
  			break;
  		case 'fieldtype_text_pattern':	
  		case 'fieldtype_related_records':
  		case 'fieldtype_formula':
  		case 'fieldtype_qrcode':
  			$db_type = 'VARCHAR(1)';
  			break;
  		default:
  			$db_type = 'TEXT';
  			break;
  	}
  	
  	return $db_type;
  }
  
  public static function prepare_field($entities_id,$fields_id,$type)
  {  	
  	$db_type = self::prepare_field_type($type);
    $sql = 'ALTER TABLE  app_entity_' . (int)$entities_id . ' ADD  field_' . (int)$fields_id . ' ' . $db_type . ' NOT NULL';
    db_query($sql);
  }     
  
  public static function delete_field($entities_id,$fields_id)
  {
    $sql = 'ALTER TABLE app_entity_' . (int)$entities_id  . ' DROP field_' . (int)$fields_id;
    db_query($sql);
  }
  
  
}