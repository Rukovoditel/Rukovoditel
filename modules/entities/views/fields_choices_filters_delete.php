<?php echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php echo form_tag('login', url_for('entities/fields_choices_filters','action=delete&id=' . $_GET['id'] . '&choices_id=' . $_GET['choices_id']. '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id'])) ?>
    
<div class="modal-body">    
<?php echo TEXT_ARE_YOU_SURE?>
</div> 
 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

</form>  