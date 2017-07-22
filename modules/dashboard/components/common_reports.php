<?php

$where_sql = '';
if(strlen($app_users_cfg->get('hidden_common_reports'))>0)
{
  $where_sql = " and r.id not in (" . $app_users_cfg->get('hidden_common_reports') . ")";
}

$reports_query = db_query("select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and find_in_set(" . $app_user['group_id'] . ",r.users_groups) and r.in_dashboard=1 and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name");
while($reports = db_fetch_array($reports_query))
{
  //get report entity info
  $entity_info = db_find('app_entities',$reports['entities_id']);
  $entity_cfg = entities::get_cfg($reports['entities_id']);
  
  //check if parent reports was not set
  if($entity_info['parent_id']>0 and $reports['parent_id']==0)
  {
    reports::auto_create_parent_reports($reports['id']);
  }
  
  //get report entity access schema
  $access_schema = users::get_entities_access_schema($reports['entities_id'],$app_user['group_id']);
  
  $add_button = ''; 
  if(users::has_access('create',$access_schema))
  { 
    if($entity_info['parent_id']==0)
    {
      $url = url_for('items/form','path=' . $reports['entities_id'] . '&redirect_to=report_' . $reports['id']); 
    }
    else
    {
      $url = url_for('reports/prepare_add_item','reports_id=' . $reports['id']);
    }
    $add_button = button_tag((strlen($entity_cfg['insert_button'])>0 ? $entity_cfg['insert_button'] : TEXT_ADD), $url) . ' ';
  } 
      


  $listing_container = 'entity_items_listing' . $reports['id'] . '_' . $reports['entities_id'];
  
  $gotopage = (isset($_GET['gotopage'][$reports['id']]) ? (int)$_GET['gotopage'][$reports['id']]:1);
  
  echo '
                            
    <div class="row dashboard-reports-container" id="dashboard-reports-container">
      <div class="col-md-12">
      
      <div class="row">
        <div class="col-md-12"><h3 class="page-title"><a href="' . url_for('reports/view','reports_id=' . $reports['id']) . '">' . $reports['name'] . '</a></h3></div>
      </div>
      
      <div class="row">
        <div class="col-sm-6">   
             ' . $add_button . '      
            <div class="btn-group">
      				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
      				' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
      				</button>
      				<ul class="dropdown-menu" role="menu">
      					<li>
      						' . link_to_modalbox(TEXT_EXPORT,url_for('items/export','path=' . $reports["entities_id"]  . '&reports_id=' . $reports['id'] ))  .'
      					</li>
                ' . plugins::include_dashboard_with_selected_menu_items($reports['id']) . '
      				</ul>
      			</div>
            
            <button class="btn btn-default popovers" data-trigger="hover" data-placement="right" data-content="' . TEXT_EXT_COMMON_REPORTS_HELP . '"><i class="fa fa-question"></i></button> 
                         
        </div>        
        <div class="col-sm-6">                        
         ' . render_listing_search_form($reports["entities_id"],$listing_container,$reports['id']) . '                         
        </div>
      </div> 
            
      <div id="' . $listing_container . '" class="entity_items_listing"></div>
      ' . input_hidden_tag($listing_container . '_order_fields',$reports['listing_order_fields']) . '
        
      </div>
    </div>
    
    
    <script>
      $(function() {     
        load_items_listing("' . $listing_container . '",' . $gotopage . ');                                                                         
      });    
    </script> 
  ';
}