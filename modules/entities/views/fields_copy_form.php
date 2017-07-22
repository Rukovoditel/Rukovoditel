<?php echo ajax_modal_template_header(TEXT_COPY_FIELDS) ?>

<?php echo form_tag('form-copy-to', url_for('entities/fields','action=copy_selected&entities_id=' . $_GET['entities_id']),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('selected_fields') ?>
<div class="modal-body" >
  <div id="modal-body-content">    
    
    <div class="form-group">
    	<label class="col-md-4 control-label" for="type"><?php echo TEXT_SELECT_ENTITY ?></label>
      <div class="col-md-8">	
    	  <?php echo select_tag('copy_to_entities_id',entities::get_choices(),$_GET['entities_id'],array('class'=>'form-control')) ?>        
      </div>			
    </div>
    
    <div id="entities_form_tabs"></div>
      
  </div>
</div> 
<?php echo ajax_modal_template_footer(TEXT_COPY) ?>

</form>  

<script>
  $(function(){
     if($('.fields_checkbox:checked').length==0)
     {
       $('#modal-body-content').html('<?php echo TEXT_PLEASE_SELECT_FIELDS ?>')
       $('.btn-primary-modal-action').hide()
     }
     else
     {
       selected_fields_list = $('.fields_checkbox:checked').serialize().replace(/fields%5B%5D=/g,'').replace(/&/g,',');
       $('#selected_fields').val(selected_fields_list);
       
       get_entities_form_tabs();
     } 
     
     $('#copy_to_entities_id').change(function(){
       get_entities_form_tabs();
     })         
  })
  
  
  function get_entities_form_tabs()
  {
    $('#entities_form_tabs').load('<?php echo url_for('entities/fields','action=get_entities_form_tabs') ?>',{entities_id:$('#copy_to_entities_id').val()})
  }  
</script>