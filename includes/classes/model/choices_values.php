<?php

class choices_values
{
  protected $entities_id;
  
  protected $use_for_fieldtypes; 
  
  protected $choices_values_list;
  
  function __construct($entities_id)
  {
    $this->entities_id = $entities_id;
    
    $this->use_for_fieldtypes = array('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_grouped_users','fieldtype_checkboxes','fieldtype_dropdown_multiple','fieldtype_entity','fieldtype_users');
    
    $this->choices_values_list = array();
  }
  
  function prepare($options)
  {
    if(in_array($options['class'],$this->use_for_fieldtypes))
    {
      $this->choices_values_list[] = array('fields_id'=>$options['field']['id'], 'value'=>$options['value']);
    }
  }
  
  function process($items_id)
  {             
    foreach($this->choices_values_list as $values)
    {       
    	//reset choices values for current item and field
    	db_query("delete from app_entity_" . $this->entities_id . "_values where items_id='" . db_input($items_id) . "' and fields_id='" . $values['fields_id']. "'");
    	
      //prepare valuse
      $value = (is_array($values['value']) ? $values['value'] : (strlen($values['value'])>0 ? array($values['value']): array()) );
      
      $sql_data = array();
      
      //insert values
      foreach($value as $v)
      {
        $sql_data[] = array('items_id'    => $items_id,
                            'fields_id'   => $values['fields_id'],
                            'value'       => $v);
        
      }   
      
      db_batch_insert("app_entity_" . $this->entities_id . "_values", $sql_data);
    }        
  }
  
  function process_by_field_id($items_id, $fields_id, $fields_type, $values)
  {  
    //reset choices values for current item and field
    db_query("delete from app_entity_" . $this->entities_id . "_values where items_id='" . db_input($items_id) . "' and fields_id='" . $fields_id. "'");    
        
    if(in_array($fields_type,$this->use_for_fieldtypes))
    {       
    
      //prepare valuse
      $value = (is_array($values) ? $values : (strlen($values)>0 ? explode(',',$values): array()));
      
      $sql_data = array();
      
      //insert values
      foreach($value as $v)
      {
        //skip empty values
        if(strlen($v)==0) continue;
        
        $sql_data[]  = array('items_id'    => $items_id,
                           	 'fields_id'   => $fields_id,
                             'value'       => $v);
        
      }   
      
      db_batch_insert("app_entity_" . $this->entities_id . "_values", $sql_data);
    }        
  }  
  
  static function delete_by_item_id($entities_id, $items_id)
  {
    db_query("delete from app_entity_" . $entities_id . "_values where items_id='" . db_input($items_id) . "'");      
  }
  
  static function delete_by_field_id($entities_id, $fields_id)
  {
    db_query("delete from app_entity_" . $entities_id . "_values where fields_id='" . db_input($fields_id) . "'");      
  }
}