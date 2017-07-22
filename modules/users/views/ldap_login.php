<h3 class="form-title"><?php echo TEXT_HEADING_LDAP_LOGIN ?></h3>


<?php if(access_groups::get_ldap_default_group_id()){ ?>

<?php echo form_tag('login_form', url_for('users/ldap_login','action=login'),array('class'=>'login-form')) ?>

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
		<i class="fa fa-lock"></i>
		<input class="form-control placeholder-no-fix required"  type="password" autocomplete="off" placeholder="<?php echo TEXT_PASSWORD ?>" name="password"/>
	</div>
</div>

<?php if(app_recaptcha::is_enabled()): ?>
<div class="form-group">
	<?php echo app_recaptcha::render() ?>	
</div>
<?php endif ?>

<div class="form-actions">
	<button type="button" id="back-btn" class="btn btn-default" onClick="location.href='<?php echo url_for('users/login')?>'"><i class="fa fa-arrow-circle-left"></i> </button>
	<button type="submit" class="btn btn-info pull-right"><?php echo TEXT_BUTTON_LOGIN ?></button>
</div>

</form>

<script>
  $(function() { 
    $('#login_form').validate();                                                                            
  });    
</script> 

<?php
  }
  else
  {
    echo '<div>' . TEXT_ERROR_DEFAULT_LDAP_GROUP . '</div>'; 
  }
?>



