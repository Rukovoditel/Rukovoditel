<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
	<h4 class="modal-title"><?php echo $heading ?></h4>
</div>

<?php echo form_tag('login', url_for('configuration/users_groups','action=delete&id=' . $_GET['id']),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
     
<?php echo $content?>

  </div>
</div>
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>    
    
 
