
<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('entities/fields_choices','action=delete&id=' . $_GET['id'] . '&entities_id=' .$_GET['entities_id'] . '&fields_id=' .$_GET['fields_id'])) ?>
     
<div class="modal-body">    
<?php echo $content?>
</div> 
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>    
    
 
