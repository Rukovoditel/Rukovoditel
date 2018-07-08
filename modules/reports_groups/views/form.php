
<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php echo form_tag('reports_form', url_for('reports_groups/reports','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
  <div class="form-body">
  
    
  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_ICON_TITLE; ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('menu_icon', $obj['menu_icon'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="in_menu"><?php echo TEXT_IN_MENU ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_menu','1',array('checked'=>$obj['in_menu'])) ?></label></div>
    </div>			
  </div>  
  
	<div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_SORT_ORDER ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-small')) ?>
    </div>			
  </div>
           

      
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#reports_form').validate({
			submitHandler: function(form){
				app_prepare_modal_action_loading(form)
				form.submit();
			}
    });                                                
  });  
</script>   
    
 
