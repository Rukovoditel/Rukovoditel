
<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php echo form_tag('users_alerts_form', url_for('users_alerts/users_alerts','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
  <div class="form-body">
   
  <div class="form-group">
	 	<label class="col-md-3 control-label" for="is_active"><?php echo TEXT_IS_ACTIVE ?></label>
    <div class="col-md-9">	
  	  <p class="form-control-static"><?php echo input_checkbox_tag('is_active',$obj['is_active'],array('checked'=>($obj['is_active']==1 ? 'checked':''))) ?></p>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="type"><?php echo TEXT_TYPE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('type',users_alerts::get_types_choices(),$obj['type'],array('class'=>'form-control input-medium')) ?>
    </div>			
  </div>
    
  <div class="form-group">
  	<label class="col-md-3 control-label" for="title"><?php echo TEXT_TITLE ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('title',$obj['title'],array('class'=>'form-control input-xlarge required')) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="description"><?php echo TEXT_DESCRIPTION ?></label>
    <div class="col-md-9">	
  	  <?php echo textarea_tag('description',$obj['description'],array('class'=>'editor')) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="location"><?php echo TEXT_LOCATION ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('location',users_alerts::get_location_choices(),$obj['location'],array('class'=>'form-control input-medium')) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="start_date"><?php echo TEXT_DISPLAY_DATE ?></label>
    <div class="col-md-9">	
  	  <div class="input-group input-large datepicker input-daterange daterange-filter">					
				<span class="input-group-addon">
					<i class="fa fa-calendar"></i>
				</span>
				<?php echo input_tag('start_date',($obj['start_date']>0 ? date('Y-m-d',$obj['start_date']) :''),array('class'=>'form-control','placeholder'=>TEXT_DATE_FROM))?>
				<span class="input-group-addon">
					<i style="cursor:pointer" class="fa fa-refresh" aria-hidden="true" title="<?php echo TEXT_EXT_RESET ?>" onClick="app_reset_date_range_input('daterange-filter','start_date','end_date')"></i>
				</span>
				<?php echo input_tag('end_date',($obj['end_date']>0 ? date('Y-m-d',$obj['end_date']) :''),array('class'=>'form-control','placeholder'=>TEXT_DATE_TO))?>			
			</div>	
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="users_groups"><?php echo TEXT_USERS_GROUPS ?></label>
    <div class="col-md-9">	
<?php 
	  	  $attributes = array('class'=>'form-control input-xlarge chosen-select',
	  	  		'multiple'=>'multiple',
	  	  		'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
	  	  
	  	  $users_groups = (strlen($obj['users_groups'])>0 ? explode(',',$obj['users_groups']) : array());
	  	  echo select_tag('users_groups[]',access_groups::get_choices(),$users_groups,$attributes);
?>      
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="assigned_to"><?php echo TEXT_ASSIGNED_TO ?></label>
    <div class="col-md-9">	
<?php
      $attributes = array('class'=>'form-control input-xlarge chosen-select',
                          'multiple'=>'multiple',
                          'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
                          
      $assigned_to = (strlen($obj['assigned_to'])>0 ? explode(',',$obj['assigned_to']) : '');                     
      echo select_tag('assigned_to[]',users::get_choices(),$assigned_to,$attributes);
      echo tooltip_text(TEXT_IF_NOT_ASSIGNED_DISPLY_EVERYONE);
?>  	        
    </div>			
  </div>   
  
  

      
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#users_alerts_form').validate({
			submitHandler: function(form){
				app_prepare_modal_action_loading(form)
				form.submit();
			}
    });                                           
  }); 

</script>   
    
 
