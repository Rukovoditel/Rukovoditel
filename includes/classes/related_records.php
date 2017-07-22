<?php

class related_records
{
  public $entities_id;
  
  public $items_id;
  
  public $field;
  
  public $cfg;
  
  public $entities_access_schema;
  
  public $current_entities_access_schema;
  
  function __construct($entities_id, $items_id)
  {
    global $app_user;
    
    $this->entities_id = $entities_id;
    $this->items_id = $items_id;
    $this->current_entities_access_schema = users::get_entities_access_schema($this->entities_id,$app_user['group_id']);
  }
  
  function set_related_field($fields_id)
  {
    $field = db_find('app_fields',$fields_id);
    $this->field = $field; 
    $this->cfg = new fields_types_cfg($field['configuration']);
  }
  
  function render_as_single_list($as_single_list = true)
  {
    global $app_user, $current_path;

    $html = '';
    
    $fields_access_schema = users::get_fields_access_schema($this->entities_id,$app_user['group_id']);
    
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input($this->entities_id) . "' and f.forms_tabs_id=t.id  order by t.sort_order, t.name, f.sort_order, f.name");
    while($field = db_fetch_array($fields_query))
    { 
    
      $this->cfg = new fields_types_cfg($field['configuration']);
      
      //skip fields that will not dipslay as single list
      if($as_single_list==true and $this->cfg->get('display_in_main_column')!=1)
      {
        continue;
      }
      
      if($as_single_list==false and $this->cfg->get('display_in_main_column')==1)
      {
        continue;
      }
               
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
                                    
      $this->entities_access_schema = users::get_entities_access_schema($this->cfg->get('entity_id'),$app_user['group_id']);
      
      //checking view access
      if(!users::has_access('view',$this->entities_access_schema) and  !users::has_access('view_assigned',$this->entities_access_schema))
      {
        continue;
      }
      
                         
      //render list
      $this->field = $field;
        
      $html .= $this->render_single_list();   
        
    }
     
    return $html;
  }
  
  function render_single_list()
  {
    global $current_path;
  
    $count_related_items = $this->count_related_items();
    
    $html = '
    <div class="portlet portlet-related-items">
			<div class="portlet-title">
				<div class="caption">        
          ' . fields_types::get_option($this->field['type'],'name',$this->field['name']) . '&nbsp;(<span id="related_items_count_' . $this->field['id'] . '">' . $count_related_items .'</span>)             
        </div>
        <div class="tools">
					<a href="javascript:;" class="collapse"></a>
				</div>
        
			</div>
			<div class="portlet-body">
        
        ' . ($count_related_items>0 ? $this->render_list($this->field) : '') . '
        
        ' . (users::has_access('update',$this->current_entities_access_schema) ? 
              '<div class="action-button">        
                ' . link_to_modalbox('<i class="fa fa-plus"></i> ' . TEXT_BUTTON_ADD,$this->get_add_url(),array('class'=>'btn btn-default btn-xs')) . ' &nbsp;          
                ' . link_to_modalbox('<i class="fa fa-link"></i> ' . TEXT_BUTTON_LINK,url_for('items/link_related_item','path=' . $current_path . '&related_entities=' . $this->cfg->get('entity_id')),array('class'=>'btn btn-default btn-xs')) . ' &nbsp;        
                ' . ($count_related_items>0 ? link_to_modalbox('<i class="fa fa-unlink" title="' . htmlspecialchars(TEXT_UNLINK) . '"></i>',url_for('items/unlink_related_item','path=' . $current_path . '&fields_id=' . $this->field['id'] . '&related_entities_id=' . $this->cfg->get('entity_id')),array('class'=>'btn btn-default btn-xs')) : '') . ' &nbsp;
              </div>' 
              : '') .'        
      </div>
    </div>    
    ';
    
    return $html;
  }
  
  function get_add_url()
  {
    global $current_path_array;
    
    $entity_info = db_find('app_entities',$this->cfg->get('entity_id'));
    $current_entity_info = db_find('app_entities',$this->entities_id);
    
//if parent items are different    
    if($entity_info['parent_id']!=$current_entity_info['parent_id'] and $entity_info['parent_id']>0)
    {
      $add_url = url_for('reports/prepare_add_item','reports_id=' . reports::get_default_entity_report_id($this->cfg->get('entity_id'),'entity_menu') . '&related=' . $this->entities_id . '-' . $this->items_id);
    }
    //if parent items are the same
    elseif($entity_info['parent_id']==$current_entity_info['parent_id'] and $entity_info['parent_id']>0)
    {     
      $path = app_get_path_to_parent_item($current_path_array) . '/' . $this->cfg->get('entity_id');
      
      $add_url = url_for('items/form','path=' . $path . '&related=' . $this->entities_id . '-' . $this->items_id);
    }
    else
    {
      $path = $this->cfg->get('entity_id');
      
      $add_url = url_for('items/form','path=' . $path . '&related=' . $this->entities_id . '-' . $this->items_id);
    }  
    
    return $add_url;   
  }
  
  function render_list()
  {
    global $app_user, $parent_entity_item_id, $current_path;
        
    $entity_info = db_find('app_entities',$this->cfg->get('entity_id'));            
    $entity_cfg = entities::get_cfg($this->cfg->get('entity_id'));
    $fields_access_schema = users::get_fields_access_schema($this->cfg->get('entity_id'),$app_user['group_id']);
    
    $comments_access_schema = users::get_comments_access_schema($this->cfg->get('entity_id'),$app_user['group_id']);
    $user_has_comments_access = users::has_comments_access('view',$comments_access_schema);
                 
    $fields_in_listing = (strlen($this->cfg->get('fields_in_listing'))>0 ? $this->cfg->get('fields_in_listing') :0); 
    
    
    $css_scrollalbe = ($this->cfg->get('rows_per_page')>0 ? '':'table-scrollable');
    
    $html = '
  <div class="related_itmes_listing">  
    <div class="' . $css_scrollalbe . '">
      <div class="' . $css_scrollalbe . ' table-wrapper">
        <table class="table table-striped table-bordered table-hover ' . ($this->cfg->get('rows_per_page')>0 ? 'data-table':''). '" data-count-fixed-columns="0" data-page-length="' . $this->cfg->get('rows_per_page') . '">
          <thead>
            <tr>
              ';

    //render listing heading
    $listing_fields = array();   
    $listing_numeric_fields = array();
    $fields_query = db_query("select f.*,if(length(f.short_name)>0,f.short_name,f.name) as name  from app_fields f where (f.id in (" . $fields_in_listing . ") or f.is_heading=1) and  f.entities_id='" . db_input($this->cfg->get('entity_id')) . "' order by f.is_heading desc,f.listing_sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {      
      //check field access
      if(isset($fields_access_schema[$v['id']]))
      {
        if($fields_access_schema[$v['id']]=='hide') continue;
      }                
           
      $html .= '
          <th><div>' . fields_types::get_option($v['type'],'name',$v['name']). '</div></th>
      ';
      
      $listing_fields[] = $v;
    }  
    
    if(users::has_access('update',$this->current_entities_access_schema))
    {
      $html .= '
          <td data-orderable="false"></td>';
    }
                    
    $html .= '          
        </tr>
      </thead>
      <tbody>        
    ';  
    
  
$fields_totals_array = array();
$has_numeric_fields = false;  
    
$listing_sql_query_select = '';
$listing_sql_query = '';
$listing_sql_query_join = '';    
    
//prepare forumulas query
$listing_sql_query_select = fieldtype_formula::prepare_query_select($this->cfg->get('entity_id'), $listing_sql_query_select);

//prepare count of related items in listing
$listing_sql_query_select = fieldtype_related_records::prepare_query_select($this->cfg->get('entity_id'), $listing_sql_query_select);

//get related times array
$related_items = $this->get_related_items();  

$listing_sql_query .= " and e.id in (" . implode(',',$related_items) . ")";


//check view assigned only access
$listing_sql_query = items::add_access_query($this->cfg->get('entity_id'),$listing_sql_query);

//include access to parent records
$listing_sql_query .= items::add_access_query_for_parent_entities($this->cfg->get('entity_id'));

//add default order
$listing_sql_query .= items::add_listing_order_query_by_entity_id($this->cfg->get('entity_id'));  

      
//render listing body
$listing_sql = "select distinct e.* " . $listing_sql_query_select . " from app_entity_" . $this->cfg->get('entity_id') . " e "  . $listing_sql_query_join . " where e.id>0 " . $listing_sql_query;
$items_query = db_query($listing_sql);
while($item = db_fetch_array($items_query))
{
  $related_id = array_search($item['id'],$related_items);
   
  $html .= '
      <tr  id="related-records-' . $related_id . '">                             
  ';
         
  foreach($listing_fields as $field)
  {  
    //check field access
    if(isset($fields_access_schema[$field['id']]))
    {
      if($fields_access_schema[$field['id']]=='hide') continue;
    }
    
    switch($field['type'])
    {
      case 'fieldtype_created_by':
          $value = $item['created_by'];
        break;
      case 'fieldtype_date_added':
          $value = $item['date_added'];                
        break;
      case 'fieldtype_action':                
      case 'fieldtype_id':
          $value = $item['id'];
        break;
      default:
          $value = $item['field_' . $field['id']]; 
        break;
    }
    
    $path_info = items::get_path_info($this->cfg->get('entity_id'),$item['id']);
    
    $output_options = array('class'=>$field['type'],
                            'value'=>$value,
                            'field'=>$field,
                            'item'=>$item,
                            'is_listing'=>true,                                                        
                            'redirect_to' => '',                            
                            'path'=> $path_info['full_path']);
                                                                
    if($field['is_heading']==1)
    { 
    
      //get fields in popup
      $fields_in_popup = fields::get_items_fields_data_by_id($item,$this->cfg->get('fields_in_popup'),$this->cfg->get('entity_id'),$fields_access_schema);
      $popup_html = '';
      if(count($fields_in_popup))
      {
        $popup_html = app_render_fields_popup_html($fields_in_popup); 
      }
        
      
      //include paretn name if parent entities are different
      $parent_name = '';
      if(isset($parent_entity_item_id))
      {
        $path_array = items::parse_path($current_path);
                                                                           
        if(($entity_info['parent_id']!=$path_array['parent_entity_id'] and $item['parent_item_id']>0) or ($item['parent_item_id']>0 and $path_array['parent_entity_item_id']!=$item['parent_item_id']))
        {                                        
          $parent_name = items::get_heading_field($entity_info['parent_id'],$item['parent_item_id']) . '&nbsp;<i class="fa fa-angle-right"></i>&nbsp;';                    
        }
      }
      
      $path = $path_info['full_path'];
      
      
      if($this->cfg->get('entity_id')==1)
      {
        $name = users::output_heading_from_item($item);
      }
      else
      {
        $name = fields_types::output($output_options);
      }
            
      $html .= '
          <td class="' . $field['type'] . ' related_item_heading_td" ><a ' . $popup_html . ' href="' . url_for('items/info', 'path=' . $path . '&redirect_to=subentity') . '">'  . $parent_name .  $name . '</a>
      ';
      
      if($entity_cfg['use_comments']==1 and $user_has_comments_access)
      {
        $html .= comments::get_last_comment_info($this->cfg->get('entity_id'),$item['id'],$path);
      }
      
      $html .= '</td>';
    }
    else
    {
      $td_class = (in_array($field['type'],array('fieldtype_action','fieldtype_date_added','fieldtype_input_datetime')) ? 'class="' . $field['type'] . ' nowrap"':'class="' . $field['type'] . '"');      
      $html .= '
          <td ' . $td_class . '>' . fields_types::output($output_options) . '</td>
      ';
    }
    
    
    $field_cfg = new fields_types_cfg($field['configuration']);
    
    //calculate totals for numeric fields
    if(in_array($field['type'],array('fieldtype_input_numeric','fieldtype_formula','fieldtype_input_numeric_comments')) and $field_cfg->get('donot_calclulate_totals')!=1)
    {      
      $has_numeric_fields = true;
      
      if(!isset($fields_totals_array[$field['id']]))
      {
        $fields_totals_array[$field['id']] = $value;
      }
      else
      {
        $fields_totals_array[$field['id']] += $value;
      }      
    }    
     
  }
      
  
  if(users::has_access('update',$this->current_entities_access_schema))
  {
    $html .= '
          <td>
            <a onClick="return confirm(i18n[\'TEXT_ARE_YOU_SURE\'])" href="' . url_for('items/related_item','action=remove_related_item&path=' . $current_path . '&id=' . $related_id . '&related_entity_id=' . $this->cfg->get('entity_id')). '" title="' . TEXT_BUTTON_DELETE_RELATION . '" class="btn btn-default btn-xs btn-xs-fa-centered"><i class="fa fa-chain-broken"></i></a>
          </td>';
  }
      
  $html .= '  
      </tr>';
        
}    
        
    $html .= '
          </tbody>';
          
if($has_numeric_fields)
{
  $html .= '
    <tfoot>
      <tr>
    ';
  foreach($listing_fields as $field)
  {
    if(isset($fields_totals_array[$field['id']]))
    {
    	$value = $fields_totals_array[$field['id']];
    	
    	$cfg = new fields_types_cfg($field['configuration']);
    	
    	if(strlen($cfg->get('number_format'))>0 and strlen($value)>0)
    	{
    		$format = explode('/',str_replace('*','',$cfg->get('number_format')));
    	
    		$value = number_format($value,$format[0],$format[1],$format[2]);
    	}
    	elseif(strstr($value,'.'))
    	{
    		$value = number_format($value,2,'.','');
    	}
    	
      $html .= '<td class="numeric_fields_total_values">' . $value . '</td>';
    }
    else
    {
      $html .= '<td></td>';
    }
  }
  
  if(users::has_access('update',$this->current_entities_access_schema))
  {
    $html .='
       <td></td>';
  }
  
  $html .='        
      </tr>
    </tfoot>
  ';
}          
    
    $html .= '  
        </table>
      </div>
    </div>
  </div>          
    ';    
    
    return $html;  
    
  }
  
  public static function get_related_items_table_name($entities_id, $related_entities_id)
  {
  	if($entities_id>$related_entities_id)
  	{
  		$table_name = 'app_related_items_' . $related_entities_id . '_' . $entities_id;
  		$key_name = $related_entities_id . '_' . $entities_id;
  	}
  	else
  	{
  		$table_name =  'app_related_items_' . $entities_id . '_' . $related_entities_id;
  		$key_name = $entities_id . '_' . $related_entities_id;
  	}
  	
  	$sufix = '';
  	
  	if($entities_id==$related_entities_id)
  	{
  		$sufix = '_related';
  	}
  	
  	return array('table_name' => $table_name, 'table_key' => $key_name,'sufix'=>$sufix);
  }
  
  function get_related_items()
  {        
    $related_items_array = array();
    
    $table_info = self::get_related_items_table_name($this->entities_id,$this->cfg->get('entity_id'));
     
    $where_sql = '';
        
    $related_items_query = db_query("select * from " . $table_info['table_name'] . " where entity_" . $this->entities_id . "_items_id='" . db_input($this->items_id) . "'");
    
    while($related_items = db_fetch_array($related_items_query))
    {
      $related_items_array[$related_items['id']] = $related_items['entity_' . $this->cfg->get('entity_id') . $table_info['sufix'] . '_items_id'];
    }
    
    if(strlen($table_info['sufix'])>0)
    {
    	$related_items_query = db_query("select * from " . $table_info['table_name'] . " where entity_" . $this->entities_id . $table_info['sufix'] . "_items_id='" . db_input($this->items_id) . "'");
    	
    	while($related_items = db_fetch_array($related_items_query))
    	{
    		$related_items_array[$related_items['id']] = $related_items['entity_' . $this->cfg->get('entity_id') . '_items_id'];
    	}    	    	
    }
    
    return $related_items_array;
  }
  
  function count_related_items()
  {                
    $related_items = $this->get_related_items();
    
    if(count($related_items)>0)
    {  
      
    	$listing_sql_query = " and e.id in (" . implode(',',$related_items) . ")";
          	    	
      //check view assigned only access
      $listing_sql_query = items::add_access_query($this->cfg->get('entity_id'),$listing_sql_query);
      
      //include access to parent records
      $listing_sql_query .= items::add_access_query_for_parent_entities($this->cfg->get('entity_id'));
        
      $listing_sql = "select count(e.id) as total from app_entity_" . $this->cfg->get('entity_id') . " e where e.id>0 " . $listing_sql_query;
      $check_query = db_query($listing_sql);
      $check = db_fetch_array($check_query);  
        
      return $check['total'];
    }
    else
    {
      return 0;
    }
  }
       
  public static function delete_related_by_item_id($entities_id,$items_id)
  {
  	$fields_query = db_query("select f.* from app_fields f where f.type in ('fieldtype_related_records') and f.entities_id='" . db_input($entities_id) . "'");
  	while($field = db_fetch_array($fields_query))
  	{  	
  		$cfg = new fields_types_cfg($field['configuration']);
  		
  		if($cfg->get('entity_id')>0)
  		{
		  	$table_info = self::get_related_items_table_name($entities_id,$cfg->get('entity_id'));
		  	
		  	db_query("delete from " . $table_info['table_name'] . " where entity_" . $entities_id . "_items_id='" . db_input($items_id) . "'");
				
		  	if(strlen($table_info['sufix'])>0)
		  	{
		  		db_query("delete from " . $table_info['table_name'] . " where entity_" . $entities_id . $table_info['sufix'] . "_items_id='" . db_input($items_id) . "'");  		
		  	}
  		}
  	}
 
  }
  
  public static function get_fields_choices_available_to_relate_to_entity($entities_id)
  {
    global $app_user;
    
    $choices = array();
    $fields_query = db_query("select f.*, e.name as entity_name from app_fields f, app_entities e where f.entities_id=e.id and f.type='fieldtype_related_records' order by e.name");
    while($fields = db_fetch_array($fields_query))
    {
      $cfg = new fields_types_cfg($fields['configuration']);
      
      if($cfg->get('entity_id')==$entities_id)
      {  
        $access_schema = users::get_entities_access_schema($fields['entities_id'],$app_user['group_id']);
        
        if(users::has_access('update',$access_schema))
        {
          $choices[$fields['entities_id'] . '-' . $fields['id']] = $fields['entity_name'] . ': ' . $fields['name'];
        }
      }
    }
    
    return $choices;
  }
  
  public static function prepare_entities_related_items_table($entities_id, $fields_id)
  {
  	$field = db_find('app_fields',$fields_id);
  	
  	if($field['type']=='fieldtype_related_records')
  	{
  		$cfg = new fields_types_cfg($field['configuration']);
  		$related_entities_id = $cfg->get('entity_id');
  		
  		if($related_entities_id>0)
  		{  			
  			$tables_array = array();
  			$tables_query = db_query("show tables");
  			while($tables = db_fetch_array($tables_query))
  			{
  				$tables_array[] =  current($tables);
  			}
  			
  			$table_info = self::get_related_items_table_name($entities_id,$related_entities_id);
  			
  			if(!in_array($table_info['table_name'],$tables_array))
  			{
  				$sql = '
		          CREATE TABLE IF NOT EXISTS `' . $table_info['table_name'] . '` (
		            `id` int(11) NOT NULL AUTO_INCREMENT,
		            `entity_' .$entities_id .  '_items_id` int(11) NOT NULL,
		            `entity_' . $related_entities_id . $table_info['sufix'] . '_items_id` int(11) NOT NULL,
		            PRIMARY KEY (`id`),
		            KEY `idx_' . $entities_id . '_items_id` (`entity_' . $entities_id . '_items_id`),
		            KEY `idx_' . $related_entities_id  . $table_info['sufix'] . '_items_id` (`entity_' . $related_entities_id  . $table_info['sufix'] . '_items_id`)
		          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		      ';
  			
  				db_query($sql);
  				 
  			}
  			
  		}
  	}
  }
  
  public static function delete_entities_related_items_table($entities_id)
  {
  	$tables_array = array();
  	$tables_query = db_query("show tables");
  	while($tables = db_fetch_array($tables_query))
  	{
  		$tables_array[] =  current($tables);
  	} 
  	
  	foreach($tables_array as $table)
  	{
  		if(preg_match('/app_related_items_(\d+)_' .$entities_id . '/',$table) or preg_match('/app_related_items_' .$entities_id . '_(\d+)/',$table))
  		{  			
  			$sql = 'DROP TABLE IF EXISTS ' . $table;
  			db_query($sql);
  		}
  	}  	  
  }
    
}