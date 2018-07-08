<?php

class fieldtype_js_formula
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_JS_FORMULA_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_FORMULA, 'name'=>'formula','type'=>'textarea','tooltip'=>TEXT_JS_FORMULA_TIP,'params'=>array('class'=>'form-control'));
    
    $cfg[] = array('title'=>tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT, 'name'=>'number_format','type'=>'input','params'=>array('class'=>'form-control input-small input-masked','data-mask'=>'9/~/~'), 'default'=>CFG_APP_NUMBER_FORMAT);
    $cfg[] = array('title'=>tooltip_icon(TEXT_CALCULATE_TOTALS_INFO) . TEXT_CALCULATE_TOTALS, 'name'=>'calclulate_totals','type'=>'checkbox');
    $cfg[] = array('title'=>TEXT_CALCULATE_AVERAGE_VALUE, 'name'=>'calculate_average','type'=>'checkbox');
    
    $cfg[] = array('title'=>TEXT_PREFIX,'name'=>'prefix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    $cfg[] = array('title'=>TEXT_SUFFIX,'name'=>'suffix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
  	global $app_fields_cache;
  	
    $cfg =  new fields_types_cfg($field['configuration']);
    
    $formula = $js_formula = $cfg->get('formula');
    
    $js_funciton_name = 'form_handle_js_formula_' . $field['id'] . '()';
    $js_funciton_name_delay = 'setTimeout("' . $js_funciton_name . '",10);';
    
    $html_change_hanlder = '';
    
    //start build funciton
    $html = '
    	<script>
    		function ' . $js_funciton_name. '
    		{
    				//alert(1)
    		';
    
    //prepare app_choices_values
    if(preg_match_all("/get_value\(([^)]*)\)/", $formula, $matches))
    {
    	//print_r($matches);
    	$prepared_fields = array();
    	foreach($matches[1] as $field_id)
    	{
    		$field_id = str_replace(array('[',']'),'',$field_id);
    		if(!in_array($field_id,$prepared_fields))
    		{  	
    			$prepared_fields[] = $field_id;
    			//echo $field_id;
    			$fields_choices_query = db_query("select id,value from app_fields_choices where fields_id='" . $field_id . "'");
    			while($fields_choices = db_fetch_array($fields_choices_query))
    			{
    				$html .= 'app_choices_values[' . $fields_choices['id'] . ']= ' . (strlen($fields_choices['value']) ? $fields_choices['value'] : 0) . ';' . "\n";
    			}
    		}
    	}
    }
    
    //prepare fields values and change handler
    if(preg_match_all("/\[([^]]*)\]/", $formula, $matches))
    {
    	//print_r($matches);
    	
    	$entities_id = $field['entities_id'];
    	
    	foreach($matches[1] as $field_id)
    	{    		
    		if(isset($app_fields_cache[$entities_id][$field_id]))
    		{    	
    			switch($app_fields_cache[$entities_id][$field_id]['type'])
    			{
    				case 'fieldtype_input_numeric':    					
    					$html .= 'var field_' . $field_id . ' = ($("#fields_' . $field_id. '").val()>0) ? Number($("#fields_' . $field_id. '").val()):0;' . "\n";    							
    					$html_change_hanlder .= '$("#fields_' . $field_id. '").keyup(function(){ ' . $js_funciton_name_delay . '})'  . "\n";
    					break;
    				case 'fieldtype_dropdown_multiple':
    					$html .= 'var field_' . $field_id . ' = $("#fields_' . $field_id. '").val();'  . "\n";
    					$html_change_hanlder .= '$("#fields_' . $field_id. '").change(function(){ ' . $js_funciton_name_delay . '})' . "\n";
    					break;
    				case 'fieldtype_dropdown_multilevel':
    					$html .= 'var field_' . $field_id . ' = new Array();' . "\n";
    					$html .= '$(".field_' . $field_id. '").each(function(){ field_' . $field_id . '.push($(this).val()); })' . "\n";
    					$html_change_hanlder .= '$(".field_' . $field_id. '").change(function(){ ' . $js_funciton_name_delay . ' })'  . "\n";
    					break;
    				case 'fieldtype_checkboxes':
    					$html .= 'var field_' . $field_id . ' = new Array();' . "\n";
    					$html .= '$(".field_' . $field_id. ':checked").each(function(){ field_' . $field_id . '.push($(this).val()); })' . "\n";
    					$html_change_hanlder .= '$(".field_' . $field_id. '").change(function(){ ' . $js_funciton_name_delay . '})'  . "\n";
    					break;
    				case 'fieldtype_radioboxes':
    					$html .= 'var field_' . $field_id . ' = ($(".field_' . $field_id. ':checked").val()>0) ? Number($(".field_' . $field_id. ':checked").val()):0;' . "\n";    						
    					$html_change_hanlder .= '$(".field_' . $field_id. '").change(function(){ ' . $js_funciton_name_delay . '})'  . "\n";
    					break;    				
    				case 'fieldtype_dropdown':
    					$html .= 'var field_' . $field_id . ' = ($("#fields_' . $field_id. '").val()>0) ? Number($("#fields_' . $field_id. '").val()):0;'  . "\n";    							
    					$html_change_hanlder .= '$("#fields_' . $field_id. '").change(function(){ ' . $js_funciton_name_delay . '})' . "\n";
    						break;
    				default:
    					$html .= '
    							var field_' . $field_id . ' = 0;
    							';
    					break;
    			}
    			
    			$html .= 'if($(".form-group-' . $field_id. '").css("display") == "none"){ field_' . $field_id . '=0; }' . "\n";
    		}
    		
    		//prepare fields
    		$js_formula = str_replace('[' . $field_id. ']','field_' . $field_id,$js_formula);
    	}
    }
    
    //set app_get_choices_values funciton 
    $js_formula = str_replace('get_value(','app_get_choices_values(',$js_formula);
        
    //echo $js_formula;
        
    //try calculate js formula to value    
    $html .= '
    	try{	
    		 value = ' . $js_formula . ';
    		 value_html = value;';
           
    //toFixed() returns a string, with the number written with a specified number of decimals:
    $decimals = 2;
    $dec_point = '.';
    $thousands_sep = '';
    if(strlen($cfg->get('number_format'))>0)
    {
    	$format = explode('/',str_replace('*','',$cfg->get('number_format')));
    
    	$decimals = $format[0];
    	$dec_point = $format[1];
    	$thousands_sep = $format[2];
    	    	    		
    	$html .= '
    		 value_html = number_format(value,"' . $decimals . '","' . $dec_point . '","' . $thousands_sep. '")';
    		    	    
    }
    
    //set value to field
    $html .='
    		 
    		 $("#fields_' . $field['id']. '").val(value)
    		 
    		 $("#fields_' . $field['id'] . '_html_value").html("' . $cfg->get('prefix') . '"+value_html+"' . $cfg->get('suffix'). '")
    		
    		} 
    		catch (err) {
					alert("' . TEXT_JS_FORMULA_ERROR . ': ' . str_replace(array("\n","\r","\n\r"),'',$js_formula). '"+"\n"+err)  				
				}
    		 		
    	 }
							
			 $(function(){ 				
    		' . $html_change_hanlder. '
    		' . ($params['is_new_item'] ? $js_funciton_name : '') . '
    	 })
    	</script>	
    		';
    
    //$html = '';
    
    return $html  . '<div id="fields_' . $field['id'] . '_html_value" class="form-control-static js-formula-value">' .  $cfg->get('prefix')  . number_format((float)$obj['field_' . $field['id']],$decimals,$dec_point,$thousands_sep) . $cfg->get('suffix'). '</div>'. input_hidden_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']]);
  }
  
  function process($options)
  {
    return db_prepare_input($options['value']);
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
  	$filters = $options['filters'];
  	$sql_query = $options['sql_query'];
  
  	$sql = reports::prepare_numeric_sql_filters($filters, $options['prefix']);
  
  	if(count($sql)>0)
  	{
  		$sql_query[] =  implode(' and ', $sql);
  	}
  
  	return $sql_query;
  }
}