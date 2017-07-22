<?php

switch($app_module_action)
{

  case 'save':
      //checking access
      if(isset($_GET['id']) and !users::has_comments_access('update'))
      {        
        redirect_to('dashboard/access_forbidden');
      }
      elseif(!users::has_comments_access('create'))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      $entity_cfg = new entities_cfg($current_entity_id);
      
      $attachments = (isset($_POST['fields']['attachments']) ? $_POST['fields']['attachments'] : '');
      
            
      if(isset($_GET['is_quick_comment']))
      {
      	$description = $_POST['quick_comments_description'];
      }
      else
      {
      	$description = $_POST['description'];
      }
      
      if(isset($_GET['is_quick_comment']) and $entity_cfg->get('use_editor_in_comments')==1)
      {
      	$description = nl2br($description);
      }
      
      $sql_data = array('description'=>db_prepare_html_input($description),
                        'entities_id'=>$current_entity_id,                        
                        'items_id'=>$current_item_id,
                        'attachments'=>fields_types::process(array('class'=>'fieldtype_attachments','value'=>$attachments)),      
                        );
      
      if(isset($_GET['id']))
      {        
        db_perform('app_comments',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");            
      }
      else
      {    
        $sql_data['date_added'] = time();
        $sql_data['created_by'] = $app_user['id'];
           
        db_perform('app_comments',$sql_data);
        
        $comments_id = db_insert_id();  
        
        //update fields in comments form if they are exist
        if(isset($_POST['fields']))
        {
          $fields_values_cache = items::get_fields_values_cache($_POST['fields'],$current_path_array,$current_entity_id);      
          
          $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
          
          $sql_data = array();
                                  
          $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.comments_status = 1 order by f.comments_sort_order, f.name");
          while($field = db_fetch_array($fields_query))
          {
            //check field access
            if(isset($fields_access_schema[$field['id']])) continue;
            
            $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');
            
            $process_options = array('class'=>$field['type'],
                                     'value'=>$value,
                                     'fields_cache'=>$fields_values_cache, 
                                     'field'=>$field,
                                     'is_new_item'=>false,
                                     'current_field_value'=>'');
            
            $fields_value = fields_types::process($process_options);
             
            if(strlen($fields_value)>0)
            {                             
              //insert comment history
              db_perform('app_comments_history',array('comments_id'=>$comments_id,'fields_id'=>$field['id'],'fields_value'=>$fields_value));
              
              if($field['type']=='fieldtype_input_numeric_comments')
              {
                $filed_type = new $field['type'];
                $sql_data['field_' . $field['id']] = $filed_type->get_fields_sum($current_entity_id,$current_item_id,$field['id']);
              }
              else
              {
                $sql_data['field_' . $field['id']] = $fields_value;
                
                //update choices values
                $choices_values = new choices_values($current_entity_id);                                                
                $choices_values->process_by_field_id($current_item_id,$field['id'],$field['type'],$fields_value);

              }
              
            }
          }
          
          if(count($sql_data)>0)
          {
            db_perform('app_entity_' . $current_entity_id,$sql_data,'update',"id='" . db_input($current_item_id) . "'");
          }                             
        } 
        
        
        //send notificaton
        app_send_new_comment_notification($comments_id,$current_item_id,$current_entity_id);
                        
      }
      
      redirect_to('items/info','path=' . $_POST['path']);      
    break;
  case 'delete':
      if(!users::has_comments_access('delete'))
      {
        redirect_to('dashboard/access_forbidden');
      }
      
      if(isset($_GET['id']))
      {     
        attachments::delete_comments_attachments($_GET['id']);
                       
        db_delete_row('app_comments',$_GET['id']);
        
        db_query("delete from app_comments_history where comments_id = '" . db_input($_GET['id']) . "'");
        
        fields_types::recalculate_numeric_comments_sum($current_entity_id,$current_item_id);
                        
        $alerts->add(TEXT_COMMENT_WAS_DELETED,'success');
        
        redirect_to('items/info','path=' . $_GET['path']);  
      }
    break;    
}