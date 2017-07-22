
<?php echo ajax_modal_template_header(TEXT_HEADING_VALUE_IFNO) ?>

<?php echo form_tag('fields_form', url_for('entities/fields_choices','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
       
<?php echo input_hidden_tag('entities_id',$_GET['entities_id']) . input_hidden_tag('fields_id',$_GET['fields_id']) ?>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="parent_id"><?php echo TEXT_PARENT ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('parent_id',fields_choices::get_choices($_GET['fields_id']), (isset($_GET['parent_id']) ? $_GET['parent_id'] : $obj['parent_id']),array('class'=>'form-control input-medium')) ?>
      <?php echo tooltip_text(TEXT_CHOICES_PARENT_INFO); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>
      <?php echo tooltip_text(TEXT_CHOICES_NAME_INFO); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="is_default"><?php echo TEXT_IS_DEFAULT ?></label>
    <div class="col-md-9">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_default','1',array('checked'=>$obj['is_default'])) ?></label></div>
      <?php echo tooltip_text(TEXT_CHOICES_IS_DEFAULT_INFO); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="bg_color"><?php echo TEXT_BACKGROUND_COLOR ?></label>
    <div class="col-md-9">
    	<div class="input-group input-small color colorpicker-default" data-color="<?php echo (strlen($obj['bg_color'])>0 ? $obj['bg_color']:'#ff0000')?>" >
  	   <?php echo input_tag('bg_color',$obj['bg_color'],array('class'=>'form-control input-small')) ?>
        <span class="input-group-btn">
  				<button class="btn btn-default" type="button"><i style="background-color: #3865a8;"></i>&nbsp;</button>
  			</span>
  		</div>
      <?php echo tooltip_text(TEXT_CHOICES_BACKGROUND_COLOR_INFO); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
    <div class="col-md-9">	
  	  <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-small')) ?>
      <?php echo tooltip_text(TEXT_CHOICES_SORT_ORDER_INFO); ?>
    </div>			
  </div>
  
  <?php if($fields_info['type']=='fieldtype_grouped_users'):?>
  <div class="form-group">
  	<label class="col-md-3 control-label" for="users"><?php echo TEXT_USERS_LIST ?></label>
    <div class="col-md-9">	
  	  <?php 
        $attributes = array('class'=>'form-control chosen-select required',
                          'multiple'=>'multiple',
                          'data-placeholder'=>TEXT_SELECT_SOME_VALUES); 
                          
        echo select_tag('users[]',users::get_choices(),explode(',',$obj['users']),$attributes);
      ?>
      <?php echo tooltip_text(TEXT_CHOICES_USERS_INFO); ?>
    </div>			
  </div>
  <?php endif ?>
    
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#fields_form').validate();                                                                                                    
  });        
</script>   