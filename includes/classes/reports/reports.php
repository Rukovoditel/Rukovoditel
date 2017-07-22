<?php

class reports
{
  public static function get_default_entity_report_id($entity_id,$reports_type)
  {
    $reports_info = reports::create_default_entity_report($entity_id, $reports_type);
    
    return $reports_info['id'];
  }
  
  
  public static function create_default_entity_report($entity_id,$reports_type,$path_array=array()) 
  {
    global $app_logged_users_id;
    
    $where_str = '';
            
    //fitler reports by parent item
    if(count($path_array)>1)
    {
      $parent_path_array = explode('-',$path_array[count($path_array)-2]);
                               
      $parent_entity_id = $parent_path_array[0];
      $parent_item_id = $parent_path_array[1];
      
      $where_str = " and parent_entity_id='" . $parent_entity_id . "' and parent_item_id='" . $parent_item_id . "'";
    }
    else
    {
      $parent_entity_id = 0;
      $parent_item_id = 0;
    }
        
    $reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($entity_id). "' and reports_type='" . $reports_type . "' and created_by='" . $app_logged_users_id . "' "  . $where_str );
    if(!$reports_info = db_fetch_array($reports_info_query))
    {
      $default_reports_query = db_query("select * from app_reports where entities_id='" . db_input($entity_id). "' and reports_type='default'");
      $default_reports = db_fetch_array($default_reports_query);
    
      $sql_data = array('name'=>'',
                       'entities_id'=>$entity_id,
                       'reports_type'=>$reports_type,                                              
                       'in_menu'=>0,
                       'in_dashboard'=>0,
                       'listing_order_fields'=>$default_reports['listing_order_fields'],
                       'created_by'=>$app_logged_users_id,
                       'parent_entity_id' => $parent_entity_id, 
                       'parent_item_id' => $parent_item_id,
                       );
      db_perform('app_reports',$sql_data);
      
      $reports_id = db_insert_id();
      
      $filters_query = db_query("select rf.*, f.name from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($default_reports['id']) . "' order by rf.id");
      while($v = db_fetch_array($filters_query))
      {
        $sql_data = array('reports_id'=>$reports_id,
                          'fields_id'=>$v['fields_id'],
                          'filters_condition'=>$v['filters_condition'],                                              
                          'filters_values'=>$v['filters_values'],
                          );
                                         
        db_perform('app_reports_filters',$sql_data);
      }
      
      $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
      $reports_info = db_fetch_array($reports_info_query);
    }
    
    //check if parent reports was not set
    if($reports_info['parent_id']==0 and $reports_type!='entity')
    {
      reports::auto_create_parent_reports($reports_info['id']);
      
      $reports_info = db_find('app_reports',$reports_info['id']);
    }
    
    return $reports_info;
  }
  
  public static function get_parent_reports($reports_id,$paretn_reports = array())
  {
    $report_info = db_find('app_reports',$reports_id);
    
    if($report_info['parent_id']>0)
    {
      $paretn_reports[] = $report_info['parent_id'];
      
      $paretn_reports = reports::get_parent_reports($report_info['parent_id'],$paretn_reports);
    }
    
    return $paretn_reports;
  }
  
  public static function auto_create_parent_reports($reports_id)
  {
    global $app_logged_users_id;
    
    $report_info = db_find('app_reports',$reports_id);
    $entity_info = db_find('app_entities',$report_info['entities_id']);
    
    if($entity_info['parent_id']>0 and $report_info['parent_id']==0)
    {
      $sql_data = array('name'=>'',
                        'entities_id'=>$entity_info['parent_id'],
                        'reports_type'=>'parent',                                              
                        'in_menu'=>0,
                        'in_dashboard'=>0,
                        'created_by'=>$app_logged_users_id,
                        );
                        
      db_perform('app_reports',$sql_data);
      
      $insert_id = db_insert_id();
      
      db_perform('app_reports',array('parent_id'=>$insert_id),'update',"id='" . db_input($reports_id) . "' and created_by='" . $app_logged_users_id . "'");
      
      reports::auto_create_parent_reports($insert_id);
    }
  }
  
  public static function delete_reports_by_item_id($entity_id, $item_id)
  {
    $report_info_query = db_query("select * from app_reports where parent_entity_id='" . $entity_id . "' and parent_item_id='" . $item_id . "'");
    while($report_info = db_fetch_array($report_info_query))
    {
      self::delete_reports_by_id($report_info['id']);            
    }
  }
  
  
  public static function delete_reports_by_id($reports_id)
  {        
    $report_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
    if($report_info = db_fetch_array($report_info_query))
    {
       //delete paretn reports
      self::delete_parent_reports($report_info['id']);
      
      db_query("delete from app_reports where id='" . db_input($report_info['id']) . "'");
      db_query("delete from app_reports_filters where reports_id='" . db_input($report_info['id']) . "'");
      
      //delete users filters
      $filters_query = db_query("select * from app_users_filters where reports_id='" .  db_input($report_info['id']) . "'");
      while($filters = db_fetch_array($filters_query))
      {
        db_query("delete from app_users_filters where id='" . db_input($filters['id']) . "'");
        db_query("delete from app_user_filters_values where filters_id='" . db_input($filters['id']) . "'");
      }
    }
  }
  
  public static function delete_parent_reports($reports_id)
  {        
    $paretn_reports = reports::get_parent_reports($reports_id);
    
    if(count($paretn_reports)>0)
    {
      foreach($paretn_reports as $id)
      {
        db_query("delete from app_reports where id='" . db_input($id) . "'");
        db_query("delete from app_reports_filters where reports_id='" . db_input($id) . "'");
      }
    }
  }
  
  public static function prepare_filters_having_query($sql_query_array)
  {
  	$sql_query = '';
  	
  	if(count($sql_query_array)>0)
  	{
  		$sql_query = ' having (' . implode(' and ',$sql_query_array) . ')';
  	} 
  	
  	return $sql_query;
  }
  
  public static function add_filters_query($reports_id,$listing_sql_query, $prefix = '', $is_parent_report = false)
  {	
  	global $sql_query_having;
  	
    $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
    if($reports_info = db_fetch_array($reports_info_query))
    {      
      $sql_query = array();
      
      $filters_query = db_query("select rf.*, f.name,f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($reports_info['id']) . "' and is_active=1 order by rf.id");
      while($filters = db_fetch_array($filters_query))
      {                
        if($filters['filters_condition']=='empty_value')
        {
          $sql_query[] = "length(field_" . $filters['fields_id'] . ")=0";
        }
        elseif(strlen($filters['filters_values'])>0)
        {       
          $sql_query = fields_types::reports_query(array('class'=>$filters['type'],'filters'=>$filters,'entities_id'=>$reports_info['entities_id'],'sql_query'=>$sql_query,'prefix'=>$prefix));
        }
      }
      
      //add filters queries
      if(count($sql_query)>0)
      {
        $listing_sql_query .= ' and (' . implode(' and ',$sql_query) .  ')';
      } 
            
      //add having queries for paretn report only
      if($is_parent_report and isset($sql_query_having[$reports_info['entities_id']]))
      {
      	$listing_sql_query  .= reports::prepare_filters_having_query($sql_query_having[$reports_info['entities_id']]);
      }
      
      //add filters for parent report if exist
      if($reports_info['parent_id']>0)
      {
        $report_info = db_find('app_reports',$reports_info['parent_id']);
               
        /**
         * The sql query "(select item_id from (select e.id ..." need to prepare filters by formula fileds with using having
         */
        $check_query = db_query("select count(*) as total from app_fields where entities_id='" . db_input($report_info['entities_id']) . "' and type='fieldtype_formula'");
        $check = db_fetch_array($check_query);
        
        if($check['total']>0)
        {
        	$listing_sql_query .= ' and e.parent_item_id in (select item_id from (select e.id as item_id ' . fieldtype_formula::prepare_query_select($report_info['entities_id'],'') . ' from app_entity_' . $report_info['entities_id']. ' e where e.id>0 ' .  items::add_access_query($report_info['entities_id'],'') . ' ' . reports::add_filters_query($reports_info['parent_id'],'','',true)  . ') as parent_entity_' . $report_info['entities_id'] . ' )';
        }
        else
        {
        	$listing_sql_query .= ' and e.parent_item_id in (select e.id from app_entity_' . $report_info['entities_id']. ' e where e.id>0 ' .  items::add_access_query($report_info['entities_id'],'') . ' ' . reports::add_filters_query($reports_info['parent_id'],'')  . ')';        	
        }
      }                   
    }
                    
    return $listing_sql_query;
  }
  
  public static function add_order_query($reports_order_fields, $entities_id)
  {
    $listing_sql_query_join = '';
    $listing_sql_query = '';
    
    $listing_order_fields_id = array();
    $listing_order_fields = array();
    $listing_order_clauses = array();
    
    foreach(explode(',',$reports_order_fields) as $key=>$order_field)
    {
      if(strlen($order_field)==0) continue; 
      
      $order = explode('_',$order_field);
      
      $alias = 'fc' . $key;
      
      $field_id = $order[0];
      $order_cause =  $order[1];
            
      //prepare sql for order by last comment date
      if($field_id=='lastcommentdate')
      {        
        $listing_order_fields[] = "(select comments.date_added from app_comments comments where comments.items_id=e.id and comments.entities_id='{$entities_id}' order by comments.date_added desc limit 1) " . $order_cause;
        
        continue;
      } 
           
      //prepare order for fields
      $field_info_query = db_query("select * from app_fields where id='" . db_input($field_id) . "'");
      if($field_info = db_fetch_array($field_info_query))
      {
        $listing_order_fields_id[]=$field_id;
        $listing_order_clauses[$field_id] = $order_cause;
        $field_cfg = new fields_types_cfg($field_info['configuration']);
        
                        
        if(in_array($field_info['type'],array('fieldtype_created_by','fieldtype_date_added','fieldtype_id')))
        {
          $listing_order_fields[] = 'e.' . str_replace('fieldtype_','',$field_info['type']) . ' ' . $order_cause;
        }
        elseif(in_array($field_info['type'],array('fieldtype_dropdown','fieldtype_dropdown_multiple','fieldtype_checkboxes','fieldtype_radioboxes','fieldtype_grouped_users')))
        {
          if($field_cfg->get('use_global_list')>0)
          {
            $listing_sql_query_join .= " left join app_global_lists_choices {$alias} on {$alias}.id=e.field_" . $field_id;
          }
          else
          {
            $listing_sql_query_join .= " left join app_fields_choices {$alias} on {$alias}.id=e.field_" . $field_id;            
          }
          
          $listing_order_fields[] = "{$alias}.sort_order " . $order_cause . ", {$alias}.name " . $order_cause;
        }
        elseif(in_array($field_info['type'],array('fieldtype_entity')))
        {
          $entity_info_query = db_query("select * from app_entities where id='" . $field_cfg->get('entity_id') . "'");
          if($entity_info = db_fetch_array($entity_info_query))
          {
            //if entity is Users then order by firstname/lastname
            if($entity_info['id']==1)
            {
              $listing_sql_query_join .= " left join app_entity_{$entity_info['id']} {$alias} on {$alias}.id=e.field_" . $field_id;
              $listing_order_fields[] = (CFG_APP_DISPLAY_USER_NAME_ORDER=='firstname_lastname' ? "{$alias}.field_7 {$order_cause}, {$alias}.field_8 {$order_cause}" : "{$alias}.field_8 {$order_cause}, {$alias}.field_7 {$order_cause}") ;
            }       
            //if exist haeading field then order by heading  
            elseif($heading_id = fields::get_heading_id($entity_info['id']))
            {
              $listing_sql_query_join .= " left join app_entity_{$entity_info['id']} {$alias} on {$alias}.id=e.field_" . $field_id;
              $listing_order_fields[] = "{$alias}.field_{$heading_id} " . $order_cause;
            }
            //default order by ID
            else
            {
              $listing_order_fields[] = 'e.field_' . $field_id . ' ' . $order_cause;
            }
          }                    
        }
        elseif(in_array($field_info['type'],array('fieldtype_input_numeric','fieldtype_input_numeric_comments','fieldtype_date_added','fieldtype_input_date','fieldtype_input_datetime')))
        {
          $listing_order_fields[] = '(e.field_' . $field_id . '+0) ' . $order_cause;
        }
        elseif(in_array($field_info['type'],array('fieldtype_formula')))
        {
          $listing_order_fields[] = 'field_' . $field_id . ' ' . $order_cause;
        }
        elseif(in_array($field_info['type'],array('fieldtype_parent_item_id')))
        {
          $entity_info = db_find('app_entities',$field_info['entities_id']);
          if($entity_info['parent_id']>0)
          {
            if($heading_id = fields::get_heading_id($entity_info['parent_id']))
            {
              $listing_sql_query_join .= " left join app_entity_{$entity_info['parent_id']} {$alias} on {$alias}.id=e.parent_item_id";
              $listing_order_fields[] = "{$alias}.field_{$heading_id} " . $order_cause;                                
            }
            else
            {
              $listing_order_fields[] = 'e.parent_item_id' . $order_cause;
            }
          } 
        }
        elseif(in_array($field_info['type'],array('fieldtype_attachments','fieldtype_input_file')))
        {
        	$listing_order_fields[] = 'SUBSTRING(e.field_' . $field_id . ',LOCATE("_",e.field_' . $field_id . ')) ' . $order_cause;
        }
        else
        {
          $listing_order_fields[] = 'e.field_' . $field_id . ' ' . $order_cause;
        }
      }      
    }
            
    if(count($listing_order_fields)>0)
    {
      $listing_sql_query .= " order by " . implode(',',$listing_order_fields);
    }
    else
    {
      $listing_sql_query .= " order by e.id ";
    }
    
    return array('listing_sql_query'        => $listing_sql_query, 
                 'listing_sql_query_join'   => $listing_sql_query_join,
                 'listing_order_fields_id'  => $listing_order_fields_id,
                 'listing_order_fields'     => $listing_order_fields,
                 'listing_order_clauses'    => $listing_order_clauses);    
  }
  
  
  public static function prepare_dates_sql_filters($filters, $prefix = 'e')
  {  
  	
  	$prefix = (strlen($prefix) ? $prefix : 'e');
  	
    if($filters['type']=='fieldtype_date_added')
    {
      $field_name = $prefix . '.date_added';
    }
    else
    {
      $field_name = $prefix . '.field_' . $filters['fields_id']; 
    }
     
    
    $sql = array();
    
    $values = explode(',',$filters['filters_values']);
              
    switch($filters['filters_condition'])        
    {
      case 'filter_by_days':
          if(strlen($values[0])>0)
          {
            if(strstr($values[0],'-'))
            {
              $use_function = 'DATE_SUB';
            }
            else
            {
              $use_function = 'DATE_ADD';  
            }
            
            $values[0] = str_replace(array('+','-'),'',$values[0]);
            
            $sql_or = array();
            foreach(explode('&',$values[0]) as $v)
            {         
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')=date_format(" . $use_function . "(now(),INTERVAL " . (int)$v . " DAY),'%Y-%m-%d')";                
            }
            
            if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
          }
          else
          {          
	          if(strlen($values[1])>0)
	          {
	          	$minutes = (strstr($values[1],':') ? ' %H:%i':'');
	          	
	            if(strtotime($values[1])<0)
	            {
	            	$sql[] = "DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL " . $field_name . " SECOND),'%Y-%m-%d{$minutes}')>='" . db_input($values[1])  . "'";
	            }
	            else
	            {	
	          		$sql[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d{$minutes}')>='" . db_input($values[1])  . "'";
	            }	            	            
	          }
	          
	          if(strlen($values[2])>0)
	          {
	          	$minutes = (strstr($values[2],':') ? ' %H:%i':'');
	          	
	          	if(strtotime($values[2])<0)
	          	{
	          		$sql[] = "DATE_FORMAT(DATE_ADD(FROM_UNIXTIME(0),INTERVAL " . $field_name . " SECOND),'%Y-%m-%d{$minutes}')<='" . db_input($values[2])  . "'";
	          	}
	          	else
	          	{
	            	$sql[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d{$minutes}')<='" . db_input($values[2])  . "'";
	          	}
	          	
	          	$sql[] = "{$field_name}>0";
	          }	          	          
          }
        break;
      case 'filter_by_week':
      
            $values = strlen($values[0])>0 ? $values[0] : 0;

            if(strstr($values,'-'))
            {
              $use_function = 'DATE_SUB';
            }
            else
            {
              $use_function = 'DATE_ADD';  
            }
            
            $values = str_replace(array('+','-'),'',$values);
            
            switch(CFG_APP_FIRST_DAY_OF_WEEK)
            {
              case '0':
                  $myslq_date_format = '%Y-%V';
                break;
              case '1':
                  $myslq_date_format = '%Y-%v';
                break;
            }
            
            $sql_or = array();
            foreach(explode('&',$values) as $v)
            {                       
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'" . $myslq_date_format. "')=date_format(" . $use_function. "(now(),INTERVAL " . (int)$v . " WEEK),'" . $myslq_date_format . "')";                                
            }
            
            if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
        
        break;  
      case 'filter_by_month':
      
            $values = strlen($values[0])>0 ? $values[0] : 0;

            if(strstr($values,'-'))
            {
              $use_function = 'DATE_SUB';
            }
            else
            {
              $use_function = 'DATE_ADD';  
            }
            
            $values = str_replace(array('+','-'),'',$values);
            
            $sql_or = array();
            foreach(explode('&',$values) as $v)
            {                       
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m')=date_format(" . $use_function. "(now(),INTERVAL " . (int)$v . " MONTH),'%Y-%m')";                                
            }
            
            if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
        
        break;
      case 'filter_by_year':
            $values = strlen($values[0])>0 ? $values[0] : 0;

            if(strstr($values,'-'))
            {
              $use_function = 'DATE_SUB';
            }
            else
            {
              $use_function = 'DATE_ADD';  
            }
            
            $values = str_replace(array('+','-'),'',$values);
            
            $sql_or = array();
            foreach(explode('&',$values) as $v)
            {                       
              $sql_or[] = "FROM_UNIXTIME(" . $field_name . ",'%Y')=date_format(" . $use_function. "(now(),INTERVAL " . (int)$v . " YEAR),'%Y')";                                
            }
            
            if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
        break;
      case 'filter_by_overdue':
            $sql[] = "FROM_UNIXTIME(" . $field_name . ",'%Y-%m-%d')<date_format(now(),'%Y-%m-%d') and length(" . $field_name . ")>0";
        break;
      
    }
            
    return $sql;
  }
  
  public static function prepare_numeric_sql_filters($filters, $prefix = 'e')
  {
    $values = preg_split("/(&|\|)/",$filters['filters_values'],null,PREG_SPLIT_DELIM_CAPTURE);
           
    if(strlen($values[0])>0)
    {
    	$values[1] = (isset($values[1]) ? $values[1] : '');
    	
      if($values[1]=='|')
      {
        $values = array_merge(array('','|'),$values);
      }
      else
      {
        $values = array_merge(array('','&'),$values);
      }
    }
    
    $sql = array();
    $sql_and = array();
    $sql_or = array();
    
    if(strlen($prefix)) $prefix .= '.';
            
    for($i=1;$i<count($values);$i+=2)
    {
      if(preg_match("/!=|>=|<=|>|</",$values[$i+1],$matches))
      {        
        $operator = $matches[0];
        $value = (float)str_replace($matches[0],'',$values[$i+1]);
      }
      else
      {
        $operator = '=';
        $value = (float)$values[$i+1];
      }
                  
      switch($values[$i])
      {
        case '|':
            $sql_or[] =  $prefix . 'field_' . $filters['fields_id'] . $operator . $value;
          break;
        case '&':
            $sql_and[] = $prefix . 'field_' . $filters['fields_id'] . $operator . $value;
          break;
      }
      
    }            
    
    if(count($sql_or)>0) $sql[] = "(" . implode(' or ', $sql_or) . ")";
    if(count($sql_and)>0) $sql[] = "(" . implode(' and ', $sql_and) . ")";
    
    return $sql;
  }
  
  public static function render_filters_dropdown_menu($report_id,$path='',$redirect_to='report',$parent_reports_id=0)
  {  
    $url_params = '';
    
    if(strlen($path)>0)
    {
      $url_params = '&path=' . $path;      
    }
    
    $parent_reports_param = '';
    if($parent_reports_id>0)
    {
      $url_params .= '&parent_reports_id=' . $parent_reports_id;
      
      $report_info = db_find('app_reports',$parent_reports_id);          
    }
    else
    {
      $report_info = db_find('app_reports',$report_id);  
    }
    
    $entity_info = db_find('app_entities',$report_info['entities_id']);
    
    
    
    $count_filters = 0;
    $html = '<ul class="dropdown-menu" role="menu">';
    $html .= '<li>' . link_to_modalbox(TEXT_FILTERS_FOR_ENTITY_SHORT . ': <b>' . $entity_info['name'] . '</b>',url_for('reports/filters_form','reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )) . '</li>';
    $html .= '<li class="divider"></li>';
    
    $filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf, app_fields f  where rf.fields_id=f.id and rf.reports_id='" . db_input(($parent_reports_id>0 ? $parent_reports_id:$report_id)) . "' order by rf.id");
    while($v = db_fetch_array($filters_query))
    {
      
      $edit_url = url_for('reports/filters_form','id=' . $v['id'] . '&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params);
      $delete_url = url_for('reports/filters','action=delete&id=' . $v['id'] . '&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params);
      
      if(in_array($v['filters_condition'],array('empty_value','filter_by_overdue')))
      {
        $fitlers_values = reports::get_condition_name_by_key($v['filters_condition']);
      }
      else
      {
        $fitlers_values = reports::render_filters_values($v['fields_id'],$v['filters_values'],'<br>',$v['filters_condition']); 
      }
      
      $html .= '
        <li class="dropdown-submenu">' . link_to_modalbox(fields_types::get_option($v['type'],'name',$v['name']),$edit_url) . '
          <ul class="dropdown-menu">
            <li class="filters-values-content">
              '  . link_to_modalbox($fitlers_values,$edit_url) . '
            </li>
            <li class="divider"></li>
            <li>
      				' . link_to('<i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_REMOVE_FILTER,$delete_url). '
      			</li>
          </ul>
        </li>
      ';
      
      $count_filters++;
    }
    $html .= '
      <li class="divider"></li>
			<li>
				' . link_to_modalbox('<i class="fa fa-plus-circle"></i> ' . TEXT_BUTTON_ADD_NEW_REPORT_FILTER,url_for('reports/filters_form','reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )). '
			</li>
      ' . ($count_filters>0 ? '      
      <li>
				' . link_to('<i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_REMOVE_ALL_FILTERS, url_for('reports/filters','action=delete&id=all&reports_id=' . $report_id . '&redirect_to=' . $redirect_to . $url_params )). '
			</li>':'') . '
    </ul>';
    
    return $html;
  
  }
  
  public static function render_filters_values($fields_id,$filters_values, $separator = '<br>',$filters_condition)
  {
    global $app_choices_cache, $app_users_cache, $app_global_choices_cache;
    
    $field_info = db_find('app_fields',$fields_id);
    
    $html = '';
          
    switch($field_info['type'])
    {
    	case 'fieldtype_user_accessgroups':
	    		$list = array();
	    		foreach(explode(',',$filters_values) as $id)
	    		{	    			
    				if(strlen($name = access_groups::get_name_by_id($id)))
    				{
    					$list[] = $name;
    				}
	    			
	    		}
	    		
	    		$html = implode($separator,$list);
    		break;
    	case 'fieldtype_user_status':
    			$html = ($filters_values==1 ? TEXT_ACTIVE : TEXT_INACTIVE);
    		break;
      case 'fieldtype_parent_item_id':
                      
          $entity_info = db_find('app_entities',$field_info['entities_id']);
                    
          $output = array();
          foreach(explode(',',$filters_values) as $item_id)
          {
            $items_info_sql = "select e.* from app_entity_" . $entity_info['parent_id'] . " e where e.id='" . db_input($item_id). "'";
            $items_query = db_query($items_info_sql);            
            if($item = db_fetch_array($items_query))
            {                            
              $output[]  = items::get_heading_field($entity_info['parent_id'],$item['id']);
            }
          }
          
          $html = implode($separator,$output);
            
        break;
      case 'fieldtype_related_records':
          $html = ($filters_values=='include' ? TEXT_FILTERS_DISPLAY_WITH_RELATED_RECORDS:TEXT_FILTERS_DISPLAY_WITHOUT_RELATED_RECORDS);
        break;
      case 'fieldtype_entity':
            
        $cfg = fields_types::parse_configuration($field_info['configuration']);
        
        $field_heading_id = 0;
        $fields_query = db_query("select f.* from app_fields f where f.is_heading=1 and  f.entities_id='" . db_input($cfg['entity_id']) . "'");
        if($fields = db_fetch_array($fields_query))
        {
          $field_heading_id = $fields['id'];        
        }
        
        $output = array();
        foreach(explode(',',$filters_values) as $item_id)
        {
          $items_info_sql = "select e.* from app_entity_" . $cfg['entity_id'] . " e where e.id='" . db_input($item_id). "'";
          $items_query = db_query($items_info_sql);
          if($item = db_fetch_array($items_query))
          {           
            if($cfg['entity_id']==1)
            {
              $output[]  = $app_users_cache[$item['id']]['name'];
            }
            elseif($field_heading_id>0)
            {
              $output[]  = items::get_heading_field_value($field_heading_id,$item);
            }
            else
            {
              $output[]  = $item['id'];
            }                                          
          }
        } 
        
        $html = implode($separator,$output); 
        break;
      case 'fieldtype_formula':
      case 'fieldtype_input_numeric':     
      case 'fieldtype_input_numeric_comments':
          $html = $filters_values;        
        break;
      case 'fieldtype_checkboxes':
      case 'fieldtype_radioboxes':
      case 'fieldtype_dropdown':
      case 'fieldtype_dropdown_multiple':
      case 'fieldtype_grouped_users':
      
          $cfg = new fields_types_cfg($field_info['configuration']);
                    
          
          $list = array();
          foreach(explode(',',$filters_values) as $id)
          {
            if($cfg->get('use_global_list')>0)
            {
              if(isset($app_global_choices_cache[$id]))
              {
                $list[] = $app_global_choices_cache[$id]['name']; 
              }
            }
            else
            {
              if(isset($app_choices_cache[$id]))
              {
                $list[] = $app_choices_cache[$id]['name']; 
              }
            }
          }
          
          $html = implode($separator,$list);
          
        break;
      case 'fieldtype_boolean':
          $html = fieldtype_boolean::get_boolean_value($field_info,$filters_values);
        break;  
      case 'fieldtype_date_added':
      case 'fieldtype_input_date':
      case 'fieldtype_input_datetime':
          $values = explode(',',$filters_values);
          
          if(strlen($values[0])>0)
          {
          	switch($filters_condition)
          	{
          		case 'filter_by_days':
          				$html = TEXT_FILTER_BY_DAYS;
          			break;
          		case 'filter_by_week':
          				$html = TEXT_FILTER_BY_WEEK;
          			break;
          		case 'filter_by_month':
          				$html = TEXT_FILTER_BY_MONTH;
          			break;
          		case 'filter_by_year':
          				$html = TEXT_FILTER_BY_YEAR;
          			break;
          	}
          	
            $html .=  ': ' . $values[0];
          }
          else
          {                    
	          if(strlen($values[1])>0)
	          {
	          	$value = ($field_info['type']=='fieldtype_input_date' ? format_date(get_date_timestamp($values[1])) : format_date_time(get_date_timestamp($values[1])));
	            $html =  TEXT_DATE_FROM . ': ' . $value . ' ';
	          }
	          
	          if(strlen($values[2])>0)
	          {
	          	$value = ($field_info['type']=='fieldtype_input_date' ? format_date(get_date_timestamp($values[2])) : format_date_time(get_date_timestamp($values[2])));
	            $html .=  TEXT_DATE_TO . ': ' . $value . ' ';
	          }
          }
          
          
        break;  
      case 'fieldtype_created_by':            
      case 'fieldtype_users':
          $list = array();
          foreach(explode(',',$filters_values) as $id)
          {
            if(isset($app_users_cache[$id]))
            {
              $list[] = $app_users_cache[$id]['name']; 
            }
          }
          
          $html = implode($separator,$list);                  
        break;
    }
                
    return $html;
  }
  
  public static function get_condition_name_by_key($condition)
  {
    switch($condition)
    {
      case 'include':
          return TEXT_CONDITION_INCLUDE;
        break;
      case 'exclude':
          return TEXT_CONDITION_EXCLUDE;
        break;
      case 'empty_value':
          return TEXT_CONDITION_EMPTY_VALUE;
        break;
      case 'filter_by_overdue':
          return TEXT_FILTER_BY_OVERDUE_DATE;
        break;
    }
  }
  
  public static function get_count_fixed_columns($reports_id)
  {
    $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id) . "'");
    if($reports_info = db_fetch_array($reports_info_query))
    {
      $cfg = entities::get_cfg($reports_info['entities_id']);
      
      $number_fixed_field = (int)$cfg['number_fixed_field_in_listing'];
      $number_fixed_field = ($number_fixed_field>0 ? ($number_fixed_field+1):0);
      
      return $number_fixed_field;
    }    
  }
}