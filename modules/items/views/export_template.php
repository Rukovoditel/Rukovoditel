
<?php $template_info = db_find('app_ext_export_templates',$_GET['templates_id'])?>
<?php echo ajax_modal_template_header($template_info['name']) ?>

<?php echo form_tag('export-form', url_for('items/export_template','path=' . $_GET['path'] . '&templates_id=' . $_GET['templates_id'])) . input_hidden_tag('action','export')  ?>

<div class="modal-body ajax-modal-width-790">    

<?php echo export_templates::get_html($current_entity_id, $current_item_id,$_GET['templates_id'])?>

<p><?php 
  echo TEXT_FILENAME . '<br>' . input_tag('filename',$template_info['name'] . ' ' . $current_item_id,array('class'=>'form-control input-large')) 
?></p>

<div><?php echo TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
</div> 

<?php
  $buttons_html = '
		<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button> 
		<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button>'; 
  $buttons_html .= ' <button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' .  TEXT_PRINT . '</button>';
  echo ajax_modal_template_footer('hide-save-button',$buttons_html) 
?>

</form>  

<script>
  $('.btn-template-export').click(function(){
    $('#action').val('export');
    $('#export-form').attr('target','_self')
    $('#export-form').submit();
  })
  
  $('.btn-template-export-word').click(function(){
    $('#action').val('export_word');
    $('#export-form').attr('target','_self')
    $('#export-form').submit();
  })
  
  $('.btn-template-print').click(function(){
    $('#action').val('print');
    $('#export-form').attr('target','_new')
    $('#export-form').submit();
  })
</script>