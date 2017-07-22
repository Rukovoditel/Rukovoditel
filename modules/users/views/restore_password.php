<h3 class="form-title"><?php echo  TEXT_HEADING_RESTORE_PASSWORD ?></h3>


<?php echo form_tag('restore_password_form', url_for('users/restore_password','action=restore'),array('class'=>'login-form')) ?>

<div class="form-group">
	<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
	<label class="control-label visible-ie8 visible-ie9"><?php echo TEXT_USERNAME ?></label>
	<div class="input-icon">
		<i class="fa fa-user"></i>
		<input class="form-control placeholder-no-fix required" type="text" autocomplete="off" placeholder="<?php echo TEXT_USERNAME ?>" name="username"/>
	</div>
</div>
<div class="form-group">
	<label class="control-label visible-ie8 visible-ie9"><?php echo TEXT_PASSWORD ?></label>
	<div class="input-icon">
		<i class="fa fa-envelope"></i>
		<input class="form-control placeholder-no-fix required"  type="text" autocomplete="off" placeholder="<?php echo TEXT_EMAIL ?>" name="email"/>
	</div>
</div>

<?php if(app_recaptcha::is_enabled()): ?>
<div class="form-group">
	<?php echo app_recaptcha::render() ?>	
</div>
<?php endif ?>

<div class="form-actions">
	<button type="button" id="back-btn" class="btn btn-default" onClick="location.href='<?php echo url_for('users/login')?>'"><i class="fa fa-arrow-circle-left"></i> </button>
	<button type="submit" class="btn btn-info pull-right"><?php echo TEXT_SEND ?></button>
</div>


<script>
  $(function() { 
    $('#restore_password_form').validate();                                                                            
  });    
</script> 