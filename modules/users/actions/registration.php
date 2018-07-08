<?php

//check security settings if they are enabled
	app_restricted_countries::verify();
	app_restricted_ip::verify();
	
	if(app_session_is_registered('app_logged_users_id') or CFG_USE_PUBLIC_REGISTRATION==0)
	{
		redirect_to('users/login','action=logoff');
	}

	$app_layout = 'public_layout.php';
	
	$current_entity_id = 1;
	$current_path_array = array(1);
	$app_user = array();
	$app_user['group_id'] = (count(explode(',',CFG_PUBLIC_REGISTRATION_USER_GROUP))==1 ? CFG_PUBLIC_REGISTRATION_USER_GROUP : 0);
	$app_user['id'] = 0;
	$app_user['name'] = CFG_EMAIL_NAME_FROM;
	$app_user['email'] = CFG_EMAIL_ADDRESS_FROM;
	$app_user['language'] = CFG_APP_LANGUAGE;
	
	switch($app_module_action)
	{		
		case 'save':	
			
			//chck form token
			app_check_form_token('users/registration');
			
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
						
					if(in_array($field['type'],fields_types::get_types_wich_choices()))
					{
						$check_query = db_query("select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1");
						if($check = db_fetch_array($check_query))
						{
							$default_field_value = $check['id'];
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
				
				$registration_user_group_id = (isset($_POST['fields'][6]) ? $_POST['fields'][6] : false);
			
				$sql_data['date_added'] = time();
				$sql_data['created_by'] = 0;
				$sql_data['parent_item_id'] = 0;
				$sql_data['field_6'] = ($registration_user_group_id>0 ? $registration_user_group_id :  (int)CFG_PUBLIC_REGISTRATION_USER_GROUP); //access group
				$sql_data['field_5'] = 1; //status
				$sql_data['field_14'] = CFG_APP_SKIN;
				
				db_perform('app_entity_' . $current_entity_id,$sql_data);
				$item_id = db_insert_id();
										
				//insert choices values for fields with multiple values
				$choices_values->process($item_id);
				
				//send notification to users
				if(strlen(CFG_REGISTRATION_NOTIFICATION_USERS))
				{
					$app_send_to = explode(',',CFG_REGISTRATION_NOTIFICATION_USERS);
					
					$breadcrumb = items::get_breadcrumb_by_item_id($current_entity_id, $item_id);
					$item_name = $breadcrumb['text'];
					
					$entity_cfg = new entities_cfg($current_entity_id);
					
					//subject for new item
					$subject = (strlen($entity_cfg->get('email_subject_new_item'))>0 ? $entity_cfg->get('email_subject_new_item') . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM . ' ' . $item_name);
						
					 
					//Send notification if there are assigned users and items is new or there is changed fields or new assigned users
					if(count($app_send_to)>0)
					{												
						$users_notifications_type = 'new_item';
							
						//default email heading
						$heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $current_entity_id . '-' . $item_id,true) . '"><h3>' . $subject . '</h3></a></div>','email_heading_content');
					
						//start sending email
						foreach(array_unique($app_send_to) as $send_to)
						{
							//prepare body
							//prepare body
							if($entity_cfg->get('item_page_details_columns','2')==1)
							{
								$body = users::use_email_pattern('single_column',array('email_single_column'=>items::render_info_box($current_entity_id,$item_id,$send_to, false)));
							}
							else
							{
								$body = users::use_email_pattern('single',array('email_body_content'=>items::render_content_box($current_entity_id,$item_id,$send_to),'email_sidebar_content'=>items::render_info_box($current_entity_id,$item_id,$send_to)));
							}
					
							//echo $subject . $body;
							//exit();
										
							if(users_cfg::get_value_by_users_id($send_to, 'disable_notification')!=1)
							{
								users::send_to(array($send_to),$subject,$heading . $body);								
							}
								
							//add users notification
							users_notifications::add($subject, $users_notifications_type, $send_to, $current_entity_id, $item_id);
					
						}
					}
				}
				
				//sending sms
				if(class_exists('sms'))
				{				
					$modules = new modules('sms');
					$sms = new sms($current_entity_id, $item_id);
					$sms->send_to = $app_send_to;
					$sms->send_insert_msg();
				}				
				
				//log changeds
				if(class_exists('track_changes'))
				{
					$log = new track_changes($current_entity_id, $item_id);
					$log->log_insert();
				}
		
				users::login($_POST['fields'][12],$password,0);
			}
			break;
			
			case 'attachments_upload':
				$verifyToken = md5($app_session_token . $_POST['timestamp']);
					
				if(strlen($_FILES['Filedata']['tmp_name']) and $_POST['token'] == $verifyToken)
				{
					$file = attachments::prepare_filename($_FILES['Filedata']['name']);
						
					if(move_uploaded_file($_FILES['Filedata']['tmp_name'], DIR_WS_ATTACHMENTS  . $file['folder']  .'/'. $file['file']))
					{
						//autoresize images if enabled
						attachments::resize(DIR_WS_ATTACHMENTS  . $file['folder']  .'/'. $file['file']);
							
						//add attachments to tmp table
						$sql_data = array('form_token'=>$verifyToken,'filename'=>$file['name'],'date_added'=>date('Y-m-d'),'container'=>$_GET['field_id']);
						db_perform('app_attachments',$sql_data);
						
						//add file to queue
						if(class_exists('file_storage'))
						{
							$file_storage = new file_storage();
							$file_storage->add_to_queue($_GET['field_id'], $file['name']);
						}
							
					}
				}
				exit();
				break;
			
			case 'attachments_preview':
				$field_id = $_GET['field_id'];
					
				$attachments_list = $uploadify_attachments[$field_id];
					
				//get new attachments
				$attachments_query = db_query("select filename from app_attachments where form_token='" . db_input($_GET['token']). "' and container='" . db_input($_GET['field_id']) . "'");
				while($attachments = db_fetch_array($attachments_query))
				{
					$attachments_list[] = $attachments['filename'];
					
					if(!in_array($attachments['filename'],$uploadify_attachments_queue[$field_id])) $uploadify_attachments_queue[$field_id][] = $attachments['filename'];
					
				}
				
				$delete_file_url = url_for('users/registration','action=attachments_delete_in_queue');
					
				echo attachments::render_preview($field_id, $attachments_list,$delete_file_url,$delete_file_url);
					
				exit();
				break;
			case 'attachments_delete_in_queue':
				//chck form token
				app_check_form_token();
			
				attachments::delete_in_queue($_POST['field_id'], $_POST['filename']);
			
				exit();
				break;
			
			case 'check_unique':
					
				//chck form token
				app_check_form_token();
			
				echo items::check_unique(_get::int('entities_id'),_post::int('fields_id'),$_POST['fields_value']);
					
				exit();
				break;			
	}	