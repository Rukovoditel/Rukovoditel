<?php

$fields_query = db_query("select * from app_fields where id='" . _get::int('fields_id') . "'");
if($fields = db_fetch_array($fields_query))
{	
	$cfg = new fields_types_cfg($fields['configuration']);
	
	$item_id = _get::int('item_id');
	$parent_entity_item_id = _get::int('parent_entity_item_id');
	
		
	$obj = array();
			
	if($cfg->get('display_as')=='dropdown_muliple' and $_GET['current_field_values']!='null')
	{
		$obj['field_' . $fields['id']] = $_GET['current_field_values'] . ',' . _get::int('item_id');
	}
	else
	{
		$obj['field_' . $fields['id']] = _get::int('item_id');
	}
				
	echo fields_types::render($fields['type'],$fields,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item'));
	
	echo '<script>appHandleChosen(); app_handle_submodal_open_btn();</script>';
}

exit();