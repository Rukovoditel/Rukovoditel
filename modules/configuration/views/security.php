
<h3 class="page-title"><?php echo TEXT_HEADING_SECURITY_CONFIGURATION ?></h3>
 
<?php echo form_tag('cfg', url_for('configuration/save'),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('redirect_to','configuration/attachments') ?>
<div class="form-body">
  
<h3 class="form-section">Google reCAPTCHA <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank"><i class="fa fa-external-link" aria-hidden="true"></i></a></h3>
  
  <p><?php echo TEXT_RECAPTCHA_INFO ?></p>
  <p><?php echo TEXT_RECAPTCHA_HOW_ENABLE ?></p>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_STATUS ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo app_render_status_label(app_recaptcha::is_enabled()) ?></p>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_RECAPTCHA_SITE_KEY ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo CFG_RECAPTCHA_KEY ?></p>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_RECAPTCHA_SECRET_KEY ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo CFG_RECAPTCHA_SECRET_KEY ?></p>
    </div>			
  </div>

<h3 class="form-section"><?php echo TEXT_RESTRICTED_COUNTRIES ?></h3>   

	<p><?php echo TEXT_RESTRICTED_COUNTRIES_INFO ?></p>
	<p><?php echo TEXT_RESTRICTED_COUNTRIES_HOW_ENABLE ?></p>
	
	<div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_STATUS ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo app_render_status_label(app_restricted_countries::is_enabled()) ?></p>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_ALLOWED_COUNTRIES ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo CFG_ALLOWED_COUNTRIES_LIST ?></p>
    </div>			
  </div>
  
<h3 class="form-section"><?php echo TEXT_RESTRICTED_BY_IP ?></h3>   

	<p><?php echo TEXT_RESTRICTED_BY_IP_INFO ?></p>
	<p><?php echo TEXT_RESTRICTED_BY_IP_HOW_ENABLE ?></p>
	
	<div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_STATUS ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo app_render_status_label(app_restricted_ip::is_enabled()) ?></p>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_RESIZE_IMAGES"><?php echo TEXT_ALLOWED_IP ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo CFG_ALLOWED_IP_LIST ?></p>
    </div>			
  </div>    
     
</div>
</form>



