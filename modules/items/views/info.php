<?php require(component_path('items/navigation')) ?>


<div class="row">
    <div class="col-md-8 project-info">
      
    <div class="portlet portlet-item-description">
			<div class="portlet-title">
				<div class="caption">        
          <?php echo $app_breadcrumb[count($app_breadcrumb)-1]['title'] ?>             
        </div>
        <div class="tools">
					<a href="javascript:;" class="collapse"></a>
				</div>
			</div>
			<div class="portlet-body">
      
        <div class="prolet-body-actions">        
          <ul class="list-inline">
          
<!-- Inlucde timer from Extension -->          
<?php
  if(class_exists('timer'))
  {
    $timer = new timer($current_entity_id, $current_item_id);
    echo $timer->render_button();
  }
?>          
          
            <?php if(users::has_comments_access('create') and $entity_cfg->get('use_comments')==1): ?>
              <li><?php echo button_tag(TEXT_BUTTON_ADD_COMMENT,url_for('items/comments_form','path=' . $_GET['path']),true,array('class'=>'btn btn-default btn-sm'),'fa-comment-o') ?></li>
            <?php endif ?>
            
            <?php if(users::has_access('update')): ?>
              <li><?php echo button_tag(TEXT_BUTTON_EDIT,url_for('items/form','id=' . $current_item_id. '&entity_id=' . $current_entity_id . '&path=' . $_GET['path'] . '&redirect_to=items_info'),true,array('class'=>'btn btn-default btn-sm'),'fa-edit') ?></li>
            <?php endif ?>
                                    
            <li>
              <div class="btn-group">
									<button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
									<?php echo TEXT_MORE_ACTIONS ?> <i class="fa fa-angle-down"></i>
									</button>
									<ul class="dropdown-menu" role="menu">
                      
                    <?php 
                      echo plugins::render_simple_menu_items('more_actions'); 
                    ?>                      
                      
                      <li><?php echo link_to_modalbox('<i class="fa fa-file-pdf-o"></i> ' . TEXT_BUTTON_EXPORT,url_for('items/single_export','path=' . $_GET['path'])) ?></li>
                    
                    <?php if(users::has_access('update') and $current_entity_id==1): ?>
                       <li><?php echo link_to('<i class="fa fa-unlock-alt"></i> ' .TEXT_CHANGE_PASSWORD, url_for('items/change_user_password','path=' . $_GET['path']))?></li>
                    <?php endif ?>  
                      
                    <?php if(users::has_access('delete')): ?>
                      <li><a href="#" onClick="open_dialog('<?php echo url_for('items/delete','id=' .$current_item_id . '&entity_id=' . $current_entity_id . '&path=' . $_GET['path']) ?>'); return false;"><i class="fa fa-trash-o"></i> <?php echo TEXT_BUTTON_DELETE?></a></li>
                    <?php endif ?>
																					
									</ul>
								</div>
            </li>
                        
          </ul>
        </div>
        
<!-- Inlucde timer from Extension -->          
<?php
  if(class_exists('timer'))
  {    
    echo $timer->render();
  }
?>        
          
        <div class="item-content-box ckeditor-images-content-prepare">
          <?php echo items::render_content_box($current_entity_id,$current_item_id) ?>
        </div>
        
      </div>
    </div>
    
    <?php
//include reladed records that displays as single list    
      $reladed_records = new related_records($current_entity_id,$current_item_id);
      echo $reladed_records->render_as_single_list();
    ?>
    
    
    <?php
//include items comments if user have access and comments enabled     
    if(users::has_comments_access('view') and $entity_cfg->get('use_comments')==1) 
    {
      require(component_path('items/comments'));
    } 
    ?>
    
    </div>
    
    <div class="col-md-4" style="position:static">

    <?php 
//include related records in box    
    echo $reladed_records->render_as_single_list(false); 
    ?>

    <div class="panel panel-info item-details">
  		<div class="panel-body item-details">            
      <?php echo items::render_info_box($current_entity_id,$current_item_id) ?>
      </div>
    </div>
    </div>
</div>   

<script>
  $(function(){
    ckeditor_images_content_prepare();
  })
</script>

