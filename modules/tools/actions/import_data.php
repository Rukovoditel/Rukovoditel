<?php

  if (!app_session_is_registered('import_fields')) 
  {
    $import_fields = array();
    app_session_register('import_fields');    
  } 

  switch($app_module_action)
  {   
    case 'import':
        $worksheet = json_decode(stripslashes($_POST['worksheet']),true);
        $entities_id = $_POST['entities_id'];
        $parent_item_id = $_POST['parent_item_id'];
        
        //check if any fields are binded
        if(count($import_fields)==0)
        {        	
        	$alerts->add(TEXT_IMPORT_BIND_FIELDS_ERROR,'error');
        	redirect_to('tools/import_data');
        }
        
        //check required fields for users entity
        if($entities_id==1)
        {
        	if(!in_array(7,$import_fields) or !in_array(8,$import_fields) or !in_array(9,$import_fields))
        	{
        		$alerts->add(TEXT_IMPORT_BIND_USERS_FIELDS_ERROR,'error');
        		redirect_to('tools/import_data');
        	}      
        	
        	$hasher = new PasswordHash(11, false);
        }	
        
        //check if import first row
        $first_row  = (isset($_POST['import_first_row']) ? 0:1);
        
        //use when import users
        $already_exist_username = array();
        
        //start import
        for ($row = $first_row; $row < count($worksheet); ++$row)
        {        
        	//start build item sql data
        	$sql_data = Array();
        
        	$choices_values = array();
        	
        	$email_username = '';
        	$import_username = '';
        	 
        	for ($col = 0; $col <= count($worksheet[$row]); ++$col)
        	{        		        	
        		if(isset($import_fields[$col]) and strlen($worksheet[$row][$col])>0)
        		{
        			$field_id = $import_fields[$col];
        			$filed_info_query = db_query("select * from app_fields where id='" . db_input($field_id). "'");
        			if($filed_info = db_fetch_array($filed_info_query))
        			{
        				 
        				$cfg = new fields_types_cfg($filed_info['configuration']);
        				 
        				switch($filed_info['type'])
        				{
        					case 'fieldtype_user_email':
        							$value = trim($worksheet[$row][$col]);
        							$email_username = substr($value,0,strpos($value,'@'));
        							
        							$sql_data['field_' . $field_id] = $value;
        						break;
        					case 'fieldtype_user_username':
        							$value = trim($worksheet[$row][$col]);
        							$import_username = $value;
        							
        							$sql_data['field_' . $field_id] = $value;
        						break;
        					case 'fieldtype_dropdown':
        					case 'fieldtype_radioboxes':
        						$value = trim($worksheet[$row][$col]);
        
        						if($cfg->get('use_global_list')>0)
        						{
        							$fields_choices_info_query = db_query("select * from app_global_lists_choices where name='" . db_input($value) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'");
        							if($fields_choices_info = db_fetch_array($fields_choices_info_query))
        							{
        								$sql_data['field_' . $field_id] = $fields_choices_info['id'];
        							}
        							else
        							{
        								$field_sql_data = array('lists_id'=>$cfg->get('use_global_list'),
        										'parent_id'=>0,
        										'name'=>$value);
        								db_perform('app_global_lists_choices',$field_sql_data);
        								 
        								$item_id = db_insert_id();
        								 
        								$sql_data['field_' . $field_id] = $item_id;
        							}
        						}
        						else
        						{
        							$fields_choices_info_query = db_query("select * from app_fields_choices where name='" . db_input($value) . "' and fields_id='" . db_input($field_id) . "'");
        							if($fields_choices_info = db_fetch_array($fields_choices_info_query))
        							{
        								$sql_data['field_' . $field_id] = $fields_choices_info['id'];
        							}
        							else
        							{
        								$field_sql_data = array('fields_id'=>$field_id,
        										'parent_id'=>0,
        										'name'=>$value);
        								db_perform('app_fields_choices',$field_sql_data);
        								 
        								$item_id = db_insert_id();
        								 
        								$sql_data['field_' . $field_id] = $item_id;
        							}
        						}
        
        						//prepare choices values
        						$choices_values[$field_id][] = $sql_data['field_' . $field_id];
        
        						break;
        					case 'fieldtype_dropdown_multiple':
        					case 'fieldtype_checkboxes':
        						$values_list = array();
        						$value = trim($worksheet[$row][$col]);
        
        						if($cfg->get('use_global_list')>0)
        						{
        							foreach(explode(',',$value) as $value_name)
        							{
        								$fields_choices_info_query = db_query("select * from app_global_lists_choices where name='" . db_input(trim($value_name)) . "' and lists_id='" . db_input($cfg->get('use_global_list')) . "'");
        								if($fields_choices_info = db_fetch_array($fields_choices_info_query))
        								{
        									$values_list[] = $fields_choices_info['id'];
        								}
        								else
        								{
        									$field_sql_data = array('lists_id'=>$cfg->get('use_global_list'),
        											'parent_id'=>0,
        											'name'=>trim($value_name));
        									db_perform('app_global_lists_choices',$field_sql_data);
        
        									$item_id = db_insert_id();
        
        									$values_list[] = $item_id;
        								}
        							}
        						}
        						else
        						{
        							foreach(explode(',',$value) as $value_name)
        							{
        								$fields_choices_info_query = db_query("select * from app_fields_choices where name='" . db_input(trim($value_name)) . "' and fields_id='" . db_input($field_id) . "'");
        								if($fields_choices_info = db_fetch_array($fields_choices_info_query))
        								{
        									$values_list[] = $fields_choices_info['id'];
        								}
        								else
        								{
        									$field_sql_data = array('fields_id'=>$field_id,
        											'parent_id'=>0,
        											'name'=>trim($value_name));
        									db_perform('app_fields_choices',$field_sql_data);
        									 
        									$item_id = db_insert_id();
        									 
        									$values_list[] = $item_id;
        								}
        							}
        						}
        
        						//prepare choices values
        						$choices_values[$field_id] = $values_list;
        
        						$sql_data['field_' . $field_id] = implode(',',$values_list);
        
        						break;
        					case 'fieldtype_input_date':
        					case 'fieldtype_input_datetime':
        						$sql_data['field_' . $field_id] = strtotime($worksheet[$row][$col]);
        						break;
        					default:
        						$sql_data['field_' . $field_id] = $worksheet[$row][$col];
        						break;
        				}
        			}
        		}
        	}
        	
        	//if import users then set required fields for users entity
        	if($entities_id==1)
        	{
        		$sql_data['field_6'] = $_POST['users_group_id'];
        		$sql_data['field_5'] = 1;
        		$sql_data['field_13'] = CFG_APP_LANGUAGE;
        		$sql_data['field_14'] = 'default';
        		
        		if(strlen($import_username)==0)
        		{
        			$sql_data['field_12'] = $email_username;
        		}
        		
        		if(isset($_POST['set_pwd_as_username']))
        		{
        			$password = (strlen($import_username)>0 ? $import_username:$email_username);
        		}
        		else
        		{
        			$password = users::get_random_password();
        		}
        		
        		$sql_data['password'] = $hasher->HashPassword($password);
        		
        		$check_query = db_query("select count(*) as total from app_entity_1 where field_12='" . db_input($sql_data['field_12']) . "'");
        		$check = db_fetch_array($check_query);
        		if($check['total']>0)
        		{
        			$already_exist_username[] = $sql_data['field_12'];
        			continue;
        		}
        		        		
        	}
					
        	//set other values
        	$sql_data['date_added'] = time();
        	$sql_data['created_by'] = $app_logged_users_id;
        	$sql_data['parent_item_id'] = (int)$parent_item_id;
        
        	//echo '<pre>';
        	//print_r($sql_data);
        
        	db_perform('app_entity_' . $entities_id,$sql_data);
        
        	$item_id = db_insert_id();
        
        	//insert choices values if exist
        	if(count($choices_values)>0)
        	{
        		foreach($choices_values as $field_id=>$values)
        		{
        			foreach($values as $value)
        			{
        				db_query("INSERT INTO app_entity_" . $entities_id . "_values (items_id, fields_id, value) VALUES ('" . $item_id . "', '" . $field_id . "', '" . $value . "');");
        			}
        
        		}
        	}
        
        }  
        
        
        //exit();
        
        if(count($already_exist_username)>0)
        {
        	$alerts->add(TEXT_USERS_IMPORT_ERROR . ' ' . implode(', ',$already_exist_username),'warning');
        }
                                
        $entity_info = db_find('app_entities',$entities_id);
                         
        if($entity_info['parent_id']>0)
        {                  
          $parent_item_query = db_query("select * from app_entity_" . $entity_info['parent_id'] . " where id='" . db_input($parent_item_id) . "'");
          
          if($parent_item = db_fetch_array($parent_item_query))
          {
            $path_info = items::get_path_info($entity_info['parent_id'],$parent_item['id']);
            
            redirect_to('items/items','path=' . $path_info['full_path'] . '/' . $entities_id); 
          }
        }
        else
        {
          redirect_to('items/items','path=' . $entities_id);
        }        
        
        exit();
      break; 
    case 'bind_field':
        $col = $_POST['col'];
        $filed_id = $_POST['filed_id'];
        
        if($filed_id>0)
        {
          $import_fields[$col] = $filed_id;
          
          $v = db_find('app_fields',$filed_id);
          
          echo fields_types::get_option($v['type'],'name',$v['name']);
        }
        elseif(isset($import_fields[$col]))
        {
          unset($import_fields[$col]);
          echo '';
        } 
        
        exit();
      break;
    case 'set_parent_item_id':
        $entity_id = $_POST['entity_id'];
        
        $entity_info = db_find('app_entities',$entity_id);
        
        if($entity_info['parent_id']>0)
        {
        	$items_search = new items_search($entity_info['parent_id']);        
        	$choices = array(''=>'')+$items_search->get_choices();
        	
          $parent_entity_info = db_find('app_entities',$entity_info['parent_id']);
          
          echo '
           <div class="form-group">
          	<label class="col-md-3 control-label" for="name">' . $parent_entity_info['name'] . '</label>
            <div class="col-md-9">	
          	  ' .  select_tag('parent_item_id',$choices,'',array('class'=>'form-control input-xlarge required chosen-select'))  . input_hidden_tag('paren_entity_id',$entity_info['parent_id']). '              
            </div>			
          </div>              
          ';
        }
        
        exit();
      break;
  }