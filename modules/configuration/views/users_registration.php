
<h3 class="page-title"><?php echo TEXT_MENU_USERS_REGISTRATION ?></h3>

<?php echo form_tag('cfg', url_for('configuration/save','redirect_to=configuration/users_registration'),array('class'=>'form-horizontal')) ?>
<div class="form-body">


<div class="tabbable tabbable-custom">

<ul class="nav nav-tabs">
  <li class="active"><a href="#user_registration"  data-toggle="tab"><?php echo TEXT_MENU_USER_REGISTRATION_EMAIL ?></a></li>
  <li><a href="#public_registration"  data-toggle="tab"><?php echo TEXT_PUBLIC_REGISTRATION ?></a></li>    
</ul>


<div class="tab-content">
  <div class="tab-pane fade active in" id="user_registration">

		<p><?php echo TEXT_HEADING_USER_REGISTRATION_EMAIL ?></p>

	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_REGISTRATION_EMAIL_SUBJECT"><?php echo TEXT_REGISTRATION_EMAIL_SUBJECT ?></label>
	    <div class="col-md-9">	
	  	  <?php echo input_tag('CFG[REGISTRATION_EMAIL_SUBJECT]', CFG_REGISTRATION_EMAIL_SUBJECT,array('class'=>'form-control input-xlarge')); ?>
	      <span class="help-block"><?php echo TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT ?></span>
	    </div>			
	  </div>
	  
	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_REGISTRATION_EMAIL_BODY"><?php echo TEXT_REGISTRATION_EMAIL_BODY ?></label>
	    <div class="col-md-9">	
	  	  <?php echo textarea_tag('CFG[REGISTRATION_EMAIL_BODY]', CFG_REGISTRATION_EMAIL_BODY,array('class'=>'form-control input-xlarge editor')); ?>
	      <span class="help-block"><?php echo TEXT_REGISTRATION_EMAIL_BODY_NOTE ?></span>
	    </div>			
	  </div>
	  
	</div>
  <div class="tab-pane fade" id="public_registration">

		<div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_EMAIL_USE_SMTP"><?php echo TEXT_USE_PUBLIC_REGISTRATION ?></label>
	    <div class="col-md-9">
	    	<?php echo select_tag('CFG[USE_PUBLIC_REGISTRATION]',$default_selector,CFG_USE_PUBLIC_REGISTRATION,array('class'=>'form-control input-small')); ?> 
	    </div>			
	  </div>
	  
	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_EMAIL_USE_SMTP"><?php echo tooltip_icon(TEXT_PUBLIC_REGISTRATION_USER_GROUP) . TEXT_USERS_GROUPS ?></label>
	    <div class="col-md-9">
	    	<?php echo select_tag('CFG[PUBLIC_REGISTRATION_USER_GROUP]',access_groups::get_choices(false),CFG_PUBLIC_REGISTRATION_USER_GROUP,array('class'=>'form-control input-small')); ?>	    	
	    </div>			
	  </div>
	  
		<div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_LOGIN_PAGE_HEADING"><?php echo TEXT_LOGIN_PAGE_HEADING ?></label>
	    <div class="col-md-9">	
	  	  <?php echo input_tag('CFG[PUBLIC_REGISTRATION_PAGE_HEADING]', CFG_PUBLIC_REGISTRATION_PAGE_HEADING,array('class'=>'form-control input-large')); ?>
	  	  <?php echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_REGISTRATION_NEW_USER) ?>
	    </div>			
	  </div>
	  
	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_LOGIN_PAGE_CONTENT"><?php echo TEXT_LOGIN_PAGE_CONTENT ?></label>
	    <div class="col-md-9">	
	  	  <?php echo textarea_tag('CFG[PUBLIC_REGISTRATION_PAGE_CONTENT]', CFG_PUBLIC_REGISTRATION_PAGE_CONTENT,array('class'=>'form-control input-xlarge','rows'=>3)); ?>
	    </div>			
	  </div>
	  
	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="CFG_LOGIN_PAGE_HEADING"><?php echo TEXT_REGISTRATION_BUTTON_TITLE ?></label>
	    <div class="col-md-9">	
	  	  <?php echo input_tag('CFG[REGISTRATION_BUTTON_TITLE]', CFG_REGISTRATION_BUTTON_TITLE,array('class'=>'form-control input-medium')); ?>
	  	  <?php echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_BUTTON_REGISTRATCION) ?>
	    </div>			
	  </div>	  
  
  </div>
</div>

</div>  


<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div> 
</form>