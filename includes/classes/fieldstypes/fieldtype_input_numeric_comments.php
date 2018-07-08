<?php

class fieldtype_input_numeric_comments
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TITLE);
  }
  
  function get_configuration($params = array())
  {
  	$cfg = array();
  	
    $cfg[] = array('title'=>tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT, 'name'=>'number_format','type'=>'input','params'=>array('class'=>'form-control input-small input-masked','data-mask'=>'9/~/~'), 'default'=>CFG_APP_NUMBER_FORMAT);
    $cfg[] = array('title'=>tooltip_icon(TEXT_CALCULATE_TOTALS_INFO) . TEXT_CALCULATE_TOTALS, 'name'=>'calclulate_totals','type'=>'checkbox');
    $cfg[] = array('title'=>TEXT_CALCULATE_AVERAGE_VALUE, 'name'=>'calculate_average','type'=>'checkbox');
    
    $cfg[] = array('title'=>TEXT_DEFAULT_VALUE,'name'=>'default_value','type'=>'input','tooltip_icon'=>TEXT_DEFAULT_VALUE_INFO,'params'=>array('class'=>'form-control input-small'));
    
    $cfg[] = array('title'=>TEXT_PREFIX,'name'=>'prefix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    $cfg[] = array('title'=>TEXT_SUFFIX,'name'=>'suffix','type'=>'input','params'=>array('class'=>'form-control input-small'));
    
    return $cfg;
  }
    
  function render($field,$obj,$params = array())
  {
  		
    if($params['form']=='comment')
    {
    	$cfg =  new fields_types_cfg($field['configuration']);
    	
    	$value = '';
    	
    	//handle default value
    	if(strlen($cfg->get('default_value'))>0)
    	{
    		$value = $cfg->get('default_value');
    	}
    	
      return input_tag('fields[' . $field['id'] . ']',$value,array('class'=>'form-control input-small fieldtype_input_numeric field_' . $field['id'] . ($field['is_required']==1 ? ' required noSpace':'') . ' number'));
    }
    else
    {
      return '<p class="form-control-static">' . $obj['field_' . $field['id']] . '</p>' . input_hidden_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']]);
    }
  }
  
  function get_fields_sum($entity_id,$item_id,$field_id)
  {
    $total = 0;
    
    $comments_query = db_query("select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input($item_id) . "'");
    while($comments = db_fetch_array($comments_query))
    {
      $history_query = db_query("select * from app_comments_history where comments_id='" . db_input($comments['id']) . "' and fields_id='" . $field_id. "'");
      while($history = db_fetch_array($history_query))
      {        
        $total +=$history['fields_value'];        
      }      
    }
    
    return $total;
  }
  
  function process($options)
  {  
    return str_replace(array(',',' '),array('.',''),db_prepare_input($options['value']));
  }
  
  function output($options)
  {
  	//return non-formated value if export
  	if(isset($options['is_export']) and !isset($options['is_print']))
  	{
  		return $options['value'];
  	}
  	  	  	
    $cfg = new fields_types_cfg($options['field']['configuration']);
                    
    if(strlen($cfg->get('number_format'))>0 and strlen($options['value'])>0)
    {
      $format = explode('/',str_replace('*','',$cfg->get('number_format')));
            
      $value = number_format($options['value'],$format[0],$format[1],$format[2]);
    }
    elseif(strstr($options['value'],'.'))
    {    	    	
      $value = number_format((float)$options['value'],2,'.','');
    }
    else
    {
      $value = $options['value'];
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