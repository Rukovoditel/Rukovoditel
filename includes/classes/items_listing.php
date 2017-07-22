<?php

class items_listing
{
  private $fields_in_listing;
  
  private $entities_id;
  
  public $rows_per_page;
  
  public $force_access_query;
  
  function __construct($reports_id)
  {
    $reports_info = db_find('app_reports',$reports_id);
    
    $this->fields_in_listing = $reports_info['fields_in_listing'];
    
    $this->entities_id = $reports_info['entities_id'];  
    
    $this->rows_per_page = $reports_info['rows_per_page'];
    
    $this->force_access_query = $reports_info['displays_assigned_only']; 
  }
  
  function get_fields_query()
  {
    if(strlen($this->fields_in_listing)>0)
    {
      $sql = "select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name  from app_fields f where f.id in (" . $this->fields_in_listing . ") and  f.entities_id='" . db_input($this->entities_id) . "' order by field(f.id," . $this->fields_in_listing . ")";  
    }
    else
    {
      $sql = "select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name  from app_fields f where f.listing_status=1 and  f.entities_id='" . db_input($this->entities_id) . "' order by f.listing_sort_order, f.name";
    }
    
    return $sql;
  }
}