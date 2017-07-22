<?php

class access_groups
{
  public static function get_ldap_default_group_id()
  {
    $group_info_query = db_query("select id from app_access_groups where is_ldap_default=1");
    if($group_info = db_fetch_array($group_info_query))
    {
      return $group_info['id'];
    }
    else
    {
      return false;
    }
  }
  
  public static function get_default_group_id()
  {
    $group_info_query = db_query("select id from app_access_groups where is_default=1");
    if($group_info = db_fetch_array($group_info_query))
    {
      return $group_info['id'];
    }
    else
    {
      return false;
    }
  }
  
  public static function get_name_by_id($id)
  {
    if($id==0)
    {
      return TEXT_ADMINISTRATOR;
    }
    else
    {
      $obj = db_find('app_access_groups',$id);
      
      return $obj['name'];
    }
  }
  
  public static function check_before_delete($id)
  {
    if(($coutn = db_count('app_entity_1',$id,'field_6'))>0)
    {
      return sprintf(TEXT_ERROR_DELETE_USER_GROUP,$coutn);
    }
    else
    {
      return '';
    }
  }
  
  public static function get_choices($include_administrator = true)
  {
    $choices = array();
    
    if($include_administrator)
    {
      $choices[0] = TEXT_ADMINISTRATOR;
    }
    
    $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
    while($v = db_fetch_array($groups_query))
    {
      $choices[$v['id']] = $v['name'];
    }
    
    return $choices;
  }
  
  public static function get_cache()
  {
    $cache = array();
    
    $cache[0] = TEXT_ADMINISTRATOR;
    
    $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
    while($v = db_fetch_array($groups_query))
    {
      $cache[$v['id']] = $v['name'];
    }
    
    return $cache;
  }
}