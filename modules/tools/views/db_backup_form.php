

<?php echo ajax_modal_template_header(TEXT_HEADING_DB_BACKUP) ?>

<?php echo form_tag('backup_form', url_for('tools/db_backup','action=backup'),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
    
  <div class="form-group">
  	<label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_COMMENT ?></label>
    <div class="col-md-9">	
  	  <?php echo textarea_tag('description','',array('class'=>'form-control')) ?>
  	  <?php echo tooltip_text(TEXT_BACKUP_DESCRIPTION_TIP) ?>
    </div>			
  </div> 
     
  </div>
</div>
 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_CREATE_BACKUP) ?>

</form> 