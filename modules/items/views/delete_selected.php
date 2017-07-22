<?php echo ajax_modal_template_header(TEXT_HEADING_DELETE) ?>

<?php echo form_tag('form-copy-to', url_for('items/delete_selected','action=delete_selected&reports_id=' . $_GET['reports_id'] . '&path=' . $_GET['path'])) ?>

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

<div class="modal-body" >
  <div id="modal-body-content">    
    <p><?php echo TEXT_DELETE_SELECTED_CONFIRMATION ?></p>  
  </div>
</div> 
<?php echo ajax_modal_template_footer(TEXT_BUTTON_DELETE) ?>

<?php } ?>
</form>  