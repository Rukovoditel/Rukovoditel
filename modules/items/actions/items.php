<?php 

switch($app_module_action)
{  
  case 'save':
  
      //checking access
      if(isset($_GET['id']))
      {        
      	$access_rules = new access_rules($current_entity_id, $_GET['id']);
      	
      	if(!users::has_access('update',$access_rules->get_access_schema()))
      	{	
        	redirect_to('dashboard/access_forbidden');
      	}
      }
      elseif(!users::has_access('create') and !isset($_GET['id']))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      //check POST data for user form
      if($current_entity_id==1)
      {      
        require(component_path('items/validate_users_form'));
      }
            
      $fields_values_cache = items::get_fields_values_cache($_POST['fields'],$current_path_array,$current_entity_id);      
      
      $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
            
      $app_send_to = array();             
      $app_send_to_new_assigned = array();
      $app_changed_fields = array();
                                  
      $is_new_item = true;
      $item_info = array();
      
      //get item info for exist item
      if(isset($_GET['id']))
      {      
        $is_new_item = false;          
        $item_info_query = db_query("select * from app_entity_" . $current_entity_id . " where id='" . db_input($_GET['id']) . "'");
        $item_info = db_fetch_array($item_info_query);  
        
        $access_rules = new access_rules($current_entity_id, $item_info);
        $fields_access_schema += $access_rules->get_fields_view_only_access();
      }
      
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
	        //for new item check if there is template field set and use it
	        if(!isset($_GET['id']) and isset($_POST['template_fields'][$field['id']]))
	        {
	          $default_field_value = $_POST['template_fields'][$field['id']];
	        }
	        //for new item check if there is default value and assign it if it's exist          
	        elseif(!isset($_GET['id']) and in_array($field['type'],fields_types::get_types_wich_choices()))
	        {      
	        	$cfg = new fields_types_cfg($field['configuration']);
	        	
	        	if($cfg->get('use_global_list')>0)
	        	{
	        		$check_query = db_query("select id from app_global_lists_choices where lists_id = '" . db_input($cfg->get('use_global_list')). "' and is_default=1");
	        	}
	        	else
	        	{
	        		$check_query = db_query("select id from app_fields_choices where fields_id='" . $field['id'] . "' and is_default=1");
	        	}
	        		          
	          if($check = db_fetch_array($check_query))
	          {
	            $default_field_value = $check['id'];                            
	          }
	          else
	          {
	          	continue;
	          }
	        } 
	        elseif(!isset($_GET['id']) and $field['type']=='fieldtype_user_accessgroups')
	        {
	        	$default_field_value = access_groups::get_default_group_id();        	
	        }
	        elseif(!isset($_GET['id']) and $field['type']=='fieldtype_user_status')
	        {
	        	$default_field_value = 1;
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
                        
      if(isset($_GET['id']))
      {            	      	
      	//update item
        db_perform('app_entity_' . $current_entity_id,$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
        $item_id = $_GET['id'];
        
        //check public form notification 
        //using $item_info as item with previous values 
        if(class_exists('public_forms'))
        {
        	public_forms::send_client_notification($current_entity_id, $item_info);
        }
        
        //sending sms
        if(class_exists('sms'))
        {
        	$modules = new modules('sms');
        	$sms = new sms($current_entity_id, $item_id);
        	$sms->send_to = $app_send_to;
        	$sms->send_edit_msg($item_info);
        }
                                
      }
      else
      { 
        //genreation user password and sending notification for new user
        if($current_entity_id==1)
        {      
          require(component_path('items/crete_new_user'));
        }
        
        $sql_data['date_added'] = time();              
        $sql_data['created_by'] = $app_logged_users_id;
        $sql_data['parent_item_id'] = $parent_entity_item_id;
        db_perform('app_entity_' . $current_entity_id,$sql_data);
        $item_id = db_insert_id();
        
        //sending sms
        if(class_exists('sms'))
        {
        	$modules = new modules('sms');
        	$sms = new sms($current_entity_id, $item_id);
        	$sms->send_to = $app_send_to;
        	$sms->send_insert_msg();
        }
        
      }
      
      
      //insert choices values for fields with multiple values
      $choices_values->process($item_id);
            
      //atuoset fieldtype autostatus
      fieldtype_autostatus::set($current_entity_id, $item_id);
      
      
      //log changeds
      if(class_exists('track_changes'))
      {
      	$log = new track_changes($current_entity_id, $item_id);
      	$log->log_prepare(isset($_GET['id']),$item_info);
      }
      
      //atuocreate comments if fields changed
      if(count($app_changed_fields))
      {
      	comments::add_comment_notify_when_fields_changed($current_entity_id, $item_id,$app_changed_fields);
      }
                  
      /**
       * Start email notification code
       **/
       
      //include sender in notification              
      if(CFG_EMAIL_COPY_SENDER==1)
      {
        $app_send_to[] = $app_user['id'];
      }
      
      //Send notification if there are assigned users and items is new or there is changed fields or new assigned users
      if((count($app_send_to)>0 and !isset($_GET['id'])) or 
         (count($app_send_to)>0 and count($app_changed_fields)>0) or 
          count($app_send_to_new_assigned)>0)
      {                                   
        $breadcrumb = items::get_breadcrumb_by_item_id($current_entity_id, $item_id);
        $item_name = $breadcrumb['text'];
        
        $entity_cfg = new entities_cfg($current_entity_id);
        
        //prepare subject for update itme      
        if(count($app_changed_fields)>0)
        {
          $subject = (strlen($entity_cfg->get('email_subject_updated_item'))>0 ? $entity_cfg->get('email_subject_updated_item') . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_UPDATED_ITEM . ' ' . $item_name);
          
          //add changed field values in subject
          $extra_subject = array();
          foreach($app_changed_fields as $v)
          {
            $extra_subject[] = $v['name'] . ': ' . $v['value']; 
          }
          
          $subject .= ' [' . implode(' | ', $extra_subject) . ']';
          
          $users_notifications_type = 'updated_item';
        }
        else
        {       
          //subject for new item    
          $subject = (strlen($entity_cfg->get('email_subject_new_item'))>0 ? $entity_cfg->get('email_subject_new_item') . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM . ' ' . $item_name);
          
          $users_notifications_type = 'new_item';
        }
        
        //default email heading
        $heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $_POST['path'] . '-' . $item_id,true) . '"><h3>' . $subject . '</h3></a></div>','email_heading_content');
        
        //if only users fields changed then send notification to new assigned users
        if(count($app_changed_fields)==0 and count($app_send_to_new_assigned)>0)
        {
          $app_send_to = $app_send_to_new_assigned;
        }
        
        //start sending email                  
        foreach(array_unique($app_send_to) as $send_to)
        {             	        	        	
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
          
          //change subject for new assigned user
          if(in_array($send_to,$app_send_to_new_assigned))
          {            
            $new_subject = (strlen($entity_cfg->get('email_subject_new_item'))>0 ? $entity_cfg->get('email_subject_new_item') . ' ' . $item_name : TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM . ' ' . $item_name);
            $new_heading = users::use_email_pattern_style('<div><a href="' . url_for('items/info','path=' . $_POST['path'] . '-' . $item_id,true) . '"><h3>' . $new_subject . '</h3></a></div>','email_heading_content');
            
            if(users_cfg::get_value_by_users_id($send_to, 'disable_notification')!=1)
            {
            	users::send_to(array($send_to),$new_subject,$new_heading . $body);
            }
            
            //add users notification
            users_notifications::add($new_subject, 'new_item', $send_to, $current_entity_id, $item_id);
          }
          else          
          {
          	if(users_cfg::get_value_by_users_id($send_to, 'disable_notification')!=1)
            {
            	users::send_to(array($send_to),$subject,$heading . $body);
            }
            
            //add users notification
            users_notifications::add($subject, $users_notifications_type, $send_to, $current_entity_id, $item_id);
          }                                       
        } 
                       
      }
      /**
       * End email notification code
       **/   
       
       
      //set off redirect if add items from calendar reprot
      if(strstr($app_redirect_to,'calendarreport'))
      {
        exit();
      } 
      
      //set off redirect if add items from gantt reprot
      if(strstr($app_redirect_to,'ganttreport'))
      {
      	require(component_path('items/items_form_gantt_submit_prepare'));      	
      	exit();
      }
                      
      //redirect to related item
      if(isset($_POST['related']))
      {   
        $related_array = explode('-',$_POST['related']);
        $related_entities_id = $related_array[0];
        $related_items_id = $related_array[1]; 
        
        $table_info = related_records::get_related_items_table_name($current_entity_id,$related_entities_id);
        
        $sql_data = array('entity_' . $current_entity_id . '_items_id' => $item_id,
        									'entity_' . $related_entities_id . $table_info['sufix'] . '_items_id' => $related_items_id);
        
        db_perform($table_info['table_name'],$sql_data);
            
        $path_info = items::get_path_info($related_entities_id,$related_items_id);
        
        //atuoset fieldtype autostatus
        fieldtype_autostatus::set($related_entities_id, $related_items_id);
                      
        redirect_to('items/info','path=' . $path_info['full_path']); 
      }
                         
      //redirects after adding new item                  
      if(!isset($_GET['id']) and $app_redirect_to=='')
      {
      	$entity_cfg = new entities_cfg($current_entity_id);
      	
      	switch($entity_cfg->get('redirect_after_adding','subentity'))
      	{      		
      		case 'subentity':
      			if($app_user['group_id']==0)
      			{
      				$entity_query = db_query("select * from app_entities where parent_id='" . db_input($current_entity_id) . "' order by sort_order, name limit 1");
      			}
      			else
      			{
      				$entity_query = db_query("select e.* from app_entities e, app_entities_access ea where e.parent_id='" . db_input($current_entity_id) . "' and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' order by e.sort_order, e.name limit 1");
      			}
      			
      			if($entity = db_fetch_array($entity_query))
      			{
      				redirect_to('items/items','path=' . $_POST['path'] . '-' . $item_id . '/' . $entity['id']);
      			}
      			break;
      		case 'info':
      			redirect_to('items/info','path=' . $_POST['path'] . '-' . $item_id);
      			break;
      	}        
      }
      
      $gotopage = '';
      if(isset($_POST['gotopage']))
      {
      	$gotopage = '&gotopage[' . key($_POST['gotopage']). ']=' . current($_POST['gotopage']);
      }
      
      //related records redirect
      related_records::handle_app_redirect();
      
      //other redirects      
      switch($app_redirect_to)
      {
      	case 'parent_item_info_page':      	      		      
      		redirect_to('items/info','path=' . app_path_get_parent_path($app_path));
      		break;
        case 'dashboard':
            redirect_to('dashboard/',substr($gotopage,1));
          break;
        case 'items_info':
            redirect_to('items/info','path=' . $_POST['path']);
          break;
        case 'parent_modal':
        		echo $item_id;
        		exit();
        	break;
        default:
	        	if(strstr($app_redirect_to,'kanban'))
	        	{
	        		redirect_to('ext/kanban/view','id='  . str_replace('kanban','',$app_redirect_to). '&path=' . $app_path);
	        	}
        	  elseif(strstr($app_redirect_to,'item_info_page'))
        	  {
        	  	redirect_to('items/info','path=' . str_replace('item_info_page','',$app_redirect_to));
        	  }	        	  	
            elseif(strstr($app_redirect_to,'report_'))
            {
              redirect_to('reports/view','reports_id=' . str_replace('report_','',$app_redirect_to) . $gotopage);
            }                                      
            else
            {              
              redirect_to('items/items','path=' . $_POST['path'] . $gotopage);
            }  
          break;
      }
      
      
    break;  
  case 'delete':
  		$access_rules = new access_rules($current_entity_id, $_GET['id']);
  		
      if(!users::has_access('delete',$access_rules->get_access_schema()))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      $path_info = items::get_path_info($current_entity_id,$_GET['id']);
                  
      items::delete($current_entity_id, $_GET['id']);
      
      plugins::handle_action('delete_item');
      
      $gotopage = '';
      if(isset($_POST['gotopage']))
      {
      	$gotopage = '&gotopage[' . key($_POST['gotopage']). ']=' . current($_POST['gotopage']);
      }
      
      //related records redirect
      related_records::handle_app_redirect();
      
      switch($app_redirect_to)
      {
      	case 'parent_item_info_page':
      		redirect_to('items/info','path=' . app_path_get_parent_path($app_path));
      		break;
        case 'dashboard':
            redirect_to('dashboard/',substr($gotopage,1));
          break;
        default:
                	
            if(strstr($app_redirect_to,'kanban'))
	        	{
	        		redirect_to('ext/kanban/view','id='  . str_replace('kanban','',$app_redirect_to). '&path=' . $app_path);
	        	}
        	  elseif(strstr($app_redirect_to,'item_info_page'))
        	  {
        	  	redirect_to('items/info','path=' . str_replace('item_info_page','',$app_redirect_to));
        	  }	        	  	
            elseif(strstr($app_redirect_to,'report_'))
            {
              redirect_to('reports/view','reports_id=' . str_replace('report_','',$app_redirect_to) . $gotopage);
            }                                      
            else
            {              
              redirect_to('items/items','path=' . $path_info['path_to_entity'] . $gotopage);
            }  
            
          break;
      }
      
      
    break;
  case 'attachments_upload':    
        $verifyToken = md5($app_user['id'] . $_POST['timestamp']);
          
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
        
        $delete_file_url = url_for('items/items','action=attachments_delete_in_queue&path=' . $_GET['path']);
                     
        echo attachments::render_preview($field_id, $attachments_list,$delete_file_url);
        
      exit();
    break;
 case 'attachments_delete_in_queue':
    	//chck form token
    	app_check_form_token();
    
    	attachments::delete_in_queue($_POST['field_id'], $_POST['filename']);
    
    	exit();
    	break;
    
    
  case 'check_unique':
           
      echo items::check_unique($current_entity_id,_post::int('fields_id'),$_POST['fields_value'],(isset($_GET["id"]) ? $_GET["id"]:false));
      
      exit();
    break;
      
   
}

$entity_info = db_find('app_entities',$current_entity_id);
$entity_cfg = new entities_cfg($current_entity_id);

$entity_listing_heading = (strlen($entity_cfg->get('listing_heading'))>0 ? $entity_cfg->get('listing_heading') : $entity_info['name']);

$app_title = app_set_title($entity_listing_heading);

//create default entity report for logged user
//also reports will be split by paretn item
$reports_info = reports::create_default_entity_report($current_entity_id, 'entity', $current_path_array);
 

