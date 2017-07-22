
<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('global_lists/choices','action=delete&id=' . $_GET['id'] . '&lists_id=' .$_GET['lists_id'])) ?>
     
<div class="modal-body">    
<?php echo $content?>
</div> 
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>    
    
 
