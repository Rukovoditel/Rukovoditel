<?php

class forms_fields_rules
{
	static function prepare_hidden_fields($entity_id,$item,$fields_access_schema)
	{
		global $app_module_path;
		
		$html = '';
		$form_fields_query = db_query("select r.* from app_forms_fields_rules r where r.entities_id='" . $entity_id . "' group by r.fields_id");
		while($v = db_fetch_array($form_fields_query))
		{
			//check if there is limited access or field ID is 6 (user group)
			if(isset($fields_access_schema[$v['fields_id']]) or ($v['fields_id']==6 and $app_module_path=='users/account'))
			{
				$html .= input_hidden_tag('fields[' . $v['fields_id']. ']',$item['field_' . $v['fields_id']]);
			}			
		}
				
		return $html;
	}
}