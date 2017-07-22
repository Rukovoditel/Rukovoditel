<?php require(component_path('entities/navigation')) ?>

<?php $default_selector = array('1'=>TEXT_YES,'0'=>TEXT_NO); ?>




<?php echo form_tag('cfg', url_for('entities/entities_configuration','action=save&entities_id=' . $_GET['entities_id']),array('class'=>'form-horizontal')) ?>



<div class="tabbable tabbable-custom">

<ul class="nav nav-tabs">
  <li class="active"><a href="#general_info"  data-toggle="tab"><?php echo TEXT_TITLES ?></a></li>
  <li><a href="#comments_configuration"  data-toggle="tab"><?php echo TEXT_COMMENTS_TITLE ?></a></li>   
  <li><a href="#redirects_configuration"  data-toggle="tab"><?php echo TEXT_REDIRRECTS ?></a></li>
</ul>

<div class="tab-content">
  <div class="tab-pane fade active in" id="general_info">

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[menu_title]', $cfg->get('menu_title'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_TITLE_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_ICON_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[menu_icon]', $cfg->get('menu_icon'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_listing_heading"><?php echo TEXT_LISTING_HEADING; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[listing_heading]', $cfg->get('listing_heading'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_LISTING_HEADING_TOOLTIP) ?>
    </div>			
  </div>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_window_heading"><?php echo TEXT_WINDOW_HEADING; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[window_heading]', $cfg->get('window_heading'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_WINDOW_HEADING_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_INSERT_BUTTON_TITLE; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[insert_button]', $cfg->get('insert_button'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_INSERT_BUTTON_TITLE_TOOLTIP) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_reports_hide_insert_button"><?php echo TEXT_HIDE_INSERT_BUTTON_IN_REPORTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[reports_hide_insert_button]',$default_selector, $cfg->get('reports_hide_insert_button'),array('class'=>'form-control input-small')); ?>       
    </div>			
  </div>
  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_EMAIL_SUBJECT_NEW_ITEM; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[email_subject_new_item]', $cfg->get('email_subject_new_item'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_EMAIL_SUBJECT_NEW_ITEM_TOOLTIP) ?>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_EMAIL_SUBJECT_UPDATED_ITEM; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[email_subject_updated_item]', $cfg->get('email_subject_updated_item'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_EMAIL_SUBJECT_UPDATED_ITEM_TOOLTIP) ?>
    </div>			
  </div>
        

	</div>
  <div class="tab-pane fade" id="comments_configuration">

  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_USE_COMMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[use_comments]',$default_selector, $cfg->get('use_comments'),array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_USE_COMMENTS_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_DISPLAY_COMMENTS_ID; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[display_comments_id]',$default_selector, $cfg->get('display_comments_id'),array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_DISPLAY_COMMENTS_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_USE_EDITOR_IN_COMMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[use_editor_in_comments]',$default_selector, $cfg->get('use_editor_in_comments'),array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_USE_EDITOR_IN_COMMENTS_TOOLTIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_DISABLE_ATTACHMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[disable_attachments_in_comments]',$default_selector, $cfg->get('disable_attachments_in_comments'),array('class'=>'form-control input-small')); ?>       
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_insert_button"><?php echo TEXT_EMAIL_SUBJECT_NEW_COMMENT; ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('cfg[email_subject_new_comment]', $cfg->get('email_subject_new_comment'),array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_EMAIL_SUBJECT_NEW_COMMENT_TOOLTIP) ?>
    </div>			
  </div>

	</div>
	
	<div class="tab-pane fade" id="redirects_configuration">
	
<?php 
	$after_adding_selector = array(
			'subentity' => TEXT_REDIRECT_TO_SUBENTITY,
			'listing' => TEXT_REDIRECT_TO_LISTING,
			'info' => TEXT_REDIRECT_TO_INFO,
	);
	
	$click_heading_selector = array(
			'subentity' => TEXT_REDIRECT_TO_SUBENTITY,			
			'info' => TEXT_REDIRECT_TO_INFO,
	);
?>	
	
	<div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_REDIRECT_AFTER_ADDING; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[redirect_after_adding]',$after_adding_selector, $cfg->get('redirect_after_adding'),array('class'=>'form-control input-large')); ?>       
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_use_comments"><?php echo TEXT_REDIRECT_AFTER_CLICK_HEADING; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[redirect_after_click_heading]',$click_heading_selector, $cfg->get('redirect_after_click_heading'),array('class'=>'form-control input-large')); ?>       
    </div>			
  </div>
  
	</div>
	
</div>

</div>	  



<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>


<script>
  $(function() {   
    $('.tooltips').tooltip();                                                                             
  });
</script>    



