<?php

class fieldtype_formula
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_FORMULA_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_FORMULA, 'name'=>'formula','type'=>'textarea','tooltip_icon'=>TEXT_FORMULA_TIP_USAGE,'tooltip'=>TEXT_FORMULA_TIP,'params'=>array('class'=>'form-control'));
    
    $cfg[] = array('title'=>tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT, 'name'=>'number_format','type'=>'input','params'=>array('class'=>'form-control input-small input-masked','data-mask'=>'9/~/~'), 'default'=>CFG_APP_NUMBER_FORMAT);
    $cfg[] = array('title'=>tooltip_icon(TEXT_CALCULATE_TOTALS_INFO) . TEXT_CALCULATE_TOTALS, 'name'=>'calclulate_totals','type'=>'checkbox');
    $cfg[] = array('title'=>TEXT_CALCULATE_AVERAGE_VALUE, 'name'=>'calculate_average','type'=>'checkbox');
    
    $cfg[] = array('title'=>TEXT_PREFIX,'name'=>'prefix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    $cfg[] = array('title'=>TEXT_SUFFIX,'name'=>'suffix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    return '<p class="form-control-static">' . $obj['field_' . $field['id']]  .'</p>' . input_hidden_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']]);
  }
  
  function process($options)
  { 
    return $options['value'];
  }
  
  function output($options)
  {
  	//return non-formated value if export
  	if(isset($options['is_export']) and !isset($options['is_print']))
  	{
  		return $options['value'];
  	}
  	
    $value = $options['value'];
    
    //just return value if not numeric (not numeric values can be returned using IF operator)
    if(!is_numeric($value))
    {
    	return $value;
    }
    
    //return value using number format
    $cfg = new fields_types_cfg($options['field']['configuration']);
    
    if(strlen($cfg->get('number_format'))>0 and strlen($value)>0)
    {
      $format = explode('/',str_replace('*','',$cfg->get('number_format')));
                  
            
      $value = number_format($value,$format[0],$format[1],$format[2]);
    }
    elseif(strstr($value,'.'))
    {
      $value = number_format((float)$value,2,'.','');
    }
    
    //add prefix and sufix
    $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
            
    return $value;
  }
  
  function reports_query($options)
  {
  	global $sql_query_having;
  	  	
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
                
    $sql = reports::prepare_numeric_sql_filters($filters,'');
    
    if(count($sql)>0)
    {
      $sql_query_having[$options['entities_id']][] =  implode(' and ', $sql);
    }
                
    return $sql_query;
  } 
  
  /*
   * to save server load we check if formula needed in listing
   */  
  public static function check_formula_query_needed($formula_fields_id, $entities_id, $check_needed)
  {  	    	
  	$check_formula_needed = false;
  	
  	//check if formula field is in listing
  	if(isset($check_needed['reports_id']))
  	{
  		$reports_info_query = db_query("select entities_id, fields_in_listing from app_reports where id='" . $check_needed['reports_id']. "'");
  		$reports_info = db_fetch_array($reports_info_query);
  	}
  	
  	$text_pattern_where_sql = '';
  	
  	//check custom listing fields
  	if(isset($check_needed['fields_in_listing']))
  	{
  		if(in_array($formula_fields_id,explode(',',$check_needed['fields_in_listing'])))
  		{
  			$check_formula_needed = true;
  		}
  		
  		if(strlen($check_needed['fields_in_listing']))
  		{
  			$text_pattern_where_sql = " and id in (" . $check_needed['fields_in_listing'] . ")";
  		}
  	}
  	//check reports settings
  	elseif(strlen($reports_info['fields_in_listing']))
  	{
  		if(in_array($formula_fields_id,explode(',',$reports_info['fields_in_listing'])))
  		{
  			$check_formula_needed = true;
  		}
  		
  		$text_pattern_where_sql = " and id in (" . $reports_info['fields_in_listing'] . ")";
  	}
  	//check default listig settings
  	else  
  	{  		
  		$check_query = db_query("select id from app_fields where id='" . $formula_fields_id . "' and listing_status=1");
  		if($check = db_fetch_array($check_query))
  		{
  			$check_formula_needed = true;
  		}
  		
  		$text_pattern_where_sql = " and listing_status=1";
  	}
  	
  	
  	//check if fomula used in filters
  	if(!$check_formula_needed and isset($check_needed['reports_id']))
  	{
  		$check_query = db_query("select count(*) as total from app_reports_filters where reports_id='" . $check_needed['reports_id'] . "' and fields_id='" . $formula_fields_id . "'");
  		$check = db_fetch_array($check_query);
  		
  		if($check['total']>0)
  		{
  			$check_formula_needed = true;
  		}
  	}
  	  	
  	//check if text pattersn using formulas
  	if(!$check_formula_needed and strlen($text_pattern_where_sql))
  	{	  		
	  	$fields_query = db_query("select configuration from app_fields where entities_id='" . $entities_id . "' {$text_pattern_where_sql} and type='fieldtype_text_pattern'");
	  	while($fields = db_fetch_array($fields_query))
	  	{
	  		$cfg = new fields_types_cfg($fields['configuration']);
	  		$pattern = $cfg->get('pattern');
	  			  			  		  		  		
	  		if(strstr($pattern,'[' . $formula_fields_id . ']'))
	  		{
	  			$check_formula_needed = true;	  			
	  		}
	  	}
  	}
  	  	
  	return $check_formula_needed;
  }
  
  /**
   *  function to prepare sql 
   *  by default funciton reurn string with formulas query
   *  $prepare_field_sum with ture retusn fields sum (using in graph report)
   *  $listing_sql_query_select as array return list of sql query in array (using in listing total calculation)
   */
  public static function prepare_query_select($entities_id, $listing_sql_query_select,$prepare_field_sum = false, $check_needed = false)
  {  
  	global $app_not_formula_fields_cache, $app_formula_fields_cache, $app_entities_cache, $app_currencies_cache;
  	
  	//get available fields for formula
    $available_fields = array();    
    if(isset($app_not_formula_fields_cache[$entities_id]))
    {
    	$available_fields = $app_not_formula_fields_cache[$entities_id];
    }
    
    //get formulas    
    if(isset($app_formula_fields_cache[$entities_id]))
    {
    	$formulas_fields = array();
    	
    	foreach($app_formula_fields_cache[$entities_id] as $fields)
    	{
    		$cfg = fields_types::parse_configuration($fields['configuration']);
    	  
    		if(strlen($cfg['formula']))
    		{
    			$formulas_fields[$fields['id']] = '(' . $cfg['formula'] . ')';
    		}
    	}
                             	    
	    foreach($app_formula_fields_cache[$entities_id] as $fields)
	    {	    		    	
	    	$cfg = new fields_types_cfg($fields['configuration']);
	    		    		      	   
	      $formula = $cfg->get('formula');
	      	      	      	      	      
	      //check if formula needed in query
	      if($check_needed)
	      {
	      	if(!self::check_formula_query_needed($fields['id'],$entities_id, $check_needed))
	      	{
	      		continue;
	      	}
	      }
	      
	      if(strlen($formula)>0)
	      {
	      	//prepare formula fields
	      	$formula = self::prepare_formula_fields($formulas_fields, $formula);
	      	
	      	//prepare fields
	        foreach($available_fields as $fields_id)
	        {
	          $formula = str_replace('[' . $fields_id . ']','e.field_' . $fields_id,$formula);
	        }
	        	        	        
	        //handle get_vallue()
	        $formula = self::perpare_choices_get_value_function($entities_id, $formula);
	        	        	        
	        //prepare [TODAY]
	        $formula = str_replace('[TODAY]',get_date_timestamp(date('Y-m-d')),$formula);
	        	        	        
	        //preapre [currecny code]
	        if(is_ext_installed())
	        {
	        	foreach($app_currencies_cache as $currecny)
	        	{
	        		$formula = str_replace('[' . $currecny['code'] . ']',$currecny['value'],$formula);
	        	}
	        }
	        	        	        
	        if(strstr($formula,'{') and class_exists('functions'))
	        {
	          $formula = functions::prepare_formula_query($entities_id, $formula);
	        }
	        
	        //echo 'test=' . htmlspecialchars($formula) .'<br>';
	                        
	        if(!strstr($formula,'[') and !strstr($formula,'{'))
	        {    
	          if($prepare_field_sum)
	          {
	            $listing_sql_query_select .= ", sum(" . $formula . ") as sum_field_" . $fields['id'] . " ";          	
	          }
	          elseif(is_array($listing_sql_query_select))
	          {
	            $listing_sql_query_select[] = "(" . $formula . ") as field_" . $fields['id']; 
	          }
	          else
	          {    
	            $listing_sql_query_select .= ", (" . $formula . ") as field_" . $fields['id'];
	          }          
	        }
	        else
	        {        	
	          echo '<div class="alert alert-danger">' . sprintf(TEXT_ERROR_FORMULA_CALCULATION,$app_entities_cache[$entities_id]['name'],$fields['name'], $fields['id'], $cfg->get('formula')) .  '</div>';
	        }
	      }
	    }	    
    }  
	       
    return $listing_sql_query_select;
  } 
  
  public static function prepare_formula_fields($formulas_fields, $formula)
  {
  	$check_count = 0;
  	
  	do
  	{  	
  		$check_count++;
  		
	  	foreach($formulas_fields as $fields_id=>$formula_text)
	  	{
	  		$formula = str_replace('[' . $fields_id . ']',$formula_text,$formula);
	  	}
	  	
	  	$check = false;
	  	
	  	foreach($formulas_fields as $fields_id=>$formula_text)
	  	{
	  		if(strstr($formula,'[' . $fields_id . ']')) $check = true;
	  	}
  	}
  	while($check==true and $check_count<200);
  	
  	return $formula;
  }
  
  public static function perpare_choices_get_value_function($entities_id, $formula,$prefix =  'e')
  {
  	global $app_fields_cache;
  	  	  
  	if(preg_match_all("/get_value\([^)]*\)/", $formula, $matches))
  	{  		  		
  		foreach($matches[0] as $get_value_function)
  		{  		
  			$field_id = str_replace(array('get_value(' . $prefix . '.field_',')'),'',$get_value_function);
  			  			  			  
  			$field_query = db_query("select type from app_fields where id='"  . db_input($field_id) . "'");
  			if($field = db_fetch_array($field_query))
  			{
  				  				
  				switch($field['type'])
  				{
  					case 'fieldtype_dropdown':	
  					case 'fieldtype_radioboxes':  						
  						$formula = str_replace("get_value({$prefix}.field_" . $field_id,"(select fcv.value from app_fields_choices fcv where fcv.id = {$prefix}.field_" . $field_id, $formula);  						
  						break;
  					default:
  						$to_replace_str = str_replace("get_value(","(select sum(fcv.value) from app_fields_choices fcv where find_in_set(fcv.id,", $get_value_function) . ")";
  						$formula = str_replace($get_value_function,$to_replace_str,$formula);
  						break;
  				}
  				
  			}  			  			
  		}
  	}
  	  	
  	return $formula;
  }
}