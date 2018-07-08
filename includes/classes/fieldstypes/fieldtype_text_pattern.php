<?php

class fieldtype_text_pattern
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_TEXT_PATTERN);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_PATTERN, 
                   'name'=>'pattern',
                   'type'=>'textarea',    
                   'tooltip'=>TEXT_ENTER_TEXT_PATTERN_INFO,
                   'params'=>array('class'=>'form-control'));      
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {        
    return '';
  }
  
  function process($options)
  {
    return '';
  }
  
  function output($options)
  {
    global $app_user, $app_entities_cache, $app_fields_cache, $fields_access_schema_holder, $parent_items_name_holder, $app_num2str;
    
    $html = '';
    
    $cfg = new fields_types_cfg($options['field']['configuration']);
        
    $entities_id = $options['field']['entities_id'];
    
    $item = $options['item'];
    
    if(!isset($fields_access_schema_holder[$entities_id]))
    {	
     $fields_access_schema = $fields_access_schema_holder[$entities_id] = users::get_fields_access_schema($entities_id,$app_user['group_id']);
    }
    else
    {
    	$fields_access_schema = $fields_access_schema_holder[$entities_id];
    }
            
    if(isset($options['custom_pattern']))
    {
      $pattern = $options['custom_pattern'];
    }
    else
    {
      $pattern = $cfg->get('pattern');
    }
    
    if(strlen($pattern)>0)
    {      
      if(preg_match_all('/\[(\w+)\]/',$pattern,$matches))
      {  
      	//use to check if formulas fields using in text pattern
      	$formulas_fields = false;
      	      	      
        foreach($matches[1] as $matches_key=>$fields_id)
        {                                
            $field = false;
            
            if(isset($app_fields_cache[$entities_id]['fieldtype_' . $fields_id]))
            {
            	$field = $app_fields_cache[$entities_id]['fieldtype_' . $fields_id];
            }
            elseif(isset($app_fields_cache[$entities_id][$fields_id]))
            {
            	$field = $app_fields_cache[$entities_id][$fields_id]; 
            }
            
            if($field)
            {                	            	            	
              //check field access
              if(isset($fields_access_schema[$field['id']]))
              {
                if($fields_access_schema[$field['id']]=='hide') continue;
              }
                                                                     
              switch($field['type'])
              {
              	case 'fieldtype_parent_item_id':
              			$enitites_info = $app_entities_cache[$entities_id];
              			
              			if($enitites_info['parent_id']>0 and $item['parent_item_id']>0)
              			{
              				if(!isset($parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']]))
              				{
              					$value = $parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']] = items::get_heading_field($enitites_info['parent_id'],$item['parent_item_id']);
              				}
              				else
              				{
              					$value = $parent_items_name_holder[$enitites_info['parent_id']][$item['parent_item_id']];
              				}
              			}
              			else 
              			{
              				$value = '';
              			}
              		break;
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
                case 'fieldtype_formula':
                	                		
                  	//check if formula value exist in item and if not then do extra query to calcualte it
                  	if(strlen($item['field_' . $field['id']])==0)
                  	{
                  		//prepare forumulas query
                  		if(!$formulas_fields)
                  		{
                  			$formulas_fields_query = db_query("select e.* " . fieldtype_formula::prepare_query_select($entities_id, '') . " from app_entity_" . $entities_id . " e where id='" . $item['id'] . "'");
                  			$formulas_fields = db_fetch_array($formulas_fields_query);
                  		}
                  
                  		$value = $item['field_' . $field['id']] = $formulas_fields['field_' . $field['id']];
                  	}
                  	else
                  	{
                  		$value = $item['field_' . $field['id']];
                  	}
                  break;
                default:
                    $value = $item['field_' . $field['id']]; 
                  break;
              }
              
              $output_options = array('class'=>$field['type'],
                                  'value'=>$value,
                                  'field'=>$field,
                                  'item'=>$item,
                                  'is_export'=>true,                              
              										'is_print'=>true,
                                  'path'=>$options['path']);
                                                                                                                                                    
              if(in_array($field['type'],array('fieldtype_textarea_wysiwyg')))
              {
                $output = trim(fields_types::output($output_options));
              }
              elseif($field['type']=='fieldtype_parent_item_id')
              {
              	$output = $value;
              }
              else
              {
                $output = trim(strip_tags(fields_types::output($output_options)));
              }   
              
              //echo '<br>' . $fields_id . ' ' . $output . ' ' . $matches[0][$matches_key];  
              
              $pattern = str_replace($matches[0][$matches_key],$output,$pattern);   
                                         
            }        
        
        }
        
        //check if fields was replaced
        if(preg_replace('/\[(\d+)\]/','',$cfg->get('pattern'))!=$pattern)
        {
          $html = $pattern;
        }
        
      }
      else 
      {
      	$html = $pattern;
      }
    }
    
    //num2str
    $html = $app_num2str->prepare($html);
    
    return $html;
  }
  
  function output_singe_text($text,$entities_id,$item)
  {
  	$output_options = array('item' => $item);
  	$output_options['field']['configuration'] = '';
  	$output_options['field']['entities_id'] = $entities_id;
  	$output_options['path'] = $entities_id . '-' . $item['id'];
  	$output_options['custom_pattern'] = $text;
  	
  	return $this->output($output_options);
  }
}