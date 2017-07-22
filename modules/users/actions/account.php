<?php
$current_entity_id = 1;

$user_query = db_query("select * from app_entity_1 where id='" . db_input($app_logged_users_id) . "' and field_5=1");
$obj = db_fetch_array($user_query);

switch($app_module_action)
{
  case 'set_cfg':
        if(isset($_POST['key']) and isset($_POST['value']))
        {
          $app_users_cfg->set($_POST['key'],$_POST['value']);
        }
      exit();
    break;
  case 'update':
  
      $msg = array();
      
      //check POST data for user form
      if(strlen($_POST['fields'][9])==0)
      {
        $msg[] = TEXT_ERROR_USEREMAL_EMPTY;
      }
      
      if(CFG_ALLOW_CHANGE_USERNAME==1)
      {
        if(strlen($_POST['fields'][12])==0)
        {
          $msg[] = TEXT_ERROR_USERNAME_EMPTY;
        }
      }
      
      if(strlen($_POST['fields'][9])>0 and CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL==0)
      {
        $check_query = db_query("select count(*) as total from app_entity_1 where field_9='" . db_input($_POST['fields'][9]) . "'  and id!='" . db_input($app_logged_users_id) . "'");
        $check = db_fetch_array($check_query);
        if($check['total']>0)
        {
          $msg[] = TEXT_ERROR_USEREMAL_EXIST;
        }
      }
      
      if(CFG_ALLOW_CHANGE_USERNAME==1)
      {
        if(strlen($_POST['fields'][12])>0)
        {
          $check_query = db_query("select count(*) as total from app_entity_1 where field_12='" . db_input($_POST['fields'][12]) . "'  and id!='" . db_input($app_logged_users_id) . "'");
          $check = db_fetch_array($check_query);
          if($check['total']>0)
          {
            $msg[] = TEXT_ERROR_USERNAME_EXIST;
          }
        }
      }
      
      if(count($msg)>0)
      {                
        foreach($msg as $v)
        {
          $alerts->add($v,'error');
        }
      
        redirect_to('users/account'); 
      }
            
      $fields_values_cache = items::get_fields_values_cache($_POST['fields'],array($current_entity_id),$current_entity_id);      
      
      $sql_data = array();
      
      
      $excluded_fileds_types = "'fieldtype_user_accessgroups','fieldtype_user_status','fieldtype_user_skin'";
                          
      if(CFG_ALLOW_CHANGE_USERNAME==0)
      {
        $excluded_fileds_types .= ",'fieldtype_user_username'";
      }
                              
      $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
      while($field = db_fetch_array($fields_query))
      {
        $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');
        
        $process_potions = array('class'=>$field['type'],'value'=>$value,'fields_cache'=>$fields_values_cache, 'field'=>$field);
        
        $sql_data['field_' . $field['id']] = fields_types::process($process_potions);
      }   
      
      db_perform('app_entity_' . $current_entity_id,$sql_data,'update',"id='" . db_input($app_logged_users_id) . "'");
      
      //set user configuration options
      $cfg = array('disable_notification','disable_internal_notification','disable_highlight_unread');
      foreach($cfg as $key)
      {      		
      	$app_users_cfg->set($key,(isset($_POST['cfg'][$key]) ? $_POST['cfg'][$key] : ''));
      }
            
      $alerts->add(TEXT_ACCOUNT_UPDATED,'success');
      
      redirect_to('users/account');

    break;

}
