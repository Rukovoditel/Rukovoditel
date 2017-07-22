<?php

define('TEXT_UPDATE_VERSION_FROM','1.6');
define('TEXT_UPDATE_VERSION_TO','1.7');


if(isset($_GET['entity_id']))
{
  require('../../config/database.php');
          
  include('includes/database.php');
      
  db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
  
  set_time_limit(0);
  
  //check entity
  $entity_query = db_query("select * from app_entities where id='" . (int)$_GET['entity_id']. "'");
  if($entity = db_fetch_array($entity_query))
  {
  
    //get groupoed users fields
    $fields_array = array();
    $fields_query = db_query("select * from app_fields where type in ('fieldtype_grouped_users') and entities_id='" . $entity['id'] . "'");
    while($fields = db_fetch_array($fields_query))
    {
      $fields_array[] = $fields['id'];
    }
    
    //if grouped users fields exist then remove | from vield value if exist
    if(count($fields_array)>0)
    {                            
      foreach($fields_array as $filed_id)      
      {        
        $update_query = "update app_entity_" . $entity['id'] . " set field_" . $filed_id . " = SUBSTRING(field_" . $filed_id . ",1,locate('|',field_" . $filed_id . ")-1) where locate('|',field_" . $filed_id . ")>0";
        
        db_query($update_query);
      }                        
    }
  
  
    //get fields with multiple values
    $fields_array = array();
    $fields_query = db_query("select * from app_fields where type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_grouped_users','fieldtype_checkboxes','fieldtype_dropdown_multiple','fieldtype_entity','fieldtype_users') and entities_id='" . $entity['id'] . "'");
    while($fields = db_fetch_array($fields_query))
    {
      $fields_array[] = $fields['id'];
    }
    
    //run update if fields exist
    if(count($fields_array)>0)
    {
      
      $insert_sql = "INSERT INTO app_choices_values (entities_id, items_id, fields_id, value) VALUES ";
      
      $insert_values_sql_array = array();  
      
      //get items
      $items_query = db_query("select * from app_entity_" . $entity['id']);
      while($items = db_fetch_array($items_query))
      {
        //print_r($items);
        
        foreach($fields_array as $filed_id)
        {
          if(strlen($items['field_' . $filed_id])>0)
          {
            //echo $items['field_' . $filed_id] . '<br>';
            
            $values = explode(',',$items['field_' . $filed_id]);
            
            //inset values
            foreach($values as $value)
            {
              if(strlen($value)>0)
              {
                $insert_values_sql_array[] =" ('" . $entity['id'] . "', '" . $items['id'] . "', '" . $filed_id . "', '" . $value . "')";
                
                if(count($insert_values_sql_array)==600)
                {
                  db_query($insert_sql . implode(',',$insert_values_sql_array));
                  
                  $insert_values_sql_array = array();
                }
              }
            }
          }
        }
      }
      
      if(count($insert_values_sql_array)>0)
      {
        db_query($insert_sql . implode(',',$insert_values_sql_array));                
      }
    }
    
       
    //add new field type fieldtype_parent_item_id to handle Parent column
    $check_query = db_query("select * from app_fields where type in ('fieldtype_parent_item_id') and entities_id='" . $entity['id'] . "'");
    if(!$check = db_fetch_array($check_query))
    {
      $fields_query = db_query("select * from app_fields where type in ('fieldtype_id') and entities_id='" . $entity['id'] . "'");
      $fields = db_fetch_array($fields_query);
      
      $sql_data = array('forms_tabs_id'=>$fields['forms_tabs_id'],
                        'entities_id'=>$entity['id'],
                        'name'=>'',
                        'listing_status'=>1, 
                        'sort_order'=>0,
                        'listing_sort_order'=>100,                       
                        'type'=>'fieldtype_parent_item_id');
                        
      db_perform('app_fields',$sql_data);
    }
    
  }
  
  
  echo '<div><i class="fa fa-check-circle"></i> OK</div>';
  
  exit();
}

include('includes/template_top.php');


if($lang=='ru')
{
  define('TEXT_UPDATING_DATA','Обновление данных');
  define('TEXT_UPDATING_DATA_WARN','Дождитесь успешного выполнения для каждой сущности');
  define('TEXT_TABLE_CHOICES_VALUES_ERRO','Таблица <b>app_choices_values</b> не найдена.<br>Сначала выполните скрипт обновления базы данных <b>from_1.6_to_1.7.php</b><br>Читайте инструкцию обновления для более подробной информации.');
}
else
{
  define('TEXT_UPDATING_DATA','Update Data');
  define('TEXT_UPDATING_DATA_WARN','Wait for the successful execution of each entity');
  define('TEXT_TABLE_CHOICES_VALUES_ERRO','Table <b>app_choices_values</b> not found.<br>First, run the script to update the database <b>from_1.6_to_1.7.php</b><br>Read update instruction for more details.');
}

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
  $tables_array[] =  current($tables);    
}

//print_r($columns_array);

//check if we have to run updat for current database
if(in_array('app_choices_values',$tables_array))
{
  //reset choices values
  db_query("delete from app_choices_values");
  
      
  $ajax_url = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
    
  $html = '

<script>
  function update_data_for_entity(entity_id)
  {
    $("#entity_container_"+entity_id).load("' . $ajax_url. '?entity_id="+entity_id);
  }
</script>  
  
  <h3 class="page-title">' . TEXT_UPDATING_DATA . '</h3>
  <div class="alert alert-warning">' . TEXT_UPDATING_DATA_WARN . '</div>
  <table >';
  $enitites_query = db_query("select * from app_entities order by name");
  while($enitites = db_fetch_array($enitites_query))
  {
    $html .= '
      <tr>
        <td style="padding-right: 15px;">' . $enitites['name']. '</td>
        <td>
          <div id="entity_container_' . $enitites['id'] . '"><i class="fa fa-spinner fa-spin"></i></div>
          
          <script>
            update_data_for_entity(' . $enitites['id']  . ')
          </script>
        </td>
      </tr>';
  }
  
  $html .= '</table>';
  
  echo $html;
}
else
{
  echo '<div class="alert alert-danger">' . TEXT_TABLE_CHOICES_VALUES_ERRO . '</div>';
}

include('includes/template_bottom.php');