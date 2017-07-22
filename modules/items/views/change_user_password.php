
<ul class="page-breadcrumb breadcrumb">
  <?php echo items::render_breadcrumb($app_breadcrumb) ?>
</ul>

<h3 class="page-title"><?php echo  TEXT_HEADING_CHANGE_PASSWORD ?></h3>


<?php echo form_tag('change_password_form', url_for('items/change_user_password','action=change&path=' . $_GET['path']),array('class'=>'form-horizontal')) ?>

  <div class="form-body">
  
    <div class="form-group">
    	<label class="col-md-3 control-label" for="password_new"><?php echo TEXT_NEW_PASSWORD ?></label>
      <div class="col-md-9">	
    	  <?php  echo input_password_tag('password_new',array('autocomplete'=>'off','class'=>'form-control input-medium required')) ?>
      </div>			
    </div>
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="password_confirmation"><?php echo TEXT_PASSWORD_CONFIRMATION ?></label>
      <div class="col-md-9">	
    	  <?php  echo input_password_tag('password_confirmation',array('autocomplete'=>'off','class'=>'form-control input-medium  required')) ?>
      </div>			
    </div> 
    
<?php echo submit_tag(TEXT_BUTTON_CHANGE,array('class'=>'btn btn-primary'))  ?>     
 
  </div>
</form>

<script>
  $(function() { 
    $('#change_password_form').validate();                                                                            
  });    
</script> 