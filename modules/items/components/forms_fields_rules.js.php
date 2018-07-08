<?php
$form_fields_query = db_query("select r.*, f.name, f.id as fields_id, f.type from app_forms_fields_rules r, app_fields f where f.type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_user_accessgroups') and r.fields_id=f.id and r.entities_id='" . $current_entity_id . "'");

if(db_num_rows($form_fields_query)>0)
{	
	$html = '';
	
	$rules_for_fields = array();
	
	while($form_fields = db_fetch_array($form_fields_query))
	{		
		if(strlen($form_fields['visible_fields']) and strlen($form_fields['choices']))
		{
			$html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="visible" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['visible_fields'] . '">';
		}
		
		if(strlen($form_fields['hidden_fields']) and strlen($form_fields['choices']))
		{
			$html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="hidden" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['hidden_fields'] . '">';
		}
				
		$rules_for_fields[$form_fields['fields_id']] = $form_fields['type'];		
	}
	
//include form rules if form exist	
if(isset($app_items_form_name))
{	
	$html .= '
		<script>
			$(function(){
			';
	foreach($rules_for_fields as $fields_id=>$fields_type)
	{
		$html .= '
			$(".field_' . $fields_id . '").change(function(){
				app_handle_forms_fields_display_rules(' . $fields_id . ',\'' . $fields_type . '\',\'\')
			})	
			
			app_handle_forms_fields_display_rules(' . $fields_id . ',\'' . $fields_type . '\',\'\')
		';
	}
	
	$html .= '
			})
		</script>
			';
}
	
	echo $html;
	
}