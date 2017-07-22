<?php

class users_filters
{
  public $reports_id;
  
  function __construct($reports_id)
  {
    $this->reports_id = $reports_id;
  }
  
  function count()
  {
    global $app_user;
    
    $count_query = db_query("select count(*) as total from app_users_filters where reports_id='" . db_input($this->reports_id) . "' and users_id='" . db_input($app_user['id']) . "'");
    $count = db_fetch_array($count_query);
    
    return $count['total'];
  }
  
  function get_choices($add_mepty = false)
  {
    global $app_user;
    
    $choices = array();
    
    if($add_mepty)
    {
      $choices[''] = '';
    }
            
    $filters_query = db_query("select * from app_users_filters where reports_id='" . db_input($this->reports_id) . "' and users_id='" . db_input($app_user['id']) . "' order by name");
    while($filters = db_fetch_array($filters_query))
    {
      $choices[$filters['id']] = $filters['name'];
    }
    
    return $choices;
  }
  
  function set_filters($filters_id)
  { 
    db_query("delete from app_user_filters_values where filters_id='" . db_input($filters_id) . "'");
    
    $this->set_reports_filters($filters_id,$this->reports_id);
      
    foreach(reports::get_parent_reports($this->reports_id) as $parent_reports_id)
    {
      $this->set_reports_filters($filters_id,$parent_reports_id);
    }
  }
  
  function set_reports_filters($filters_id, $reports_id)
  {
    global $app_user;
    
    $reports_filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf, app_fields f  where rf.fields_id=f.id and rf.reports_id='" . db_input($reports_id) . "' order by rf.id");
    while($reports_filters = db_fetch_array($reports_filters_query))
    {
      $sql_data = array('reports_id'=>$reports_id,
                        'filters_id'=>$filters_id,
                        'fields_id'=>$reports_filters['fields_id'],
                        'filters_values'=> $reports_filters['filters_values'],                                                                    
                        'filters_condition'=> $reports_filters['filters_condition'],
      									'is_active'=>$reports_filters['is_active'],
                        );              
      db_perform('app_user_filters_values',$sql_data);
    }    
  }
  
  function use_filters($filters_id)
  {
    if($filters_id=='default')
    {
      $this->use_default_filters();
    }
    else
    {
      $this->use_reports_filters($filters_id,$this->reports_id);
        
      foreach(reports::get_parent_reports($this->reports_id) as $parent_reports_id)
      {
        $this->use_reports_filters($filters_id,$parent_reports_id);
      }
    }
  }
  
  function use_reports_filters($filters_id, $reports_id)
  {
    db_query("delete from app_reports_filters where reports_id='" . db_input($reports_id) . "'");
    
    $filters_query = db_query("select * from app_user_filters_values where filters_id='" . db_input($filters_id) . "' and reports_id='" . db_input($reports_id) . "'");
    while($filters = db_fetch_array($filters_query))
    {
      $sql_data = array('reports_id'=>$reports_id,
                        'fields_id'=>$filters['fields_id'],                        
                        'filters_values'=> $filters['filters_values'],                                                                    
                        'filters_condition'=> $filters['filters_condition'],
      									'is_active'=>$filters['is_active'],
                        );              
      db_perform('app_reports_filters',$sql_data);
    }
  }
  
  function use_default_filters()
  {
    db_query("delete from app_reports_filters where reports_id='" . db_input($this->reports_id) . "'");
    
    foreach(reports::get_parent_reports($this->reports_id) as $parent_reports_id)
    {
      db_query("delete from app_reports_filters where reports_id='" . db_input($parent_reports_id) . "'");
    }
    
    $reports_info_query = db_query("select * from app_reports where id='" . db_input($this->reports_id). "'");
    $reports_info = db_fetch_array($reports_info_query);
    
    $reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($reports_info['entities_id']). "' and reports_type='default'");
    if($reports_info = db_fetch_array($reports_info_query))
    {      
      $filters_query = db_query("select * from app_reports_filters where reports_id='" . db_input($reports_info['id']) . "'");
      while($filters = db_fetch_array($filters_query))
      {
        $sql_data = array('reports_id'=>$this->reports_id,
                          'fields_id'=>$filters['fields_id'],                        
                          'filters_values'=> $filters['filters_values'],                                                                    
                          'filters_condition'=> $filters['filters_condition'],
        									'is_active'=>$filters['is_active'],	
                          );              
        db_perform('app_reports_filters',$sql_data);
      }
    }
  }
  
  function ser_reports_settings($filters_id)
  {
  	$reports_info_query = db_query("select * from app_reports where id='" . db_input($this->reports_id). "'");
  	$reports_info = db_fetch_array($reports_info_query);
  	
  	if($filters_id=='default')
  	{
  		$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($reports_info['entities_id']). "' and reports_type='default'");
      if($reports_info = db_fetch_array($reports_info_query))
      {            
      	db_query("update app_reports set fields_in_listing='" . db_input($reports_info['fields_in_listing']) ."', listing_order_fields='" . db_input($reports_info['listing_order_fields']) ."'  where id='" . db_input($this->reports_id) . "'");
      }
  	}
  	else 
  	{
  		$filters_info = db_find('app_users_filters',$filters_id);
  		
  		if(strlen($filters_info['fields_in_listing']))
  		{
  			db_query("update app_reports set fields_in_listing='" . db_input($filters_info['fields_in_listing']) ."' where id='" . db_input($this->reports_id) . "'");
  		}
  		 
  		if(strlen($filters_info['listing_order_fields']))
  		{
  			db_query("update app_reports set listing_order_fields='" . db_input($filters_info['listing_order_fields']) ."' where id='" . db_input($this->reports_id) . "'");
  		}
  	}
  	
  	  	
  }
}