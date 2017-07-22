<?php
  app_reset_selected_items();
   
  $listing_container = 'entity_items_listing' . $reports_info['id'] . '_' . $reports_info['entities_id'];
      
  //check if parent reports was not set
  if($entity_info['parent_id']>0 and $reports_info['parent_id']==0)
  {
    reports::auto_create_parent_reports($reports_info['id']);
  }
  
  //get report entity access schema
  $access_schema = users::get_entities_access_schema($reports_info['entities_id'],$app_user['group_id']);
                
?>

<h3 class="page-title"><?php echo $page_title ?></h3>

<?php 
if($reports_info['reports_type']!='common')
{
  $filters_preivew = new filters_preivew($reports_info['id']);
  echo $filters_preivew->render();
}    
?>

<div class="row">
  <div class="col-sm-6">
    <div class="entitly-listing-buttons-left">
    
      <?php 
        if(users::has_access('create',$access_schema) and $entity_cfg->get('reports_hide_insert_button')!=1)
        { 
          if($entity_info['parent_id']==0)
          {
            $url = url_for('items/form','path=' . $reports_info['entities_id'] . '&redirect_to=report_' . $reports_info['id']); 
          }
          else
          {
            $url = url_for('reports/prepare_add_item','reports_id=' . $reports_info['id']);
          }
          echo button_tag((strlen($entity_cfg->get('insert_button'))>0 ? $entity_cfg->get('insert_button') : TEXT_ADD), $url) . ' ';
        } 
      ?>
      
      <div class="btn-group">
				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
				<?php echo TEXT_WITH_SELECTED ?> <i class="fa fa-angle-down"></i>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<?php echo link_to_modalbox(TEXT_EXPORT,url_for('items/export','path=' . $reports_info["entities_id"]  . '&reports_id=' . $reports_info['id'] )) ?>
					</li>
<?php 
  echo plugins::render_simple_menu_items('with_selected'); 
?>           
				</ul>
			</div>
            
    </div>
  </div>
  <div class="col-sm-6">
    <div class="entitly-listing-buttons-right">    
      <?php echo render_listing_search_form($reports_info["entities_id"],$listing_container,$reports_info['id']) ?>            
    </div>                    
  </div>
</div> 

<div class="row">
  <div class="col-xs-12">
    <div id="<?php echo $listing_container;  ?>" class="entity_items_listing"></div>
  </div>
</div>

<?php echo input_hidden_tag($listing_container . '_order_fields',$reports_info['listing_order_fields']) ?>

<?php require(component_path('items/load_items_listing.js')); ?>

<?php $gotopage = (isset($_GET['gotopage'][$reports_info['id']]) ? (int)$_GET['gotopage'][$reports_info['id']]:1); ?>

<script>  
  $(function() {     
    load_items_listing('<?php echo $listing_container  ?>',<?php echo $gotopage ?>);                                                                         
  });          
</script> 

