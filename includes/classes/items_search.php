<?php

class items_search
{
  public $access_schema;
  
  public $search_fields;
  
  public $entities_id;
  
  public $parent_entities_id;
  
  public $search_keywords;
  
  public $path;
  
  function __construct($entities_id)
  {
    global $app_user;
    
    $this->entities_id = $entities_id;
    
    $entities_info = db_find('app_entities',$this->entities_id);
    
    $this->parent_entities_id = $entities_info['parent_id'];
    
    //get entity access schema
    $this->access_schema = users::get_entities_access_schema($this->entities_id, $app_user['group_id']);
    
    $this->search_fields = array();
    
    //set search by Name by default
    if($id = fields::get_heading_id($this->entities_id))
    {
      $this->search_fields[] = array('id'=>$id);
    }
    
    if($this->entities_id==1)
    {
      $this->search_fields[] = array('id'=>7);
      $this->search_fields[] = array('id'=>8);
      $this->search_fields[] = array('id'=>9);
    }
    
    $this->path = false;
  }
  
  function set_path($path)
  {
    $this->path = $path;
  }
  
  function set_search_keywords($keywords)
  {
    $this->search_keywords = $keywords;
  }
  
  function build_search_sql_query()
  {
    $listing_sql_query = '';
    
    if(app_parse_search_string($this->search_keywords, $search_keywords))
    {
      //print_r($search_keywords);
      
      $sql_query = array();
      
      /**
       *  search in fields
       */         
      foreach($this->search_fields as $field)
      {        
        if (isset($search_keywords) && (sizeof($search_keywords) > 0)) 
        {
          $where_str = "(";
          for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) 
          {
            switch ($search_keywords[$i]) 
            {
              case '(':
              case ')':
              case 'and':
              case 'or':
                $where_str .= " " . $search_keywords[$i] . " ";
                break;
              default:
                $keyword = $search_keywords[$i];
                $where_str .= "e.field_" . $field['id'] . " like '%" . db_input($keyword) . "%'";
                break;
            }
          }
          $where_str .= ")";
          
          $sql_query[] = $where_str;
        }
      }
      
      /**
       *  add search by record ID if vlaue is numeric
       */        
      if(count($search_keywords)==1 and is_numeric($search_keywords[0]))
      {
        $sql_query[] = "id='" . db_input($search_keywords[0]) . "'";
      }
      
    
      if(count($sql_query)>0)
      {                  
        //print_r($sql_query);
        
        $listing_sql_query .= ' and (' . implode(' or ',$sql_query) . ')';
      }        
    }
        
    //check parent item
    if($this->path and $this->parent_entities_id>0)
    {
      $path_array = items::parse_path($this->path);
                   
      if($this->parent_entities_id==$path_array['parent_entity_id'])
      {
        $listing_sql_query .= " and e.parent_item_id='" . db_input($path_array['parent_entity_item_id']) . "'";                
      }      
    }
    
    return $listing_sql_query;
  }
  
  function get_choices()
  {  	  
    $choices = array();
    
    //add search sql query
    $listing_sql_query = $this->build_search_sql_query();        
    
    //check view assigned only access
    $listing_sql_query = items::add_access_query($this->entities_id,$listing_sql_query);
  
    //include access to parent records
    $listing_sql_query .= items::add_access_query_for_parent_entities($this->entities_id);
    
    $listing_sql_query .= items::add_listing_order_query_by_entity_id($this->entities_id);
    
    $items_sql_query = "select e.* from app_entity_" . $this->entities_id . " e where e.id>0 " . $listing_sql_query; 
    $items_query = db_query($items_sql_query);
    while($items = db_fetch_array($items_query))
    {       
      //add paretn item name if exist
      $parent_name = '';
      
      if($this->path and $this->parent_entities_id>0)
      {
        $path_array = items::parse_path($this->path);
                   
        if($this->parent_entities_id!=$path_array['parent_entity_id'] and $items['parent_item_id']>0)
        {                    
          $parent_name = items::get_heading_field($this->parent_entities_id,$items['parent_item_id']) . ' > ';          
        }
      } 
            
      $name = items::get_heading_field($this->entities_id,$items['id']);
          
      $choices[$items['id']] = $parent_name . $name;
                  
    } 
    
    return $choices; 
  
  }
  
  
}