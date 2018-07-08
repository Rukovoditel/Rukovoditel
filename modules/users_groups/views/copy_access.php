<?php echo ajax_modal_template_header(TEXT_COPY_ACCESS) ?>

<?php echo form_tag('form-copy-to', url_for('users_groups/copy_access','action=copy_selected&id=' . $users_groups_info['id']),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('selected_items') ?>
<div class="modal-body" >
  <div id="modal-body-content">    

<?php 
	$choices = array(''=>'');
		
	$groups_query = db_query("select * from app_access_groups where id!='" . $users_groups_info['id'] . "' order by sort_order, name");
	while($v = db_fetch_array($groups_query))
	{
		$choices[$v['id']] = $v['name'];
	}
?>    
    <div class="form-group">
    	<label class="col-md-4 control-label" for="type"><?php echo TEXT_USERS_GROUPS ?></label>
      <div class="col-md-8">	
    	  <?php echo select_tag('copy_to_group_id',$choices,'',array('class'=>'form-control required')) ?>
    	  <?php echo tooltip_text(TEXT_COPY_ACCESS_INFO) ?>        
      </div>			
    </div>
             
  </div>
</div> 
<?php echo ajax_modal_template_footer(TEXT_COPY) ?>

</form>  

<script>
  $(function(){
    $('#form-copy-to').validate();
    	
     if($('.items_checkbox:checked').length==0)
     {
       $('#modal-body-content').html('<?php echo TEXT_SELECT_ENTITY_TO_COPY_ACCESS ?>')
       $('.btn-primary-modal-action').hide()
     }
     else
     {
       selected_fields_list = $('.items_checkbox:checked').serialize().replace(/items%5B%5D=/g,'').replace(/&/g,',');
       $('#selected_items').val(selected_fields_list);            
     } 
       
  })  
</script>