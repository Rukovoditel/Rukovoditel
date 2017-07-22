<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('entities/comments_form','action=delete&id=' . $_GET['id'] . '&entities_id=' .$_GET['entities_id'])) ?>
    
<div class="modal-body">    
<?php echo $content?>
</div>
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>   