
<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('items/comments','action=delete&id=' . $_GET['id'] . '&path=' . $_GET['path'])) ?>
    
<div class="modal-body">    
<?php echo $content?>
</div>
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form> 
    
    
    
 
