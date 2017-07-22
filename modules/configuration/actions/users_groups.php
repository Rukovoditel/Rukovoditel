<?php

switch($app_module_action)
{
  case 'sort':
      if(isset($_POST['sort_items']))
      {
        $sort_order = 0;
        foreach(explode(',',$_POST['sort_items']) as $v)
        {
          db_query("update app_access_groups set sort_order='" . $sort_order . "' where id='" . str_replace('item_','',$v). "'");
          
          $sort_order++;
        }
      }
      exit();
    break;
  case 'save':
    $sql_data = array('name'=>$_POST['name'],
                      'sort_order'=>$_POST['sort_order'],                        
                      'is_default'=>(isset($_POST['is_default']) ? $_POST['is_default']:0),
                      'is_ldap_default'=>(isset($_POST['is_ldap_default']) ? $_POST['is_ldap_default']:0),
                      );
    
    if(isset($_POST['is_default']))
    {
      db_query("update app_access_groups set is_default = 0");
    }
    
    if(isset($_POST['is_ldap_default']))
    {
      db_query("update app_access_groups set is_ldap_default = 0");
    }
    
    if(isset($_GET['id']))
    {        
      db_perform('app_access_groups',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {               
      db_perform('app_access_groups',$sql_data);                  
    }
        
    redirect_to('configuration/users_groups');      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = access_groups::check_before_delete($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = access_groups::get_name_by_id($_GET['id']);
          
          db_delete_row('app_access_groups',$_GET['id']);
          db_delete_row('app_entities_access',$_GET['id'],'access_groups_id');
                              
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
                
        redirect_to('configuration/users_groups');  
      }
    break;   
}