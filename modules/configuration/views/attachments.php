
<h3 class="page-title"><?php echo TEXT_HEADING_ATTACHMENTS_CONFIGURAITON ?></h3>
 
<?php echo form_tag('cfg', url_for('configuration/save'),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('redirect_to','configuration/attachments') ?>
<div class="form-body">

	<div class="form-group">
  	<label class="col-md-3 control-label" ><?php echo TEXT_MAX_UPLOAD_FILE_SIZE ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo CFG_SERVER_UPLOAD_MAX_FILESIZE; ?> MB</p>
  	  <?php echo tooltip_text(TEXT_MAX_UPLOAD_FILE_SIZE_TIP) ?>
    </div>			
  </div>
  
<h3 class="form-section"></h3>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_RESIZE_IMAGES ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('CFG[RESIZE_IMAGES]',$default_selector,CFG_RESIZE_IMAGES,array('class'=>'form-control input-small')); ?>
  	  <?php echo tooltip_text(TEXT_RESIZE_IMAGES_TIP) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_MAX_IMAGE_WIDTH"><?php echo TEXT_MAX_IMAGE_WIDTH ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[MAX_IMAGE_WIDTH]', CFG_MAX_IMAGE_WIDTH,array('class'=>'form-control input-small number','type'=>'number')); ?>
  	  <?php echo tooltip_text(TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_LBANK) ?>  	  
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_MAX_IMAGE_HEIGHT"><?php echo TEXT_MAX_IMAGE_HEIGHT ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[MAX_IMAGE_HEIGHT]', CFG_MAX_IMAGE_HEIGHT,array('class'=>'form-control input-small number','type'=>'number')); ?>
  	  <?php echo tooltip_text(TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_LBANK) ?>  	  
    </div>			
  </div> 
    
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES_TYPES"><?php echo tooltip_icon(TEXT_RESIZE_IMAGES_TYPES_TIP) . TEXT_IMAGES_TYPES ?></label>
    <div class="col-md-9">	
  	  <?php echo select_checkboxes_tag('CFG[RESIZE_IMAGES_TYPES]',array('1'=>'gif','2'=>'jpeg','3'=>'png'), CFG_RESIZE_IMAGES_TYPES); ?>  	  
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_SKIP_IMAGE_RESIZE"><?php echo TEXT_SKIP_IMAGE_RESIZE ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('CFG[SKIP_IMAGE_RESIZE]', CFG_SKIP_IMAGE_RESIZE,array('class'=>'form-control input-small number','type'=>'number')); ?>
  	  <?php echo tooltip_text(TEXT_SKIP_IMAGE_RESIZE_TIP) ?>
    </div>			
  </div> 
 
                                                                                                          
  <?php echo submit_tag(TEXT_BUTTON_SAVE) ?>
  

      
</div>
</form>



