
<?php echo ajax_modal_template_header(TEXT_WARNING) ?>

<?php echo form_tag('backup', url_for('tools/db_backup','action=delete&id=' . $_GET['id'])) ?>
<div class="modal-body">    
<?php
	$backup_info = db_find('app_backups',$_GET['id']);
	echo sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION,'#' . $backup_info['id'] . ' - ' . format_date_time($backup_info['date_added']))
?>
</div> 
<?php echo ajax_modal_template_footer(TEXT_DELETE) ?>

</form>  