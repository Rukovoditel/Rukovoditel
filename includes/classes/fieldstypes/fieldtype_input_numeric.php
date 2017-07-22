<?php

class fieldtype_input_numeric
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_NUMERIC_TITLE);
  }
  
  function get_configuration($params = array())
  {
  	$cfg = array();
  	
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
                   
    $cfg[] = array('title'=>tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT, 'name'=>'number_format','type'=>'input','params'=>array('class'=>'form-control input-small input-masked','data-mask'=>'9/~/~'), 'default'=>CFG_APP_NUMBER_FORMAT);
    $cfg[] = array('title'=>tooltip_icon(TEXT_CALCULATE_TOTALS_INFO) . TEXT_CALCULATE_TOTALS, 'name'=>'calclulate_totals','type'=>'checkbox');    
    $cfg[] = array('title'=>TEXT_CALCULATE_AVERAGE_VALUE, 'name'=>'calculate_average','type'=>'checkbox');
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
    $cfg[] = array('title'=>TEXT_IS_UNIQUE_FIELD_VALUE, 'name'=>'is_unique','type'=>'checkbox','tooltip_icon'=>TEXT_IS_UNIQUE_FIELD_VALUE_TIP);
    
    $cfg[] = array('title'=>TEXT_DEFAULT_VALUE,
    		'name'=>'default_value',
    		'type'=>'input',
    		'tooltip_icon'=>TEXT_DEFAULT_VALUE_INFO,
    		'params'=>array('class'=>'form-control input-small'));
    
    return $cfg;
  }
    
  function render($field,$obj,$params = array())
  {
  	$value = $obj['field_' . $field['id']];
  	
    $cfg =  new fields_types_cfg($field['configuration']);
    
    //handle default value
    if($params['is_new_item']==true and strlen($cfg->get('default_value'))>0)
    {
    	$value = $cfg->get('default_value');
    }
    
    $attributes = array('class'=>'number form-control ' . $cfg->get('width') .
                        ' fieldtype_input_numeric field_' . $field['id'] . 
                        ($field['is_required']==1 ? ' required noSpace':'') .
                        ($cfg->get('is_unique')==1 ? ' is-unique':'')    										
                        ); 
    
    return input_tag('fields[' . $field['id'] . ']',$value,$attributes);
  }
  
  function process($options)
  {       
    return str_replace(array(',',' '),array('.',''),db_prepare_input($options['value']));
  }
  
  function output($options)
  {
  	//return non-formated value if export
  	if(isset($options['is_export']))
  	{
  		return $options['value'];
  	}
  		
    $cfg = new fields_types_cfg($options['field']['configuration']);
                    
    if(strlen($cfg->get('number_format'))>0 and strlen($options['value'])>0)
    {
      $format = explode('/',str_replace('*','',$cfg->get('number_format')));
                        
      return number_format($options['value'],$format[0],$format[1],$format[2]);
    }
    else
    {
      return $options['value'];
    }
  }
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
                
    $sql = reports::prepare_numeric_sql_filters($filters);
    
    if(count($sql)>0)
    {
      $sql_query[] =  implode(' and ', $sql);
    }
                
    return $sql_query;
  }
}