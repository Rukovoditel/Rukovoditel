
<?php echo ajax_modal_template_header($heading) ?>

<?php echo form_tag('login', url_for('items/','action=delete&id=' . $_GET['id'] . '&path=' . $_GET['path'])) ?>

<?php echo input_hidden_tag('redirect_to',$app_redirect_to) ?>
<?php if(isset($_GET['gotopage'])) echo input_hidden_tag('gotopage[' . key($_GET['gotopage']). ']',current($_GET['gotopage'])) ?>
    
<div class="modal-body">    
<?php echo $content?>
</div>
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>    
    
 
