<?php

if($_GET['entities_id']!=1)
{
  redirect_to('entities/entities_configuration','entities_id=1');
}

$cfq_query = db_query("select * from app_configuration where configuration_name='CFG_PUBLIC_USER_PROFILE_FIELDS'");
if(!$cfq = db_fetch_array($cfq_query))
{
  db_perform('app_configuration',array('configuration_value'=>'','configuration_name'=>'CFG_PUBLIC_USER_PROFILE_FIELDS'));
  redirect_to('entities/user_public_profile','entities_id=1');
}

$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list() . "," . fields_types::get_users_types_list(). ") and f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id");
if(!$v = db_fetch_array($fields_query))
{
  $alerts->add(TEXT_USER_PUBLIC_PROFILE_NO_FIELDS,'warning');
}


switch($app_module_action)
{
  case 'sort_fields':
        
        $fields_list = array();
        foreach(explode(',',$_POST['fields_in_profile']) as $v)
        {
          $fields_list[] = str_replace('form_fields_','',$v);            
        }
        
        db_perform('app_configuration',array('configuration_value'=>implode(',',$fields_list)),'update',"configuration_name='CFG_PUBLIC_USER_PROFILE_FIELDS'");
                              
      exit();
    break;
}

$public_user_profile_fields = (strlen(CFG_PUBLIC_USER_PROFILE_FIELDS)==0 ? '0':CFG_PUBLIC_USER_PROFILE_FIELDS);    