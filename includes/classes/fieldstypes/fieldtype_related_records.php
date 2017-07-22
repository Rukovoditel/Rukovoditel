<?php

class fieldtype_related_records
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_RELATED_RECORDS_TITLE);
  }
  
  function get_configuration($params = array())
  {  
    $entity_info = db_find('app_entities',$params['entities_id']);
    
    $cfg = array();
            
    $cfg[] = array('title'=>TEXT_SELECT_ENTITY, 
                   'name'=>'entity_id',
                   'tooltip'=>TEXT_FIELDTYPE_RELATED_RECORDS_SELECT_ENTITY_TOOLTIP . ' ' . $entity_info['name'],
                   'type'=>'dropdown',
                   'choices'=>entities::get_choices(),
                   'params'=>array('class'=>'form-control input-medium'));
                         
    $cfg[] = array('title'=>tooltip_icon(TEXT_ROWS_PER_PAGE_IF_NOT_SET) . TEXT_ROWS_PER_PAGE,
    		'name'=>'rows_per_page',
    		'type'=>'input',
    		'params'=>array('class'=>'form-control input-xsmall'));
    
    $cfg[] = array('title'=>tooltip_icon(TEXT_DISPLAY_IN_MAIN_COLUMN_INFO) . TEXT_DISPLAY_IN_MAIN_COLUMN, 'name'=>'display_in_main_column','type'=>'checkbox');
                   
    $cfg[] = array('name'=>'fields_in_listing','type'=>'hidden');
    $cfg[] = array('name'=>'fields_in_popup','type'=>'hidden');
                           
    return $cfg;
  }  
  
  function render($field,$obj,$params = array())
  {
    return false;
  }
  
  function process($options)
  {        
    return false;
  }
  
  function output($options)
  {
    global $current_path_array, $current_entity_id, $current_item_id, $current_path,$app_user;
            
    //output count of related items 
    
    return $options['value'];    
  }  
  
  function reports_query($options)
  {
    $filters = $options['filters'];
    $sql_query = $options['sql_query'];
  
    $sql = array();
    
    if(strlen($filters['filters_values'])>0)
    {
    
      $field = db_find('app_fields',$filters['fields_id']);
      
      $cfg = new fields_types_cfg($field['configuration']);
                  
      $table_info = related_records::get_related_items_table_name($options['entities_id'],$cfg->get('entity_id'));
      
      $where_sql = '';
      
      if(strlen($table_info['sufix'])>0)
      {
      	$where_sql = " or ri.entity_" . $options['entities_id'] . $table_info['sufix'] . "_items_id=e.id ";
      }
      
      $sql = "(select count(*) as total from " . $table_info['table_name'] . " ri where ri.entity_" . $options['entities_id'] . "_items_id=e.id {$where_sql})";
      
                                          
      $sql_query[] = ($filters['filters_values']=='include' ? $sql . ">0" : $sql . "=0");
    }
                      
    return $sql_query;
  }
  
  static function prepare_query_select($entities_id,$listing_sql_query_select)
  {
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.listing_status=1 and f.entities_id='" . db_input($entities_id) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    {
      $cfg = new fields_types_cfg($field['configuration']);
      
      $table_info = related_records::get_related_items_table_name($entities_id,$cfg->get('entity_id'));
      
      $where_sql = '';
      
      if(strlen($table_info['sufix'])>0)
      {
      	$where_sql = " or ri.entity_" . $entities_id . $table_info['sufix'] . "_items_id=e.id ";
      }
      
      $listing_sql_query_select .= ", (select count(*) as total from " . $table_info['table_name'] . " ri where ri.entity_" . $entities_id . "_items_id=e.id {$where_sql}) as field_" .$field['id'];                                            
    }
    
    return $listing_sql_query_select;
  }  
}