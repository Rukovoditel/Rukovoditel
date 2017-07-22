<?php

class forms_tabs
{
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_forms_tabs',$id);
    
    return $obj['name'];
  }
  
  public static function check_before_delete($forms_tabs_id)
  {
    $msg = '';
    
    if(db_count('app_fields',$forms_tabs_id,'forms_tabs_id')>0)
    {
      $msg = sprintf(TEXT_WARN_DELETE_FROM_TAB,forms_tabs::get_name_by_id($forms_tabs_id));
    }
        
    return $msg;
  }
  
  public static function get_choices($entities_id)
  {
    $choices = array();
    $query = db_fetch_all('app_forms_tabs',"entities_id='" . (int)$entities_id . "'",'sort_order, name');   
    while ($v = db_fetch_array($query)) 
    {    
      $choices[$v['id']] = $v['name']; 
    }
    
    return $choices;
  }
  
  public static function get_last_sort_number($entities_id)
  {
    $v = db_fetch_array(db_query("select max(sort_order) as max_sort_order from app_forms_tabs where entities_id = '" . db_input($entities_id) . "'"));
    
    return $v['max_sort_order'];
  }
  
  public static function is_reserved($tabs_id)
  {
    $tab_query = db_query("select * from app_fields where forms_tabs_id='" . db_input($tabs_id ). "' and type='fieldtype_id'");
    if($tab = db_fetch_array($tab_query))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
   
}