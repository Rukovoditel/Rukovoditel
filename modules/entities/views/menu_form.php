<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title"><?php echo TEXT_INFO ?></h4>
</div>


<?php echo form_tag('menu_form', url_for('entities/menu','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-medium required')) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="icon"><?php echo TEXT_MENU_ICON_TITLE ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('icon',$obj['icon'],array('class'=>'form-control input-medium ')) ?>
  	  <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="is_default"><?php echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_SELECT_ENTITIES ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo select_tag('entities_list[]',entities::get_choices(),$obj['entities_list'],array('class'=>'form-control input-xlarge chosen-select chosen-sortable','chosen_order'=>$obj['entities_list'],'multiple'=>'multiple')) ?></label></div>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="is_default"><?php echo tooltip_icon(TEXT_SORT_ITEMS_IN_LIST) . TEXT_SELECT_REPORTS ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo select_tag('reports_list[]',entities_menu::get_reports_choices(),$obj['reports_list'],array('class'=>'form-control input-xlarge chosen-select chosen-sortable','chosen_order'=>$obj['reports_list'],'multiple'=>'multiple')) ?></label></div>
    </div>			
  </div>
    
  <div class="form-group">
  	<label class="col-md-4 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-small number')) ?>
    </div>			
  </div> 
     
  </div>
</div>
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() {     
    $('#menu_form').validate({
			submitHandler: function(form){
				app_prepare_modal_action_loading(form)
				form.submit();
			}
    });                                                                   
  });
  
</script>   