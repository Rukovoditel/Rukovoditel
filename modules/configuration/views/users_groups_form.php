<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title"><?php echo TEXT_HEADING_USER_GROUP_IFNO ?></h4>
</div>


<?php echo form_tag('users_groups_form', url_for('configuration/users_groups','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-medium required')) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="is_default"><?php echo TEXT_IS_DEFAULT ?></label>
    <div class="col-md-9">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_default','1',array('checked'=>$obj['is_default'])) ?></label></div>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="is_ldap_default"><?php echo TEXT_IS_LDAP_DEFAULT ?></label>
    <div class="col-md-9">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_ldap_default','1',array('checked'=>$obj['is_ldap_default'])) ?></label></div>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-small number')) ?>
    </div>			
  </div> 
     
  </div>
</div>
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#users_groups_form').validate();                                                                  
  });
  
</script>   
    
 
