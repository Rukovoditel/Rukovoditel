<?php

switch($app_module_action)
{  
  case 'select':  
        if(isset($_POST['checked']))
        {
          $app_selected_items[$_POST['reports_id']][] = $_POST['id'];
        }
        else
        {
          $key = array_search($_POST['id'], $app_selected_items[$_POST['reports_id']]);
          if($key!==false)
          {
            unset($app_selected_items[$_POST['reports_id']][$key]);
          }
        }        
        
        $app_selected_items[$_POST['reports_id']] =  array_unique($app_selected_items[$_POST['reports_id']]);        
      exit();
    break;
  case 'select_all':
  
      if(isset($_POST['checked']))
      {
      	$listing_sql_query_select = '';
        $listing_sql_query = '';
        $listing_sql_query_join = '';
        $listing_sql_query_having = '';
        $sql_query_having = array();
        
        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, $listing_sql_query_select);
        
        //prepare count of related items in listing
        $listing_sql_query_select = fieldtype_related_records::prepare_query_select($current_entity_id, $listing_sql_query_select);
        
        if(strlen($_POST['search_keywords'])>0)
        {
          require(component_path('items/add_search_query'));
        }
        
        if(strlen($_POST['search_keywords'])>0 and $_POST['search_in_all']=='true')
				{
					//skip filters if there is search keyworkds and option search_in_all in 
				}
				else
				{        
	        $listing_sql_query = reports::add_filters_query($_POST['reports_id'],$listing_sql_query);
	        
	        //prepare having query for formula fields
	        if(isset($sql_query_having[$current_entity_id]))
	        {
	        	$listing_sql_query_having  = reports::prepare_filters_having_query($sql_query_having[$current_entity_id]);
	        }
        }
                
        if($parent_entity_item_id>0)
        {
          $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
        }
                
        $listing_sql_query = items::add_access_query($current_entity_id,$listing_sql_query);
        
        //add having query
        $listing_sql_query .= $listing_sql_query_having;
        
	      if(strlen($_POST['listing_order_fields'])>0)
				{  
				  $info = reports::add_order_query($_POST['listing_order_fields'],$current_entity_id);
				      
				  $listing_order_fields_id = $info['listing_order_fields_id'];
				  $listing_order_fields = $info['listing_order_fields'];
				  $listing_order_clauses = $info['listing_order_clauses'];
				  
				  $listing_sql_query .= $info['listing_sql_query'];
				  $listing_sql_query_join .= $info['listing_sql_query_join'];
				}
        
        $app_selected_items[$_POST['reports_id']] = array();
        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;        
        $items_query = db_query($listing_sql);
        while($item = db_fetch_array($items_query))
        {
          $app_selected_items[$_POST['reports_id']][] = $item['id'];
        }        
      }
      else
      {
        $app_selected_items[$_POST['reports_id']] = array();
      }
                  
    break;
    
}    