<?php

class fieldtype_parent_value
{
	public $options;

	function __construct()
	{				
		$this->options = array('title' => TEXT_FIELDTYPE_PARENT_VALUE_TITLE);
	}

	function get_configuration()
	{
		$cfg = array();
		
		$entities_id = _post::int('entities_id');
		
		$entities_info = db_find('app_entities',$entities_id);
		
		$choices = array();
		
		if($entities_info['parent_id']>0)
		{
			$choices = array(''=>'');
			$reserverd_fields_types = array_merge(fields_types::get_reserved_data_types(),fields_types::get_users_types());
			$reserverd_fields_types_list = "'" . implode("','", $reserverd_fields_types). "'";
			
			$fields_query = db_query("select f.*, t.name as tab_name, if(f.type in (" . $reserverd_fields_types_list . "),-1,t.sort_order) as tab_sort_order from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action','fieldtype_parent_item_id') and f.entities_id='" . $entities_info['parent_id'] . "' and f.forms_tabs_id=t.id order by tab_sort_order, t.name, f.sort_order, f.name");
			while($fields = db_fetch_array($fields_query))
			{
				$choices[$fields['tab_name']][$fields['id']] = (strlen($fields['name']) ? $fields['name']:fields_types::get_title($fields['type']));
			}				
		}
						
		$cfg[] = array(
				'title'=>TEXT_SELECT_FIELD,
				'name'=>'field_id',
				'type'=>'dropdown',
				'choices'=>$choices,				
				'params'=>array('class'=>'form-control input-large required'));
		 				
		return $cfg;
	}

	function render($field,$obj,$params = array())
	{
		return false;
	}

	function process($options)
	{
		return false;
	}

	function output($options)
	{
		global $parent_items_values_cache;
		
		$html = '';
		$entities_id = $options['field']['entities_id'];
		$parent_item_id = $options['item']['parent_item_id'];
		
		$entities_info = db_find('app_entities',$entities_id);
		
		if($parent_item_id>0 and $entities_info['parent_id']>0)
		{
			//prepare query cache
			if(!isset($parent_items_values_cache[$parent_item_id]))
			{
				$select_fields = array();
				$fields_query = db_query("select id, configuration from app_fields where entities_id='" . db_input($entities_id) . "' and type='fieldtype_parent_value'");
				while($fields = db_fetch_array($fields_query))
				{
					$cfg = new fields_types_cfg($fields['configuration']);
					
					if(strlen($cfg->get('field_id')))
					{
						$select_fields[] = $cfg->get('field_id');
					}
				}
				
				if(count($select_fields))
				{
					$paretn_item_info_query = db_query("select e.* " . fieldtype_formula::prepare_query_select($entities_info['parent_id'], '',false,array('fields_in_listing'=>implode(',',$select_fields))) . " from app_entity_" . $entities_info['parent_id'] . " e  where e.id='" . db_input($parent_item_id) . "'");
					if($paretn_item_info = db_fetch_array($paretn_item_info_query))
					{
						$parent_items_values_cache[$parent_item_id] = $paretn_item_info;
					}
				}
			}
			
			//output field value
			if(isset($parent_items_values_cache[$parent_item_id]))
			{
				$item = $parent_items_values_cache[$parent_item_id];
				
				$cfg = new fields_types_cfg($options['field']['configuration']);
				
				if(strlen($cfg->get('field_id')))
				{
					$field = db_find('app_fields',$cfg->get('field_id'));
					
					//prepare field value
					$value = items::prepare_field_value_by_type($field, $item);
					
					$output_options = array(
							'class'       => $field['type'],
							'value'       => $value,
							'field'       => $field,
							'item'        => $item,
							'is_listing'  => true,
							'redirect_to' => '',
							'reports_id'  => 0,
							'path'        => $entities_info['parent_id'],							
					);
					
					$html = fields_types::output($output_options);
				}
			}
		}
				
		return $html;
	}
}