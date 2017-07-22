<?php

switch($app_module_action)
{
  case 'set_access':
                  
        if(isset($_POST['access']))
        {
          
          foreach($_POST['access'] as $access_groups_id=>$fields)
          {
            foreach($fields as $id=>$access)
            {
              if(in_array($access,array('view','hide')))
              {
                $sql_data = array('access_schema'=>$access);
              
                $acess_info_query = db_query("select access_schema from app_fields_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . db_input($access_groups_id) . "' and fields_id='" . db_input($id) . "'");
                if($acess_info = db_fetch_array($acess_info_query))
                {
                  db_perform('app_fields_access',$sql_data,'update',"entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . db_input($access_groups_id). "'  and fields_id='" . db_input($id) . "'");
                }
                else
                {
                  $sql_data['entities_id'] = $_GET['entities_id'];
                  $sql_data['access_groups_id'] = $access_groups_id;
                  $sql_data['fields_id'] = $id;
                  db_perform('app_fields_access',$sql_data);
                }
              }
              else
              {
                db_query("delete from app_fields_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . db_input($access_groups_id). "'  and fields_id='" . db_input($id) . "'");
              }
            }                                                                                
          }
          
          $alerts->add(TEXT_ACCESS_UPDATED,'success');
        }
                        
      redirect_to('entities/fields_access','entities_id=' . $_GET['entities_id'] . '&ui_accordion_active=' . $_POST['ui_accordion_active']);
    break;
}