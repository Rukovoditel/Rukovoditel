<?php

$fields_info_query = db_query("select * from app_fields where id='" . db_input($_GET['fields_id']) . "'");
if(!$fields_info = db_fetch_array($fields_info_query))
{
  redirect_to('entities/fields','entities_id=' . $_GET['entities_id']);
}

switch($app_module_action)
{
  case 'save':
      $fields_configuration = $_POST['fields_configuration'];
      
      switch($fields_info['type'])
      {
        case 'fieldtype_related_records':
            if(isset($_POST['fields_in_listing']))
            {
              $fields_configuration['fields_in_listing'] = implode(',',$_POST['fields_in_listing']);
            }
            else
            {
              $fields_configuration['fields_in_listing'] = '';
            } 
            
            if(isset($_POST['fields_in_popup']))
            {
              $fields_configuration['fields_in_popup'] = implode(',',$_POST['fields_in_popup']);
            }
            else
            {
              $fields_configuration['fields_in_popup'] = '';
            }  
          break;
          
        case 'fieldtype_entity':
            
            if(isset($_POST['fields_in_popup']))
            {
              $fields_configuration['fields_in_popup'] = implode(',',$_POST['fields_in_popup']);
            }
            else
            {
              $fields_configuration['fields_in_popup'] = '';
            }  
          break;
      }
            
      db_query("update app_fields set configuration='" . db_input(fields_types::prepare_configuration($fields_configuration)) . "' where id='" . db_input($fields_info['id']) . "'");
      
      $alerts->add(TEXT_CONFIGURATION_UPDATED,'success');
      
      redirect_to('entities/fields','entities_id=' . $_GET['entities_id']);
    break;
}