<?php

$listing = new items_listing($_POST['reports_id']);

$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
$current_entity_info = db_find('app_entities',$current_entity_id);
$entity_cfg = entities::get_cfg($current_entity_id);

$user_has_comments_access = users::has_comments_access('view');
      
$html = '';

$listing_sql_query_select = '';
$listing_sql_query = '';
$listing_sql_query_join = '';
$listing_sql_query_from = '';
$listing_sql_query_having = '';
$sql_query_having = array();

if(!isset($_POST['search_keywords'])) $_POST['search_keywords'] = '';
if(!isset($_POST['search_reset'])) $_POST['search_reset'] = '';
if(!isset($_POST['force_display_id'])) $_POST['force_display_id'] = '';
if(!isset($_POST['force_popoup_fields'])) $_POST['force_popoup_fields'] = '';
if(!isset($_POST['force_filter_by'])) $_POST['force_filter_by'] = '';

//prepare forumulas query
$listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, $listing_sql_query_select,false,array('reports_id'=>$_POST['reports_id']));

//prepare count of related items in listing
$listing_sql_query_select = fieldtype_related_records::prepare_query_select($current_entity_id, $listing_sql_query_select);


//add search query and skip filters to search in all items
if(strlen($_POST['search_keywords'])>0)
{
  $html .= '<div class="note note-info search-notes">' . sprintf(TEXT_SEARCH_RESULT_FOR,htmlspecialchars($_POST['search_keywords'])) . ' <span onClick="listing_reset_search(\'' . $_POST['listing_container'] . '\')" class="reset_search">' . TEXT_RESET_SEARCH . '</span></div>';
  require(component_path('items/add_search_query')); 
}

if(strlen($_POST['search_keywords'])>0 or $_POST['search_reset']=='true')
{
	//save search settings for current report
	listing_search::save($_POST['reports_id']);
}

//default search include reports fitlers
//if flga "search_in_all" = true we exlude fitlers from search
if((strlen($_POST['search_keywords'])>0 and $_POST['search_in_all']=='true') or strlen($_POST['force_display_id']))
{
	//skip filters if there is search keyworkds and option search_in_all in 
}
else
{
  //add filters query
  if(isset($_POST['reports_id']))
  {
    $listing_sql_query = reports::add_filters_query($_POST['reports_id'],$listing_sql_query);
        
    //prepare having query for formula fields
    if(isset($sql_query_having[$current_entity_id]))
    {    	
    	$listing_sql_query_having  = reports::prepare_filters_having_query($sql_query_having[$current_entity_id]);
    }
  }
}

//filter items by parent
if($parent_entity_item_id>0)
{
  $listing_sql_query .= " and e.parent_item_id='" . db_input($parent_entity_item_id) . "'";
}

//exclude admin users from listing for not admin users
if($current_entity_id==1 and $app_user['group_id']>0)
{
	$listing_sql_query .= " and e.field_6>0";
}

//force display items by ID
if(strlen($_POST['force_display_id']))
{
	$listing_sql_query .= " and e.id in (" . $_POST['force_display_id'] . ")";
}

//force extra filter
if(strlen($_POST['force_filter_by']))
{
	$listing_sql_query .= reports::force_filter_by($_POST['force_filter_by']);
}	

//check view assigned only access
$listing_sql_query = items::add_access_query($current_entity_id,$listing_sql_query,$listing->force_access_query);

//add having query
$listing_sql_query .= $listing_sql_query_having;

//add order_query
$listing_order_fields_id = array();
$listing_order_fields = array();
$listing_order_clauses = array();

if(strlen($_POST['listing_order_fields'])>0)
{  
  $info = reports::add_order_query($_POST['listing_order_fields'],$current_entity_id);
      
  $listing_order_fields_id = $info['listing_order_fields_id'];
  $listing_order_fields = $info['listing_order_fields'];
  $listing_order_clauses = $info['listing_order_clauses'];
  
  $listing_sql_query .= $info['listing_sql_query'];
  $listing_sql_query_join .= $info['listing_sql_query_join'];  
  $listing_sql_query_from .= $info['listing_sql_query_from'];
}

$reports_entities_id = (isset($_POST['reports_entities_id']) ? $_POST['reports_entities_id'] : 0);

$has_with_selected = (isset($_POST['has_with_selected']) ? $_POST['has_with_selected'] : 0);

$html .= '
<div class="table-scrollable">
  <div class="table-scrollable table-wrapper">
    <table class="table table-striped table-bordered table-hover" data-count-fixed-columns="' . reports::get_count_fixed_columns($_POST['reports_id'],$has_with_selected) . '">
      <thead>
        <tr>
          ' . ($has_with_selected ?  '<th>' . input_checkbox_tag('select_all_items',$_POST['reports_id'],array('class'=>'select_all_items')) . '</th>' : '');

//render listing heading
$listing_fields = array();   
$listing_numeric_fields = array();
$fields_query = db_query($listing->get_fields_query());
while($v = db_fetch_array($fields_query))
{      
  //check field access
  if(isset($fields_access_schema[$v['id']]))
  {
    if($fields_access_schema[$v['id']]=='hide') continue;
  }
  
  //skip fieldtype_parent_item_id for deafult listing
  if($v['type']=='fieldtype_parent_item_id' and (strlen($app_redirect_to)==0 or $current_entity_info['parent_id']==0 or $listing->report_type=='parent_item_info_page'))
  {
    continue;      
  }
      
  if(!in_array($v['type'],fields_types::get_types_excluded_in_sorting()))
  {
    if(!isset($listing_order_clauses[$v['id']])) 
    {
      $listing_order_clauses[$v['id']] = 'asc';
    }
    
    $listing_order_action = 'onClick="listing_order_by(\'' . $_POST['listing_container'] . '\',\'' . $v['id'] . '\',\'' . (($listing_order_clauses[$v['id']]=='asc' and in_array($v['id'],$listing_order_fields_id)) ? 'desc':'asc'). '\')"';
  }
  else
  {
    $listing_order_action = '';
  }
  
  $th_css_class = $v['type'] . '-th filed-' . $v['id'] . '-th';
  
  if(in_array($v['id'],$listing_order_fields_id))
  {
    $listing_order_css_class = 'class="' . $th_css_class . ' listing_order listing_order_' . $listing_order_clauses[$v['id']] .'"';
  }
  else
  {
    $listing_order_css_class = 'class="' . $th_css_class . ' listing_order"';
  }   
  
  
  if($v['type']=='fieldtype_dropdown_multilevel')
  {
  	$html .= fieldtype_dropdown_multilevel::output_listing_heading($v);  
  }
  else
  {
	  $html .= '
	      <th ' . $listing_order_action . ' ' . $listing_order_css_class . '><div>' . fields_types::get_option($v['type'],'name',$v['name']) . '</div></th>
	  ';
  }
  
  $listing_fields[] = $v;
  
  $field_cfg = new fields_types_cfg($v['configuration']);
  
  if(in_array($v['type'],array('fieldtype_input_numeric','fieldtype_formula','fieldtype_js_formula','fieldtype_input_numeric_comments')) and ($field_cfg->get('calclulate_totals')==1 or $field_cfg->get('calculate_average')==1))
  {
    $listing_numeric_fields[] = $v['id']; 
  }
}  
 
       
$html .= '
    </tr>
  </thead>
  <tbody>        
';


if(!isset($app_selected_items[$_POST['reports_id']]))
{
  $app_selected_items[$_POST['reports_id']] = array();
}

//setup unread items
$users_notifications = new users_notifications($current_entity_id);

//render listing body
$listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . $listing_sql_query_from . " where e.id>0 " . $listing_sql_query;

//if there is having query then use db_num_rows function to calculate num rows
if(strlen($listing_sql_query_having)>0)
{
	$count_sql = 'query_num_rows';
}
else
{
	$count_sql = "select count(e.id) as total from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;
}

//$count_sql = 'query_num_rows';

$listing_split = new split_page($listing_sql,$_POST['listing_container'],$count_sql, $listing->rows_per_page);

$items_query = db_query($listing_split->sql_query,false);

while($item = db_fetch_array($items_query))
{
  $html .= '
      <tr ' . ($users_notifications->has($item['id']) ? 'class="unread-item-row"':''). '>
        ';

//perpare selected checkbox  
  $hide_actions_buttons = false;
  
  if($has_with_selected)
  {
  	$checkbox_html = '<td>' . input_checkbox_tag('items_' . $item['id'],$item['id'],array('class'=>'items_checkbox','checked'=>in_array($item['id'],$app_selected_items[$_POST['reports_id']]))) . '</td>';
  	
  	//check access to action with assigned only
  	if(users::has_users_access_name_to_entity('action_with_assigned',$current_entity_id))
  	{
  		if(users::has_access_to_assigned_item($current_entity_id,$item['id']))
  		{
  			$html .= $checkbox_html;
  		}
  		else 
  		{
  			$html .= '<td></td>';
  			
  			$hide_actions_buttons = true;
  		}
  	}
  	else 
  	{
  		$html .= $checkbox_html;
  	}
  }
//end prepare selected checkbox  
  
  $path_info_in_report = array();
  
  if($reports_entities_id>0  and $current_entity_info['parent_id']>0)
  {
    $path_info_in_report = items::get_path_info($_POST['reports_entities_id'],$item['id'],$item);        
  }
  
  
  foreach($listing_fields as $field)
  {
  
    //check field access
    if(isset($fields_access_schema[$field['id']]))
    {
      if($fields_access_schema[$field['id']]=='hide') continue;
    }
    
    
    if($field['type']=='fieldtype_parent_item_id' and (strlen($app_redirect_to)==0 or $current_entity_info['parent_id']==0  or $listing->report_type=='parent_item_info_page'))
    {
      continue;      
    }
    
    //prepare field value
    $value = items::prepare_field_value_by_type($field, $item);  
        
    $output_options = array(
    		'class'       => $field['type'],
        'value'       => $value,
        'field'       => $field,
        'item'        => $item,
        'is_listing'  => true,                                                        
        'redirect_to' => $app_redirect_to,
        'reports_id'  => ($reports_entities_id>0 ? $_POST['reports_id']:0),
        'path'        => (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path),
        'path_info'   => $path_info_in_report,
    		'hide_actions_buttons' => $hide_actions_buttons,
    );
                            
                                    
    if($field['is_heading']==1)
    {  
    	//get fields in popup
    	$popup_html = '';
    	if(strlen($_POST['force_popoup_fields']))
    	{    		
    		$fields_in_popup = fields::get_items_fields_data_by_id($item,$_POST['force_popoup_fields'],$current_entity_id,$fields_access_schema);
    		
    		if(count($fields_in_popup))
    		{
    			$popup_html = app_render_fields_popup_html($fields_in_popup);
    		}
    	}
    	
      $path = (isset($path_info_in_report['full_path']) ? $path_info_in_report['full_path']  :$current_path . '-' . $item['id']);
            
      $html .= '
          <td class="' . $field['type'] . ' item_heading_td' . ($entity_cfg['heading_width_based_content']==1 ? ' width-auto':'') . '"><a ' . $popup_html . ' class="item_heading_link" href="' . url_for('items/info', 'path=' . $path . '&redirect_to=subentity') . '">' . fields_types::output($output_options) . '</a>
      ';
      
      if($entity_cfg['use_comments']==1 and $user_has_comments_access)
      {
        $html .= comments::get_last_comment_info($current_entity_id,$item['id'],$path, $fields_access_schema);
      }
      
      $html .= '</td>';
    }
    elseif($field['type']=='fieldtype_dropdown_multilevel')
    {
    	$html .= fieldtype_dropdown_multilevel::output_listing($output_options);
    }
    else
    {
      $td_class = (in_array($field['type'],array('fieldtype_action','fieldtype_date_added','fieldtype_input_datetime')) ? 'class="' . $field['type'] . ' nowrap"':'class="' . $field['type'] . '"');      
      $html .= '
          <td ' . $td_class . '>' . fields_types::output($output_options) . '</td>
      ';
    } 
  }
     
  $html .= '
      </tr>
  ';
}

if($listing_split->number_of_rows==0)
{
  $html .= '
    <tr>
      <td colspan="100">' . TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  '; 
}
                
$html .= '
  </tbody>';

if(count($listing_numeric_fields)>0)
{
  require(component_path('items/calculate_fields_totals'));
}

$html .= '  
    </table>
  </div>
</div>
';


//add pager
$html .= '
<div class="row">
  <div class="col-md-4 col-sm-12">' . $listing_split->display_count() . '</div>
  <div class="col-md-8 col-sm-12">' . $listing_split->display_links(). '</div>
</div>      
';

echo $html;

