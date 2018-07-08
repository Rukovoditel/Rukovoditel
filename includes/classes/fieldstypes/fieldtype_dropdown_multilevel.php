<?php

class fieldtype_dropdown_multilevel
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_DISPLAY_CHOICES_VALUES, 'name'=>'display_choices_values','type'=>'checkbox','tooltip_icon'=>TEXT_DISPLAY_CHOICES_VALUES_TIP);
    
    //cfg global list if exist
    if(count($choices = global_lists::get_lists_choices())>0)
    {
    	$cfg[] = array('title'=>TEXT_USE_GLOBAL_LIST,
    			'name'=>'use_global_list',
    			'type'=>'dropdown',
    			'choices'=>$choices,
    			'tooltip'=>TEXT_USE_GLOBAL_LIST_TOOLTIP,
    			'params'=>array('class'=>'form-control input-medium'));
    }
        
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip_icon'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_USE_SEARCH, 
                   'name'=>'use_search',
                   'type'=>'dropdown',
                   'choices'=>array('0'=>TEXT_NO,'1'=>TEXT_YES),
                   'tooltip'=>TEXT_USE_SEARCH_INFO,
                   'params'=>array('class'=>'form-control input-small'));
    
    $cfg[] = array('title'=>TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_VALUE_DISPLAY,
    		'name'=>'value_displya_own_column',
    		'type'=>'dropdown',
    		'choices'=>array('0'=>TEXT_NO,'1'=>TEXT_YES),
    		'tooltip_icon'=>TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_VALUE_DISPLAY_TIP,
    		'params'=>array('class'=>'form-control input-small'));
    
    $cfg[] = array(
    		'title'=>TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS,
    		'name'=>'level_settings',
    		'type'=>'textarea',
    		'tooltip_icon'=>TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS_INFO,
    		'tooltip'=>TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS_TIP,
    		'params'=>array('class'=>'form-control required'));
                
                                 
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    $cfg = new fields_types_cfg($field['configuration']);
    
    $display_choices_values = $cfg->get('display_choices_values');
    
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . ' field_' . $field['id'] . ($field['is_required']==1 ? ' required':'') . ($cfg->get('use_search')==1 ? ' chosen-select':''),
                        'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
    
    $choices = array();
    
		//use global lists if exsit    
    if($cfg->get('use_global_list')>0)
    {     
    	$default_id = global_lists::get_choices_default_id($cfg->get('use_global_list'));
    	
      $choices_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($cfg->get('use_global_list')). "' and parent_id=0 order by sort_order, name");
      while($v = db_fetch_array($choices_query))
      {
      	$choices[$v['id']] = $v['name'];
      }
    }
    else
    {  
    	$default_id = fields_choices::get_default_id($field['id']);
    	
      $choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($field['id']). "' and parent_id=0 order by sort_order, name");      
      while($v = db_fetch_array($choices_query))
      {
      	if($display_choices_values==1)
      	{
      		$v['name'] = $v['name'] . (strlen($v['value']) ? ' (' . ($v['value']>=0 ? '+':'') . $v['value'] . ')' : '');
      	}
      	
      	$choices[$v['id']] = $v['name'];
      }
    }
              
    //get level settings
    $level_settings = (strlen($cfg->get('level_settings')) ? preg_split("/\\r\\n|\\r|\\n/",$cfg->get('level_settings')) : array());
    
    //get max level
    $choices_tree_level = count($level_settings)-1;
               
    $values_array = ( strlen($obj['field_' . $field['id']]) ? explode(',', $obj['field_' . $field['id']]) : array());
    
    if(!count($values_array) and $default_id)
    {
    	$values_array = array($default_id);
    }
    
    $tooltip_array = (strlen($field['tooltip']) ? preg_split("/\\r\\n|\\r|\\n/",$field['tooltip']):array());
    
    $html = '';
    for($level =0; $level<=$choices_tree_level;$level++)
    {
    	//We use choices for first level only. For other levels we reset choices
    	if($level>0)
    	{
    		$choices = array();
    	}
    	
    	$field_name = $field['name'] .' '. $level;
    	
    	//use level settings
    	if(isset($level_settings[$level]))
    	{
    		$level_settings_array = explode(',',$level_settings[$level]);
    		
    		$field_name = trim($level_settings_array[0]);
    		
    		if(isset($level_settings_array[1]))
    		{
    			$choices = array(''=>trim($level_settings_array[1]))+$choices;
    		}
    		    		    		
    		$field_value = (isset($values_array[$level]) ? $values_array[$level] :'');
    	}
    	
    	$field_tooltip = '';
    	if(isset($tooltip_array[$level]))
    	{
    		$field_tooltip = $tooltip_array[$level];
    	}
    	    	
    	
    	$html .='
	          <div class="form-group form-group-' . $field['id'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $field['id']  . '">' .
    		          	($field['is_required']==1 ? '<span class="required-label">*</span>':'') .
    		          	($field['tooltip_display_as']=='icon' ? tooltip_icon($field_tooltip) :'') .
    		          	$field_name .
    		          	'</label>
	            <div class="col-md-9">
	          	  <div id="fields_' . $field['id'] . '_rendered_value">' . select_tag('fields[' . $field['id'] . '][' . $level . ']',$choices,$field_value,$attributes+array('data-level'=>$level,'data_value'=>$field_value)) . '</div>
	              ' . ($field['tooltip_display_as']!='icon' ? tooltip_text($field_tooltip):'') . '
	            </div>
	          </div>
	        ';
    }
    
    //if there are more then one level then build js tree handler
    if($choices_tree_level>0)
    {
    	$html .= $this->get_js_tree_handler($field, $choices_tree_level, $values_array, $display_choices_values);
    }
    
    return $html;
    
    //$value = ($obj['field_' . $field['id']]>0 ? $obj['field_' . $field['id']] : ($params['form']=='comment' ? '':$default_id)); 
    
    //return select_tag('fields[' . $field['id'] . ']',$choices,$value,$attributes);
  }
  
  function process($options)
  {         
    return (is_array($options['value']) ? implode(',',$options['value']) : $options['value']);
  }
    
  function output($options)
  {
  	$is_export = isset($options['is_export']);
  
  	$cfg = new fields_types_cfg($options['field']['configuration']);
  
  	//render global list value
  	if($cfg->get('use_global_list')>0)
  	{
  		return global_lists::render_value($options['value'], $is_export);
  	}
  	else
  	{
  		return fields_choices::render_value($options['value'], $is_export);
  	}
  } 
  
  static function output_export_template($field, $is_export=false)
  {
  	  	
  	$cfg = new fields_types_cfg($field['configuration']);
  
  	$html = '';
  	  	
  	if($cfg->get('value_displya_own_column')==1)
  	{
  		$level_settings = (strlen($cfg->get('level_settings')) ? preg_split("/\\r\\n|\\r|\\n/",$cfg->get('level_settings')) : array());
  
  		//print_r($listing_order_fields_id);
  		//print_r($listing_order_clauses);
  
  		foreach($level_settings as $level=>$level_cfg)
  		{
  			$level_cfg_array = explode(',',$level_cfg);
  			$level_name = $level_cfg_array[0];
  				
  			$field_id = $field['id'] . 'L'. $level;
  			
  			$html .= '
			    <li style="cursor: pointer" class="insert_to_template_description">
			      {#' . $field_id .':'. trim($level_name) . '}
			    </li>';
  				
  		}
  	}
  	else
  	{
  		$html .= '
	    <li style="cursor: pointer" class="insert_to_template_description">
	      {#' . $field['id'] .':'. $field['name'] . '}
	    </li>';
  	}

 		return $html;
  }  
  
  static function output_export_template_value($fields_id,$options)
  {
  	$is_export = isset($options['is_export']);
  
  	$cfg = new fields_types_cfg($options['field']['configuration']);
  	
  	$value = $options['value'];
  	  	  	  	
  	if(strstr($fields_id,'L'))
  	{
  		$fields_id_array = explode('L',$fields_id);
  		$level = $fields_id_array[1];
  		
  		if(strlen($value))
  		{
  			$value_array = explode(',',$value);
  			$value = $value_array[$level];
  		}
  	}  	  
  	  
  	//render global list value
  	if($cfg->get('use_global_list')>0)
  	{
  		return global_lists::render_value($value, $is_export);
  	}
  	else
  	{
  		return fields_choices::render_value($value, $is_export);
  	}
  }  
    
  static function output_listing_heading($field, $is_export=false)
  {
  	global $listing_order_fields_id, $listing_order_fields, $listing_order_clauses;
  	
  	$cfg = new fields_types_cfg($field['configuration']);
  
  	$html = '';
  	$export_array = array();
  	
  	$th_css_class = $field['type'] . '-th filed-' . $field['id'] . '-th';
  	  
  	if($cfg->get('value_displya_own_column')==1)
  	{
  		$level_settings = (strlen($cfg->get('level_settings')) ? preg_split("/\\r\\n|\\r|\\n/",$cfg->get('level_settings')) : array());
  
		  //print_r($listing_order_fields_id);
	    //print_r($listing_order_clauses);
  
  		foreach($level_settings as $level=>$level_cfg)
  		{
  			$level_cfg_array = explode(',',$level_cfg);
  			$level_name = $level_cfg_array[0];
  			
  			$field_id = $field['id'] . '-'. $level;
  			
  			if(!isset($listing_order_clauses[$field_id]))
  			{
  				$listing_order_clauses[$field_id] = 'asc';
  			}
  			
  			$listing_order_action = 'onClick="listing_order_by(\'' . $_POST['listing_container'] . '\',\'' . $field_id . '\',\'' . (($listing_order_clauses[$field_id]=='asc' and in_array($field_id,$listing_order_fields_id)) ? 'desc':'asc'). '\')"';
  			
  			$listing_order_css_class = 'class="' . $th_css_class . ' listing_order ' . (in_array($field_id,$listing_order_fields_id) ? 'listing_order_' . $listing_order_clauses[$field_id]:'') . '"';
 				
  			if($is_export)
  			{
  				$export_array[] = $level_name;
  			}
  			else
  			{
	  			$html .='
	              <th ' . $listing_order_action  . ' ' . $listing_order_css_class . '><div>' . $level_name . '</div></th>
	          ';
  			}
  		}
  	}
  	else
  	{
  		if($is_export)
  		{
  			$export_array[] = $field['name'];
  		}
  		else
  		{
	  		$listing_order_css_class = 'class="' . $th_css_class . ' listing_order ' . (isset($listing_order_clauses[$v['id']]) ? 'listing_order_' . $listing_order_clauses[$v['id']]:'') . '"';
	  		$html = '<th ' . $listing_order_css_class . '><div>' .$field['name'] . '</div></th>';
  		}
  	}
  	
  	if($is_export)
  	{
  		return $export_array;
  	}
  	else
  	{  
  		return $html;
  	}
  }
  
  static function output_listing($options, $is_export = false)
  {
  	$cfg = new fields_types_cfg($options['field']['configuration']);
  	 
  	$html = '';
  	$export_array = array();
  	
  	if($cfg->get('value_displya_own_column')==1)
  	{
  		$level_settings = (strlen($cfg->get('level_settings')) ? preg_split("/\\r\\n|\\r|\\n/",$cfg->get('level_settings')) : array());
  
  
  		foreach($level_settings as $level=>$level_cfg)
  		{
  			$level_cfg_array = explode(',',$level_cfg);
  			$level_name = $level_cfg_array[0];
  				
  			$level_values = explode(',',$options['value']);
  
  			$value = '';
  				
  			if(isset($level_values[$level]))
  			{
  				$value = ($cfg->get('use_global_list') ? global_lists::render_value($level_values[$level]) : fields_choices::render_value($level_values[$level]));
  			}
  			
  			if($is_export)
  			{
  				$export_array[] = trim(strip_tags($value));
  			}
  			else 
  			{
	  			$html .='            
	              <td>' . $value . '</td>            
	          ';
  			}
  		}  			  			
  	}
  	else
  	{
  		$obj = new fieldtype_dropdown_multilevel;
  		
  		if($is_export)
  		{
  			$export_array[] =  trim(strip_tags($obj->output($options)));
  		}
  		else
  		{
  			$html = '<td class="' . $options['field']['type'] . '">' . $obj->output($options) . '</td>';
  		}
  	}
  	
  	if($is_export)
  	{  		
  		return $export_array;
  	}
  	else
  	{
  		return $html;
  	}  	   	
  }  
  
  static function output_info_box($options, $is_export = false)
  {
  	$cfg = new fields_types_cfg($options['field']['configuration']);
  	
  	$html = '';
  	$export_array = array();
  	
  	if($cfg->get('value_displya_own_column')==1)
  	{
  		$level_settings = (strlen($cfg->get('level_settings')) ? preg_split("/\\r\\n|\\r|\\n/",$cfg->get('level_settings')) : array());
  		
  		
  		foreach($level_settings as $level=>$level_cfg)
  		{
  			$level_cfg_array = explode(',',$level_cfg);
  			$level_name = $level_cfg_array[0];
  			
  			$level_values = explode(',',$options['value']);
  			  			
  			$value = '';
  			
  			if(isset($level_values[$level]))
  			{
  				$value = ($cfg->get('use_global_list') ? global_lists::render_value($level_values[$level]) : fields_choices::render_value($level_values[$level]));
  			}
  			
  			if($is_export)
  			{
  				$export_array[] = array($level_name,$value);
  			}	
  			else
  			{
  			
	  			$html .='
	            <tr class="form-group-' . $options['field']['id'] . '">
	              <th ' . (strlen($level_name)>25 ? 'class="white-space-normal"':''). '>' .
	  			              $level_name .
	  			              '</th>
	              <td>' . $value . '</td>
	            </tr>
	          ';
  			}
  		}
  			
  			
  	}
  	else 
  	{	  
	  	//render global list value
	  	if($cfg->get('use_global_list')>0)
	  	{
	  		$value =  global_lists::render_value($options['value']);
	  	}
	  	else
	  	{
	  		$value =  fields_choices::render_value($options['value']);
	  	}
	  	
	  	$field_name = $options['field']['name'];
	  	
	  	if($is_export)
	  	{
	  		$export_array[] = array($field_name,$value);
	  	}
	  	else
	  	{
		  	$html .='
	            <tr class="form-group-' . $options['field']['id'] . '">
	              <th ' . (strlen($field_name)>25 ? 'class="white-space-normal"':''). '>' .
		  	              $field_name .
		  	              '</th>
	              <td>' . $value . '</td>
	            </tr>
	          ';
	  	}
  	}
  	
  	if($is_export)
  	{
  		return $export_array;
  	}
  	else
  	{
  		return $html;
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
    
  function get_js_tree_handler($field, $choices_tree_level, $values_array, $display_choices_values='')
  {
  	$cfg = new fields_types_cfg($field['configuration']);
  	  	
  	$js_tree = ($cfg->get('use_global_list') ? global_lists::get_js_level_tree($cfg->get('use_global_list')) : fields_choices::get_js_level_tree($field['id'],0,array(),0,$display_choices_values));	

  	//echo '<pre>';
  	//print_r($js_tree);
  	//exit();
  	
  	$html = '
 		<script>
  		function multilevel_dropdown_' . $field['id']. '(selected_value, level)
  		{		
  				level++;
  				choices_tree_level = ' . $choices_tree_level. ';
  				
  				var update_field = "#fields_' .  $field['id'] . '_"+level;  					  					
  				//alert(update_field)
  				
  				$(update_field).find("option[value!=\'\']").remove();
  				
  				//reset previous selected values		
  				if(level<choices_tree_level)
  				{
  					for(i=level+1;i<=choices_tree_level;i++)
  					{
  						$("#fields_' .  $field['id'] . '_"+i).find("option[value!=\'\']").remove()
  					}	
  				}		
  	';
  	
  	//render tree values
  	foreach($js_tree as $parent_id=>$values)
  	{
  		$html .= '
  			if(selected_value==' . $parent_id . ')
  			{		
  		';
  		foreach($values as $v)
  		{
  			$html .= $v;
  		}
  		
  		$html .= '
  			}
  		';
  	}
  	  	
  	//update field value
  	$html .= '  			
  			field_value = $(update_field).attr("data_value")
  			$(update_field).val(field_value)  
  			';
  	
  	if($cfg->get('use_search'))
  	{
  		$html .= '
  				$(update_field).trigger("chosen:updated");
  				';
  	}
  	
  	//end of multilevel_dropdown_# function 
		$html .= '  			
  		}
  	';
  	
  	//add on change handler 
  	for($level = 0; $level<$choices_tree_level;$level++)
  	{
  		$html .='
  			$("#fields_' . $field['id'] . '_' . $level . '").change(function(){ multilevel_dropdown_' . $field['id']. '($(this).val(),$(this).attr("data-level")) })  			
  		';
  		
  		if(isset($values_array[$level]))
  		{
  			$html .= '
  				multilevel_dropdown_' . $field['id']. '(' . (int)$values_array[$level] . ',' . $level . ')
  			';
  		}	
  	}
  	
  	$html .= '</script>';
  	
  	  	
  	return $html;
  }
  
}