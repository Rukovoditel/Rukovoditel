
<?php echo ajax_modal_template_header(TEXT_HEADING_FIELD_IFNO) ?>

<?php echo form_tag('fields_form', url_for('entities/fields','action=save_internal' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body ajax-modal-width-790">
     
<?php echo input_hidden_tag('entities_id',$_GET['entities_id']) ?>

    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo  TEXT_TYPE ?></label>
      <div class="col-md-9">	
    	  <p class="form-control-static"><?php echo fields_types::get_title($obj['type']) ?></p>
    	  <?php echo tooltip_text(TEXT_INTERNAL_FIELD_NOTE) ?>        
      </div>			
    </div>
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo tooltip_icon(TEXT_FIELD_NAME_INFO) . TEXT_NAME ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large')) ?>        
      </div>			
    </div>
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="short_name"><?php echo tooltip_icon(TEXT_FIELD_SHORT_NAME_INFO) . TEXT_SHORT_NAME ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('short_name',$obj['short_name'],array('class'=>'form-control input-small')) ?>        
      </div>			
    </div>
<?php if($obj['type']!='fieldtype_parent_item_id'): ?>    
   	<div class="form-group" id="is-heading-container">
    	<label class="col-md-3 control-label" for="is_heading"><?php echo tooltip_icon(TEXT_IS_HEADING_INFO) . TEXT_IS_HEADING ?></label>
      <div class="col-md-9">	
    	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_heading','1',array('checked'=>$obj['is_heading'])) ?></label></div>        
      </div>			
    </div> 
<?php endif ?>    
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo  TEXT_SORT_ORDER ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-xsmall')) ?>        
      </div>			
    </div>   
             
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

 