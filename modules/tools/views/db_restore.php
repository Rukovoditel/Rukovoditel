<?php echo ajax_modal_template_header(TEXT_WARNING) ?>

<?php echo form_tag('backup', url_for('tools/db_backup','action=restore&id=' . $_GET['id'])); ?> 

<div class="modal-body">    
<?php 
$backup_info = db_find('app_backups',$_GET['id']);
echo sprintf(TEXT_DB_RESTORE_CONFIRMATION,$backup_info['filename'])?>
</div>

<?php echo ajax_modal_template_footer(TEXT_BUTTON_RESTORE) ?>

</form>  