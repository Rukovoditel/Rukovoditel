<?php

define('TEXT_UPDATE_VERSION_FROM','1.7');
define('TEXT_UPDATE_VERSION_TO','1.8');


//build table name for related itesm
	function get_related_items_table_name($entities_id, $related_entities_id)
	{
	  if($entities_id>$related_entities_id)
	  {
	    $table_name = 'app_related_items_' . $related_entities_id . '_' . $entities_id;
	    $key_name = $related_entities_id . '_' . $entities_id;
	  }
	  else
	  {
	    $table_name =  'app_related_items_' . $entities_id . '_' . $related_entities_id;
	    $key_name = $entities_id . '_' . $related_entities_id;
	  }
	  
	  $sufix = '';
	   
	  if($entities_id==$related_entities_id)
	  {
	  	$sufix = '_related';
	  }
	  
	  return array('table_name' => $table_name, 'table_key' => $key_name,'sufix'=>$sufix);
	}

//extra db connect for ajax action
	if(isset($_GET['update_related_records']) or isset($_GET['entity_id']))
	{
		require('../../config/database.php');
		
		include('includes/database.php');
		
		db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
		
		set_time_limit(0);
	}

//to update related items
if(isset($_GET['update_related_records']))
{
	/**
	 * update related items
	 */
	
	//create related tables from current data
	$related_entities_groups = array();
	$related_items_query = db_query("select * from app_related_items");
	while($related_items = db_fetch_array($related_items_query))
	{
		$table_info = get_related_items_table_name($related_items['entities_id'],$related_items['related_entities_id']);
	
		if(!in_array($table_info['table_key'],$related_entities_groups))
		{
			$sql = '
          CREATE TABLE IF NOT EXISTS `' . $table_info['table_name'] . '` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `entity_' .$related_items['entities_id'] .  '_items_id` int(11) NOT NULL,
            `entity_' . $related_items['related_entities_id']. $table_info['sufix'] . '_items_id` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_' . $related_items['entities_id'] . '_items_id` (`entity_' . $related_items['entities_id'] . '_items_id`),
            KEY `idx_' . $related_items['related_entities_id'] . $table_info['sufix'] . '_items_id` (`entity_' . $related_items['related_entities_id'] . $table_info['sufix'] . '_items_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
      ';
	
			db_query($sql);
		}
	
		$related_entities_groups[$table_info['table_key']] = $table_info['table_key'];
	
	}
	
	//created related tabes from current fields
	$fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_related_records')");
	while($fields = db_fetch_array($fields_query))
	{		
		if(strlen($fields['configuration']))
		{
			$cfg = json_decode(stripcslashes($fields['configuration']),true);
			
			if((int)$cfg['entity_id']>0)
			{
				$entities_id = $fields['entities_id'];
				$related_entities_id = (int)$cfg['entity_id'];
				
				$table_info = get_related_items_table_name($entities_id,$related_entities_id);
				
				if(!in_array($table_info['table_key'],$related_entities_groups))
				{
					$sql = '
	          CREATE TABLE IF NOT EXISTS `' . $table_info['table_name'] . '` (
	            `id` int(11) NOT NULL AUTO_INCREMENT,
	            `entity_' .$entities_id .  '_items_id` int(11) NOT NULL,
	            `entity_' . $related_entities_id. $table_info['sufix'] . '_items_id` int(11) NOT NULL,
	            PRIMARY KEY (`id`),
	            KEY `idx_' . $entities_id . '_items_id` (`entity_' . $entities_id . '_items_id`),
	            KEY `idx_' . $related_entities_id . $table_info['sufix'] . '_items_id` (`entity_' . $related_entities_id . $table_info['sufix'] . '_items_id`)
	          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
	      	';
					
					db_query($sql);
				}
			}						
		}
	}
	
	foreach($related_entities_groups as  $v)
	{
		$related_entities = explode('_',$v);
		$table_info = get_related_items_table_name($related_entities[0],$related_entities[1]);
		 
		db_query("TRUNCATE TABLE `" . $table_info['table_name'] . "`");
	}
	
	
	$current_step = '';
	$insert_sql = array();
	$related_items_query = db_query("select * from app_related_items order by entities_id, related_entities_id");
	while($related_items = db_fetch_array($related_items_query))
	{
		if($current_step=='')
		{
			$current_step = $related_items['entities_id'] . '_' . $related_items['related_entities_id'];
		}
		
		$table_info = get_related_items_table_name($related_items['entities_id'],$related_items['related_entities_id']);
		 
		$use_step = $related_items['entities_id'] . '_' . $related_items['related_entities_id'];
		
		if(!isset($insert_sql[$use_step]))
		{
			$insert_sql[$use_step] = array(
					'table_name' => $table_info['table_name'],																			
					'entities_id' => $related_items['entities_id'],	
					'related_entities_id' => $related_items['related_entities_id'],
					'sufix' => $table_info['sufix'],
					'query'=> array(),
			);
			
		}
			
		$insert_sql[$use_step]['query'][] = "(NULL, '" . $related_items['items_id'] . "', '" . $related_items['related_items_id'] . "')";
					
		if(count($insert_sql[$current_step]['query'])==1600 or $current_step!=$use_step)
		{
			$sql ="INSERT INTO `" . $insert_sql[$current_step]['table_name'] . "` (`id`, `entity_" . $insert_sql[$current_step]['entities_id'] . "_items_id`, `entity_" . $insert_sql[$current_step]['related_entities_id']. $insert_sql[$current_step]['sufix'] . "_items_id`) VALUES " . implode(',',$insert_sql[$current_step]['query']);
			db_query($sql);
	
			$insert_sql[$current_step]['query'] = array();
			
			$current_step = $use_step;
		}
	}
	
	foreach($insert_sql as $current_step=>$info)
	{
		if(count($info['query'])>0)
		{	
			$sql ="INSERT INTO " . $info['table_name'] . " (`id`, `entity_" . $info['entities_id'] . "_items_id`, `entity_" . $info['related_entities_id']. $info['sufix'] . "_items_id`) VALUES " . implode(',',$info['query']);
			db_query($sql);
		}
	}
		 
	/**
	 * End of update related items
	 */	
	
	echo '<div><i class="fa fa-check-circle"></i> OK</div>';
	
	exit();
}

//to update choices values
if(isset($_GET['entity_id']))
{	
	/**
	 * start update choices values for entities
	 */
	
	$entities_query = db_query("select * from app_entities where id='" . $_GET['entity_id'] . "'");
	if($entities = db_fetch_array($entities_query))
	{
		$sql = '
    		CREATE TABLE IF NOT EXISTS app_entity_' . (int)$entities['id'] . '_values (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  items_id int(11) NOT NULL,
				  fields_id int(11) NOT NULL,
				  value int(11) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `idx_items_id` (`items_id`),
				  KEY `idx_fields_id` (`fields_id`),
    			KEY `idx_items_fields_id` (`items_id`,`fields_id`),
    			KEY `idx_value_id` (`value`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    	';
		 
		db_query($sql);
		 
		db_query("TRUNCATE TABLE app_entity_" . (int)$entities['id'] . "_values");
		 
		$insert_sql = "INSERT INTO app_entity_" . (int)$entities['id'] . "_values (items_id, fields_id, value) VALUES ";
		$insert_values_sql_array = array();
		 
		$choices_query = db_query("select * from app_choices_values where entities_id='" . (int)$entities['id'] . "'");
		while($choices = db_fetch_array($choices_query))
		{
			$insert_values_sql_array[] =" ('" . $choices['items_id'] . "', '" . $choices['fields_id'] . "', '" . $choices['value'] . "')";
	
			if(count($insert_values_sql_array)==1600)
			{
				db_query($insert_sql . implode(',',$insert_values_sql_array));
	
				$insert_values_sql_array = array();
			}
		}
		 
		if(count($insert_values_sql_array)>0)
		{
			db_query($insert_sql . implode(',',$insert_values_sql_array));
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
                 
  $ajax_url = ($_SERVER['HTTPS'] == "on" ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];
  
  $html = '
  
<script>
  function update_data_for_entity(entity_id)
  {
    $("#entity_container_"+entity_id).load("' . $ajax_url. '?entity_id="+entity_id);
  }
    		
  function update_related_records(entity_id)
  {
    $("#related_records_container").load("' . $ajax_url. '?update_related_records=true");
  }  		
</script>
  
  <h3 class="page-title">' . TEXT_UPDATING_DATA . '</h3>
  <div class="alert alert-warning">' . TEXT_UPDATING_DATA_WARN . '</div>
  <table >
  	<tr>
        <td style="padding-right: 15px;">Related Records/Связанные Записи</td>
        <td>
          <div id="related_records_container"><i class="fa fa-spinner fa-spin"></i></div>
  
          <script>
            update_related_records()
          </script>
        </td>
     </tr>		
  		
  ';
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