<?php

class fieldtype_autostatus
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_AUTOSTATUS_TITLE,'has_choices'=>true);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_NOTIFY_WHEN_CHANGED, 'name'=>'notify_when_changed','type'=>'checkbox','tooltip_icon'=>TEXT_NOTIFY_WHEN_CHANGED_TIP);
                                         
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
		return '<p class="form-control-static"><table><tr><td>' . fields_choices::render_value($obj['field_' . $field['id']])  . '</td></tr></table></p>' . input_hidden_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']]);
  }
  
  function process($options)
  {  
    return $options['value'];
  }
  
  function output($options)
  {      	
    return fields_choices::render_value($options['value']);    
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
    
    $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');
  
    $sql_query[] = $prefix . '.field_' . $filters['fields_id'] .  ($filters['filters_condition']=='include' ? ' in ': ' not in ') .'(' . $filters['filters_values'] . ') ';
    
    return $sql_query;
  }
  
  static function set($entities_id, $items_id)
  {
  	global $sql_query_having, $app_changed_fields, $app_choices_cache;
  	
  	$fields_query = db_query("select * from app_fields where entities_id='" . db_input($entities_id) . "' and type='fieldtype_autostatus'");
  	while($fields = db_fetch_array($fields_query))
  	{  		
  		$cfg = new fields_types_cfg($fields['configuration']);
  		
  		foreach(fields_choices::get_tree($fields['id']) as $choices)
  		{
	  		$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($entities_id). "' and reports_type='fields_choices" . $choices['id'] . "'");
	  		if($reports_info = db_fetch_array($reports_info_query))
	  		{	  				  				  				  				  			
	  			$sql_query_having = array();
	  				  				  				  		
	  			$listing_sql_query = reports::add_filters_query($reports_info['id'],'');
	  			
	  			//prepare having query for formula fields
	  			if(isset($sql_query_having[$entities_id]))
	  			{
	  				$listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$entities_id]);
	  			}
	  			
	  			$item_info_query = db_query("select e.* " . fieldtype_formula::prepare_query_select($entities_id, '')  . fieldtype_related_records::prepare_query_select($entities_id, ''). " from app_entity_" . $entities_id . " e where e.id='" . db_input($items_id) . "' " . $listing_sql_query);
	  			if($item_info = db_fetch_array($item_info_query))
	  			{	  	  					  				
	  				if($choices['id']!=$item_info['field_' . $fields['id']] and $cfg->get('notify_when_changed')==1)
	  				{
	  					$app_changed_fields[] = array(
	  							'name'=>$fields['name'],
	  							'value'=>$app_choices_cache[$choices['id']],
	  							'fields_id'=>$fields['id'],
	  							'fields_value'=>$choices['id'],
	  					);
	  				}
	  				
	  				$sql_data = array(
	  						'field_'.$fields['id'] => $choices['id']  
	  				);
	  				
	  				db_perform('app_entity_' . $entities_id,$sql_data,'update',"id='" . db_input($items_id) . "'");
	  				
	  				//break from current fields choices
	  				break;	  					  				
	  			}	  			
	  		}
  		}
  	}
  	
  	return true;
  }
  
}