
<?php echo ajax_modal_template_header(TEXT_DATABASE_EXPORT_APPLICATION) ?>

<div class="modal-body">
	<p><?php echo TEXT_DATABASE_EXPORT_EXPLANATION ?></p>
	
	<p><?php echo button_tag(TEXT_BUTTON_EXPORT_DATABASE,url_for('tools/db_backup','action=export_template'),false) ?></p>
	
	<?php echo tooltip_text(TEXT_DATABASE_EXPORT_TOOLTIP) ?>
</div>

<?php echo ajax_modal_template_footer('hide-save-button') ?>