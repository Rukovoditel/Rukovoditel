<?php

app_reset_selected_items();

if(isset($field_entity_items_position))
{	
	$fields_query = db_query("select id, name, configuration, entities_id from app_fields where entities_id!='" . db_input($current_entity_id)  . "' and type='fieldtype_entity'");
	while($fields = db_fetch_array($fields_query))
	{
		$field_cfg = new fields_types_cfg($fields['configuration']);
	
		if($field_cfg->get('entity_id')==$current_entity_id)
		{						
			if($app_user['group_id']==0)
			{
				$entities_query = db_query("select e.* from app_entities e where id='" . db_input($fields['entities_id']) . "' order by e.sort_order, e.name");
			}
			else
			{
				$entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.id = '" . db_input($fields['entities_id']) . "' order by e.sort_order, e.name");
			}
						
			if($entities = db_fetch_array($entities_query))
			{								
				if($entity_cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position')==$field_entity_items_position)
				{
										
					//try to get report type parent_item_info_page
					$subentity_report_query = db_query("select * from app_reports where entities_id='" . db_input($entities['id']). "' and reports_type='field" . $fields['id'] . "_entity_item_info_page'");
					if(!$subentity_report = db_fetch_array($subentity_report_query))
					{
						$sql_data = array('name'=>'',
								'entities_id'=>$entities['id'],
								'reports_type'=>'field' . $fields['id'] . '_entity_item_info_page',
								'in_menu'=>0,
								'in_dashboard'=>0,
								'created_by'=>0,
						);
		
						db_perform('app_reports',$sql_data);
		
						$reports_id = db_insert_id();
		
						$subentity_report = db_find('app_reports',$reports_id);
					}
						
					$subentity_cfg = new entities_cfg($entities['id']);
						
					$listing_container = 'entity_items_listing' . $subentity_report['id'] . '_' .  $subentity_report['entities_id'];
						
						
					//get report entity access schema
					$access_schema = users::get_entities_access_schema($subentity_report['entities_id'],$app_user['group_id']);
						
					$add_button = '';					
					if(users::has_access('create',$access_schema))
					{
						$field_entity_info = db_find('app_entities',$entities['id']);
						
						switch(true)
						{
							case $field_entity_info['parent_id']==0:
								$url = url_for('items/form','path=' . $entities['id'] . '&fields[' . $fields['id'] . ']=' . $current_item_id. '&redirect_to=item_info_page' . $current_entity_id . '-'. $current_item_id);
								break;
							case ($field_entity_info['parent_id']>0 and $field_entity_info['parent_id']!=$entity_info['parent_id']):
								$url = url_for('reports/prepare_add_item','reports_id=' . $subentity_report['id'] . '&fields[' . $fields['id'] . ']=' . $current_item_id. '&redirect_to=item_info_page' . $current_entity_id . '-'. $current_item_id);								
								break;
							case ($field_entity_info['parent_id']>0 and $field_entity_info['parent_id']==$entity_info['parent_id']):
								$path_info = items::parse_path($app_path);
								$url = url_for('items/form','path=' . $path_info['parent_entity_id'] . '-' . $path_info['parent_entity_item_id'] . '/'. $entities['id'] . '&fields[' . $fields['id'] . ']=' . $current_item_id. '&redirect_to=item_info_page' . $current_entity_id . '-'. $current_item_id);
								break;
						}
											
						$add_button = button_tag((strlen($subentity_cfg->get('insert_button'))>0 ? $subentity_cfg->get('insert_button') : TEXT_ADD), $url,true,array('class'=>'btn btn-primary btn-sm')) . ' ';
					}
										
					$with_selected_menu = '';
						
					if(users::has_access('export_selected',$access_schema) and users::has_access('export',$access_schema))
					{
						$with_selected_menu .= '<li>' . link_to_modalbox('<i class="fa fa-file-excel-o"></i> ' . TEXT_EXPORT,url_for('items/export','path=' . $subentity_report["entities_id"]  . '&reports_id=' . $subentity_report['id'] )) . '</li>';
					}
		
					$with_selected_menu .=  plugins::include_dashboard_with_selected_menu_items($subentity_report['id'],'&path=' . $app_path . '/' .  $subentity_report['entities_id'] . '&redirect_to=parent_item_info_page');
		
					$html = '
					
					<div class="row info-page-reports-container">
			      <div class="col-md-12">
		
			      <div class="portlet">
							<div class="portlet-title">
								<div class="caption">
				          <a href="' . url_for('items/items','path=' . $app_path . '/' .  $subentity_report['entities_id']) . '">' .(strlen($entity_cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading')) ? $entity_cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading') : (strlen($subentity_cfg->get('listing_heading'))>0 ? $subentity_cfg->get('listing_heading') : $entities['name'])) . '</a>
				        </div>
				        <div class="tools">
									<a href="javascript:;" class="collapse"></a>
								</div>
							</div>
							<div class="portlet-body">
			   
			      <div class="row">
			        <div class="col-sm-6">
			             ' . $add_button . '
			             ' . (strlen($with_selected_menu) ? '
			            <div class="btn-group">
			      				<button class="btn btn-default dropdown-toggle btn-sm" type="button" data-toggle="dropdown" data-hover="dropdown">
			      				' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
			      				</button>
			      				<ul class="dropdown-menu" role="menu">
			      					' . $with_selected_menu . '
			      				</ul>
			      			</div>': '') .
		
			      			'</div>
			        <div class="col-sm-6">
			         ' . render_listing_search_form($subentity_report["entities_id"],$listing_container,$subentity_report['id'],'input-small') . '
			        </div>
			      </div>
			      
			      <div id="' . $listing_container . '" class="entity_items_listing"></div>
			      ' . input_hidden_tag($listing_container . '_order_fields',$subentity_report['listing_order_fields']) .
			      input_hidden_tag($listing_container . '_has_with_selected',(strlen($with_selected_menu) ? 1:0)) .
			      input_hidden_tag($listing_container . '_force_filter_by', $fields['id'] . ':' . $current_item_id) . 
			      input_hidden_tag($listing_container . '_redirect_to', 'item_info_page' . $current_entity_id . '-'. $current_item_id) . '
			    
			      
				        </div>
				    	</div>
			      
			      </div>
			    </div>
							
						<script>
				      $(function() {
				        load_items_listing("' . $listing_container . '",1);
				      });
				    </script>
					';
			      	
			      echo $html;
			      	
				}
			}
		}
	}
}
