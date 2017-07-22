
<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('entities/fields','action=delete&id=' . $_GET['id'] . '&entities_id=' .$_GET['entities_id'])) ?>
    
<?php if(isset($_GET['redirect_to']))echo input_hidden_tag('redirect_to',$_GET['redirect_to']) ?>

<div class="modal-body">    
<?php echo $content?>
</div> 
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>    
    
 
