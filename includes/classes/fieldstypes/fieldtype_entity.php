<?php

class fieldtype_entity
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_ENTITY_TITLE);
  }
  
  function get_configuration($params = array())
  {
  
    $cfg = array();
    $cfg[] = array('title'=>TEXT_SELECT_ENTITY, 
                   'name'=>'entity_id',
                   'tooltip'=>TEXT_FIELDTYPE_ENTITY_SELECT_ENTITY_TOOLTIP,
                   'type'=>'dropdown',
                   'choices'=>entities::get_choices(),
                   'params'=>array('class'=>'form-control input-medium'));
                                  
    $cfg[] = array('title'=>TEXT_DISPLAY_USERS_AS, 
                   'name'=>'display_as',
                   'tooltip'=>TEXT_DISPLAY_USERS_AS_TOOLTIP,
                   'type'=>'dropdown',
                   'choices'=>array('dropdown'=>TEXT_DISPLAY_USERS_AS_DROPDOWN,'checkboxes'=>TEXT_DISPLAY_USERS_AS_CHECKBOXES,'dropdown_muliple'=>TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE),
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_DEFAULT_TEXT,
    		'name'=>'default_text',
    		'type'=>'input',
    		'tooltip'=>TEXT_DEFAULT_TEXT_INFO,
    		'params'=>array('class'=>'form-control input-medium'));
                   
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
                   
    $cfg[] = array('title'=>tooltip_icon(TEXT_DISPLAY_NAME_AS_LINK_INFO) . TEXT_DISPLAY_NAME_AS_LINK, 'name'=>'display_as_link','type'=>'checkbox');                   
                   
    $cfg[] = array('name'=>'fields_in_popup','type'=>'hidden');                       
    
    return $cfg;
  }
  
  static function prepare_parents_sql($parent_entity_item_id, $entity_id, $field_entity_id,$listing_sql_query='', $previous_prefix='e')
  {  	
  	//set prefix for current entity
  	$prefix = 'e' . $entity_id;
  	
  	//get entity info
  	$entity_info = db_find('app_entities',$entity_id);
  	
  	//if paretn is 0 then it means we did not find $field_entity_id in this tree branch
  	//and we don't have to check parents so that is why we return empyt query
  	if($entity_info['parent_id']==0) return '';
  	
  	//check parents the same
  	if($entity_info['parent_id']==$field_entity_id)
  	{
  		$listing_sql_query .= " and {$previous_prefix}.parent_item_id in (select {$prefix}.id from app_entity_" . $entity_id . " {$prefix} where {$prefix}.parent_item_id='" . db_input($parent_entity_item_id) . "')";
  	}
  	//if parents not the same then wer include sub-query
  	else 
  	{
  		$listing_sql_query .= " and {$previous_prefix}.parent_item_id in (select {$prefix}.id from app_entity_" . $entity_id . " {$prefix} where {$prefix}.id>0 " . self::prepare_parents_sql($parent_entity_item_id, $entity_info['parent_id'], $field_entity_id, $listing_sql_query,$prefix) . ")";
  	}
  	
  	return $listing_sql_query;
  }
  
  function render($field,$obj,$params = array())
  {
    global $app_users_cache, $current_path_array, $app_layout, $app_action; 
           
    $parent_entity_item_id = $params['parent_entity_item_id'];
    
    $cfg = new fields_types_cfg($field['configuration']);
    
    $entity_info = db_find('app_entities',$cfg->get('entity_id'));
    $field_entity_info = db_find('app_entities',$field['entities_id']);
                                      
    $choices = array();
    
    //add empty value if dispalys as dropdown and field is not requireed
    if($cfg->get('display_as')=='dropdown')
    {    	
      $choices[''] = (strlen($cfg->get('default_text')) ? $cfg->get('default_text') : TEXT_NONE);  
    }
    
    $listing_sql_query = 'e.id>0 ';
    $listing_sql_query_order = '';
    $listing_sql_query_join = '';
    
    $parent_entity_item_is_the_same = false;
    
    //if parent entity is the same then select records from paretn items only
    if($parent_entity_item_id>0 and $entity_info['parent_id']>0 and $entity_info['parent_id']==$field_entity_info['parent_id'])
    {
    	$parent_entity_item_is_the_same = true;
    	
      $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
    }
    //if paretn is different then check level branch 
    elseif($parent_entity_item_id>0 and $entity_info['parent_id']>0 and $entity_info['parent_id']!=$field_entity_info['parent_id']) 
    {
    	$listing_sql_query = $listing_sql_query . self::prepare_parents_sql($parent_entity_item_id,$entity_info['parent_id'],$field_entity_info['parent_id']);    	
    }
              
    $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($cfg->get('entity_id')). "' and reports_type='entityfield" . $field['id'] . "'");
    if($default_reports = db_fetch_array($default_reports_query))
    {      
      $listing_sql_query = reports::add_filters_query($default_reports['id'],$listing_sql_query);
      
      $info = reports::add_order_query($default_reports['listing_order_fields'],$cfg->get('entity_id'));
      $listing_sql_query_order .= $info['listing_sql_query'];
      $listing_sql_query_join .= $info['listing_sql_query_join'];
      
    }
    else
    {
      $listing_sql_query_order .= " order by e.id";
    }
    
    //if exist value then include it in query 
    $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : '');    
    if(strlen($value))
    {
    	$listing_sql_query = "(" . $listing_sql_query . ") or e.id in (" . $value . ") ";
    }
    
    $field_heading_id = 0;
    $fields_query = db_query("select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input($cfg->get('entity_id')) . "'");
    if($fields = db_fetch_array($fields_query))
    {
      $field_heading_id = $fields['id'];
    }
                    
    $listing_sql = "select  e.* from app_entity_" . $cfg->get('entity_id') . " e "  . $listing_sql_query_join . " where " . $listing_sql_query . $listing_sql_query_order;
    $items_query = db_query($listing_sql);
    while($item = db_fetch_array($items_query))
    {
      if($cfg->get('entity_id')==1)
      {
        $choices[$item['id']] = $app_users_cache[$item['id']]['name'];
      }
      elseif($field_heading_id>0)
      {
        //add paretn item name if exist
        $parent_name = '';
        if($entity_info['parent_id']>0 and $entity_info['parent_id']!=$field_entity_info['parent_id'])
        {
          $parent_name = items::get_heading_field($entity_info['parent_id'],$item['parent_item_id']) . ' > ';
        }
        
        $choices[$item['id']] = $parent_name . items::get_heading_field_value($field_heading_id,$item);
      }
      else
      {
        $choices[$item['id']] = $item['id'];
      } 
    }
    
    //echo '<pre>';
    //print_r($cfg);
    
    
    //prepare button add
    $button_add_html = '';
    if(isset($current_path_array) and $app_action!='account' and $app_action!='processes' and $app_layout!='public_layout.php' and users::has_access_to_entity($cfg->get('entity_id'),'create') and $cfg->get('entity_id')!=1 and !isset($_GET['is_submodal']) and ($entity_info['parent_id']==0 or ($entity_info['parent_id']>0 and $parent_entity_item_is_the_same)))
    {
    	$url_params = 'is_submodal=true&redirect_to=parent_modal&refresh_field=' . $field['id'];
    	 
    	if($entity_info['parent_id']==0)
    	{
    		$url_params .= '&path=' . $cfg->get('entity_id');
    	}
    	else
    	{
    		$path_array = $current_path_array;
    		unset($path_array[count($path_array)-1]);
    
    		$url_params .= '&path=' . implode('/',$path_array) . '/' . $cfg->get('entity_id');
    	}
    	 
    	$submodal_url = url_for('items/form',$url_params);
    	 
    	$button_add_html = '<button type="button" class="btn btn-default btn-submodal-open btn-submodal-open-chosen" data-parent-entity-item-id="' . $parent_entity_item_id . '" data-field-id="' . $field['id'] . '" data-submodal-url="' . $submodal_url . '"><i class="fa fa-plus" aria-hidden="true"></i></button>';
    } 
    
    //set value    
    $value = (strlen($obj['field_' . $field['id']]) ? $obj['field_' . $field['id']] : ''); 
    
    if($cfg->get('display_as')=='dropdown')
    {    	    	
      $attributes = array('class'=>'form-control chosen-select ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
                 
      return '<table><tr><td>' . select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes)  . '</td><td>' . $button_add_html . '</td></tr></table>';
    }
    elseif($cfg->get('display_as')=='checkboxes')
    {
      $attributes = array('class'=>'field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));
      
      return '<div class="checkboxes_list ' . ($field['is_required']==1 ? ' required':'') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes) . '</div>';
    }
    elseif($cfg->get('display_as')=='dropdown_muliple')
    {
      $attributes = array('class'=>'form-control chosen-select ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
                          'multiple'=>'multiple',
                          'data-placeholder'=>(strlen($cfg->get('default_text')) ? $cfg->get('default_text') : TEXT_SELECT_SOME_VALUES));
      
      return '<table><tr><td>' .select_tag('fields[' . $field['id'] . '][]',$choices,explode(',',$value),$attributes) . '</td><td valign="top">' . $button_add_html . '</td></tr></table>';
    }
  }
  
  function process($options)
  {          	
    return (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
  }
  
  function output($options)
  {
    global $app_user;
    
    if(strlen($options['value'])==0)
    {
      return '';
    }
                
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    //prepare sql if not export
    $items_info_formula_sql = '';    
    if(!isset($options['is_export']))
    {
    	$fields_access_schema = users::get_fields_access_schema($cfg->get('entity_id'),$app_user['group_id']);
    	
    	$fields_in_listing = fields::get_heading_id($cfg->get('entity_id')) . (strlen($cfg->get('fields_in_popup')) ? ','. $cfg->get('fields_in_popup') : ''); 
    	$items_info_formula_sql = fieldtype_formula::prepare_query_select($cfg->get('entity_id'), '',false,array('fields_in_listing'=>$fields_in_listing));
    }
    
    $output = array();
    foreach(explode(',',$options['value']) as $item_id)
    {
      $items_info_sql = "select e.* {$items_info_formula_sql} from app_entity_" . $cfg->get('entity_id') . " e where e.id='" . db_input($item_id). "'";
      $items_query = db_query($items_info_sql);
      if($item = db_fetch_array($items_query))
      {
        $name = items::get_heading_field($cfg->get('entity_id'),$item['id']);
        
        //get fields in popup in not export
        if(!isset($options['is_export']))
        {
	        $fields_in_popup = fields::get_items_fields_data_by_id($item,$cfg->get('fields_in_popup'),$cfg->get('entity_id'),$fields_access_schema);
	        $popup_html = '';
	        if(count($fields_in_popup)>0)
	        {
	          $popup_html = app_render_fields_popup_html($fields_in_popup);
	          
	          $name = '<span ' . $popup_html . '>' . $name . '</span>'; 
	        }
	        
	        if($cfg->get('display_as_link')==1)
	        {
	          $path_info = items::get_path_info($cfg->get('entity_id'),$item['id']);
	          
	          $name = '<a href="' . url_for('items/info', 'path=' . $path_info['full_path']) . '">' . $name . '</a>';
	        }
        }
        
        $output[] = $name;        
      }
    } 
    
    
    if(isset($options['is_export']))
    {
      return implode(', ',$output);
    }
    else
    {
      return implode('<br>',$output);
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