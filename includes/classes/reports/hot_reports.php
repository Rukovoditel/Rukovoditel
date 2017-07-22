<?php

class hot_reports
{
  public $poup_items_limit;
  
  function __construct()
  {
    //set limit items in reports popup
    $this->poup_items_limit = 25;
  }
  
  //render reports header navitagion menu
  function render()
  {      
    $html = '';  
                
    $reports_query = db_query($this->reports_query());
    while($reports = db_fetch_array($reports_query))
    {
    	//set off $this->render_dropdown($reports['id']) to speed up
      $html .= '
        <li class="dropdown hot-reports" id="hot_reports_' . $reports['id'] . '" data-id="' . $reports['id'] . '">
          ' . '
        </li>
        
        <script>
          function hot_reports_' . $reports['id'] . '_render_dropdown()
          {
            $("#hot_reports_' . $reports['id'] . '").load("' . url_for("dashboard/","action=update_hot_reports&reports_id=" . $reports['id']) . '",function(){
                $(\'[data-hover="dropdown"]\').dropdownHover();                
              })
          }
            		
          $(function(){
             setInterval(function(){
              hot_reports_' . $reports['id'] . '_render_dropdown()
             },60000);                                                                   
          });
              		
          hot_reports_' . $reports['id'] . '_render_dropdown()
          		
        </script>
      ';
    }
    
    return $html;
  }
  
  //get reports items list with count
  function get_items($report_info,$options = array())
  {
  	global $sql_query_having;
  	
  	$listing_sql_query_select = '';
  	$listing_sql_query = '';
    $listing_sql_query_join = '';
    $listing_sql_query_having = '';
    $sql_query_having = array();
    
    //prepare formulas query
    $listing_sql_query_select = fieldtype_formula::prepare_query_select($report_info['entities_id'], $listing_sql_query_select);
    
    //prepare listing query        
    $listing_sql_query = reports::add_filters_query($report_info['id'],$listing_sql_query);
               
    //prepare having query for formula fields
    if(isset($sql_query_having[$report_info['entities_id']]))
    {
    	$listing_sql_query_having  = reports::prepare_filters_having_query($sql_query_having[$report_info['entities_id']]);
    }
    
    //check view assigned only access
    $listing_sql_query = items::add_access_query($report_info['entities_id'],$listing_sql_query);
    
    //add having query
    $listing_sql_query .= $listing_sql_query_having;
    
    //prepare order query
    $info = reports::add_order_query($report_info['listing_order_fields'],$report_info['entities_id']);
    $listing_sql_query .= $info['listing_sql_query'];
    $listing_sql_query_join .= $info['listing_sql_query_join'];
            
    //get heading field        
    $field_heading_id = fields::get_heading_id($report_info['entities_id']);
    
    $items_array = array();                    
        
    $count = 0;
    $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $report_info['entities_id'] . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query . " ";
    $items_query = db_query($listing_sql);
    $count_items = db_num_rows($items_query);
    while($item = db_fetch_array($items_query))
    {
      $path_info = items::get_path_info($report_info['entities_id'],$item['id']);
      
      $parent_name = '';
      
    	if(strlen($path_info['parent_name'])>0)
      {
      	$parent_name_array = explode('<br>',$path_info['parent_name']);
       	krsort($parent_name_array);
       	
       	if(isset($options['is_email']))
       	{
       		$parent_name = '<span style="color: #9A9A9A;"> &laquo; ' .implode(' &laquo; ',$parent_name_array) . '</span>';
       	}
       	else
       	{
       		$parent_name = '<span class="parent-name"><i class="fa fa-angle-left"></i>' .implode('<i class="fa fa-angle-left"></i>',$parent_name_array) . '</span>';
       	}
      }
      
      $items_array[] = array('id'   => $item['id'],
                             'path' => $path_info['full_path'], 
                             'name' =>   ($field_heading_id ? items::get_heading_field_value($field_heading_id,$item) : $item['id']) . $parent_name);
      $count++;
      
      if($count==$this->poup_items_limit)
      {
        break;
      }
    }
    
        
    return array('items_count'=> $count_items,'items_array'=>$items_array);
  }
  
  //render reports nav menu dropdown
  function render_dropdown($id)
  {
    $html = '';
    $report_info_query = db_query("select * from app_reports where id='" . db_input($id) . "'");
    if($report_info = db_fetch_array($report_info_query))
    {
      $entity_cfg = entities::get_cfg($report_info['entities_id']);
      
      
      $items_info = $this->get_items($report_info);
      
      $items_html = '';
      foreach($items_info['items_array'] as $v)
      {
        $items_html .= '
          <li>
  					<a href="' . url_for('items/info','path=' . $v['path']) . '">' . $v['name'] . '</a>
  				</li>
        ';
      }
      
      if($items_info['items_count']==0)
      {
      	$items_html .= '
          <li>
  					<a onClick="return false;">' . TEXT_NO_RECORDS_FOUND . '</a>
  				</li>
        ';
      }
      
      $external_html = '';
      if($items_info['items_count']>$this->poup_items_limit)
      {
        $external_html = '
          <li class="external">
						<a href="' . url_for('reports/view','reports_id=' . $report_info['id']) . '">' . sprintf(TEXT_DISPLAY_NUMBER_OF_ITEMS,1,$this->poup_items_limit,$items_info['items_count']) . '</a>
					</li>
        ';
      }
      
      $dropdown_menu_height = (count($items_info['items_array'])<11 ? (count($items_info['items_array'])*42+42) : 420);
      
      $badge_html = ($items_info['items_count']>0 ? '<span class="badge badge-warning">' . $items_info['items_count'] . '</span>' : '');
      
      $menu_icon = (strlen($report_info['menu_icon']) ? $report_info['menu_icon'] : (strlen($entity_cfg['menu_icon'])>0 ? $entity_cfg['menu_icon'] : 'fa-reorder') );
      
      $html = '
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				  <i class="fa ' . $menu_icon . '"></i>
				  ' . $badge_html . '
				</a>
				<ul class="dropdown-menu extended tasks">
					<li style="cursor:pointer" onClick="location.href=\'' . url_for('reports/view','reports_id=' . $report_info['id']) . '\'">
						<p>' . $report_info['name'] . '</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
							' . $items_html . '
              ' . $external_html . '  
						</ul>
					</li>
          
				</ul>            
      ';
    }
    
    return $html;;
  }
  
  //build hot reports query with common reports
  function reports_query()
  {
    global $app_logged_users_id, $app_user, $app_users_cfg;
    
    $where_sql = '';
    
    //check hidden common reports
    if(strlen($app_users_cfg->get('hidden_common_reports'))>0)
    {
      $where_sql = " and r.id not in (" . $app_users_cfg->get('hidden_common_reports') . ")";
    }
        
    //get common reports list
    $common_reports_list = array();    
    $reports_query = db_query("select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and find_in_set(" . $app_user['group_id'] . ",r.users_groups) and r.in_header=1 and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name");
    while($reports = db_fetch_array($reports_query))
    {
      $common_reports_list[] = $reports['id'];
    }
    
    //create reports query inclue common reports            
    $reports_query = "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and ((r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and  r.in_header=1)  " . (count($common_reports_list)>0 ? " or r.id in(" . implode(',',$common_reports_list). ")" : "") . ") order by r.header_sort_order, r.dashboard_sort_order, r.name";
        
    return $reports_query;
  }
}