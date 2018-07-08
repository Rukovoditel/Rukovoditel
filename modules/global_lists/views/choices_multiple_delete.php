<?php echo ajax_modal_template_header(TEXT_DELETE_SELECTED) ?>

<?php echo form_tag('form-copy-to', url_for('global_lists/choices','action=multiple_delete&lists_id=' . $_GET['lists_id']),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('selected_fields') ?>
<div class="modal-body" >
  <div id="modal-body-content">    

	<?php echo TEXT_DELETE_SELECTED_CONFIRMATION ?>
      
  </div>
</div> 
<?php echo ajax_modal_template_footer(TEXT_DELETE) ?>

</form>  

<script>
  $(function(){
     if($('.fields_checkbox:checked').length==0)
     {
       $('#modal-body-content').html('<?php echo TEXT_PLEASE_SELECT_ITEMS ?>')
       $('.btn-primary-modal-action').hide()
     }
     else
     {
       selected_fields_list = $('.fields_checkbox:checked').serialize().replace(/choices%5B%5D=/g,'').replace(/&/g,',');
       $('#selected_fields').val(selected_fields_list);              
     } 
     
              
  })     
</script>