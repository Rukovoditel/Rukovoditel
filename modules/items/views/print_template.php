<?php $template_info = db_find('app_ext_export_templates',$_GET['templates_id'])?>

<?php echo ajax_modal_template_header($template_info['name']) ?>

<?php
if(!isset($app_selected_items[$_GET['reports_id']])) $app_selected_items[$_GET['reports_id']] = array();

if(count($app_selected_items[$_GET['reports_id']])==0)
{
  echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
}
else
{
?>


<?php echo form_tag('export-form', url_for('items/print_template','path=' . $_GET['path'] . '&templates_id=' . $_GET['templates_id']),array('target'=>'_blank')) ?>
<?php echo input_hidden_tag('action','print') ?>
<?php echo input_hidden_tag('reports_id', $_GET['reports_id']) ?>

<div class="modal-body ajax-modal-width-790">    


<div><?php echo TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
</div> 

<?php
  $buttons_html = '		 
		<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button>'; 
  $buttons_html .= ' <button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' .  TEXT_PRINT . '</button>';
  echo ajax_modal_template_footer('hide-save-button',$buttons_html) 
?>

</form>  

<script>  
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

</form>  

<?php } ?>