<?php

//check security settings if they are enabled
	app_restricted_countries::verify();
	app_restricted_ip::verify();
	
	if(app_session_is_registered('app_logged_users_id') or CFG_USE_PUBLIC_REGISTRATION==0)
	{
		redirect_to('users/login','action=logoff');
	}

	$app_layout = 'registration_layout.php';
	
	$current_entity_id = 1;
	$current_path_array = array(1);
	$app_user = array();
	$app_user['group_id'] = CFG_PUBLIC_REGISTRATION_USER_GROUP;
	
	switch($app_module_action)
	{		
		case 'save':	

			$is_error = false;
			
			//check reaptcha
			if(app_recaptcha::is_enabled())
			{
				if(!app_recaptcha::verify())
				{
					$alerts->add(TEXT_RECAPTCHA_VERIFY_ROBOT,'error');
					
					$is_error = true;
				}
			}
			
			if(!$is_error)
			{	
				//check POST data for user form
				if($current_entity_id==1)
				{
					require(component_path('items/validate_users_form'));
				}
				
				$fields_values_cache = items::get_fields_values_cache($_POST['fields'],$current_path_array,$current_entity_id);
				
				$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
							
				$is_new_item = true;
				$item_info = array();
							
				//prepare item data
				$sql_data = array();
				
				$choices_values = new choices_values($current_entity_id);
				
				$fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ",'fieldtype_related_records') and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
				while($field = db_fetch_array($fields_query))
				{
					$default_field_value = '';
				
					//check field access and skip fields without access
					if(isset($fields_access_schema[$field['id']]))
					{
						if(!isset($_GET['id']) and in_array($field['type'],fields_types::get_types_wich_choices()))
						{
							$check_query = db_query("select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1");
							if($check = db_fetch_array($check_query))
							{
								$default_field_value = $check['id'];
							}
							else
							{
								continue;
							}
						}
						else
						{
							continue;
						}
					}
				
					//submited field value
					$value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : $default_field_value);
					 
					//current field value
					$current_field_value = (isset($item_info['field_' . $field['id']]) ? $item_info['field_' . $field['id']] : '');
				
					//prepare process options
					$process_options = array('class'          => $field['type'],
							'value'          => $value,
							'fields_cache'   => $fields_values_cache,
							'field'          => $field,
							'is_new_item'    => $is_new_item,
							'current_field_value' => $current_field_value,
					);
				
					$sql_data['field_' . $field['id']] = fields_types::process($process_options);
				
					//prepare choices values for fields with multiple values
					$choices_values->prepare($process_options);
				}
				
		
				//genreation user password and sending notification for new user
				if($current_entity_id==1)
				{
					require(component_path('items/crete_new_user'));
				}
			
				$sql_data['date_added'] = time();
				$sql_data['created_by'] = 0;
				$sql_data['parent_item_id'] = 0;
				$sql_data['field_6'] = CFG_PUBLIC_REGISTRATION_USER_GROUP; //access group
				$sql_data['field_5'] = 1; //status
				$sql_data['field_14'] = CFG_APP_SKIN;
				
				db_perform('app_entity_' . $current_entity_id,$sql_data);
				$item_id = db_insert_id();
										
				//insert choices values for fields with multiple values
				$choices_values->process($item_id);
		
				users::login($_POST['fields'][12],$password,0);
			}
			break;
	}	