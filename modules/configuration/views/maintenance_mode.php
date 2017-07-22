<h3 class="page-title"><?php echo TEXT_HEADING_MAINTENANCE_MODE ?></h3>

<?php echo form_tag('cfg', url_for('configuration/save','redirect_to=configuration/maintenance_mode'),array('enctype'=>'multipart/form-data','class'=>'form-horizontal')) ?>
<div class="form-body">

	<div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_MAINTENANCE_MODE"><?php echo TEXT_MAINTENANCE_MODE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[MAINTENANCE_MODE]',$default_selector,CFG_MAINTENANCE_MODE,array('class'=>'form-control input-small')); ?>
  	  <?php echo tooltip_text(TEXT_MAINTENANCE_MODE_NOTE) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_MAINTENANCE_MESSAGE_HEADING"><?php echo TEXT_MESSAGE_HEADING ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[MAINTENANCE_MESSAGE_HEADING]', CFG_MAINTENANCE_MESSAGE_HEADING,array('class'=>'form-control input-large')); ?>
  	  <?php echo tooltip_text(TEXT_DEFAULT . ': "' . TEXT_MAINTENANCE_MESSAGE_HEADING. '"') ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_MAINTENANCE_MESSAGE_CONTENT"><?php echo TEXT_MESSAGE_CONTENT ?></label>
    <div class="col-md-9">	
  	  <?php echo textarea_tag('CFG[MAINTENANCE_MESSAGE_CONTENT]', CFG_MAINTENANCE_MESSAGE_CONTENT,array('class'=>'form-control input-xlarge','rows'=>3)); ?>
  	  <?php echo tooltip_text(TEXT_DEFAULT . ': "' . TEXT_MAINTENANCE_MESSAGE_CONTENT. '"') ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="APP_LOGIN_MAINTENANCE_BACKGROUND"><?php echo TEXT_LOGIN_PAGE_BACKGROUND ?></label>
    <div class="col-md-9">	
  	  <?php echo input_file_tag('APP_LOGIN_MAINTENANCE_BACKGROUND') . input_hidden_tag('CFG[APP_LOGIN_MAINTENANCE_BACKGROUND]',CFG_APP_LOGIN_MAINTENANCE_BACKGROUND);             
      if(is_file(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGIN_MAINTENANCE_BACKGROUND))
      {
        echo  '<span class="help-block">' . CFG_APP_LOGIN_MAINTENANCE_BACKGROUND . '<label class="checkbox">' . input_checkbox_tag('delete_login_maintenance_background') . ' ' . TEXT_DELETE . '</label></span>';
      }   
      
      echo tooltip_text(TEXT_LOGIN_PAGE_BACKGROUND_INFO);                                                                                              
    ?>
    </div>			
  </div>
      
<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>