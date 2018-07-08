<?php echo ajax_modal_template_header(TEXT_BUTTON_EDIT) ?>

<?php echo form_tag('form-copy-to', url_for('global_lists/choices','action=multiple_edit&lists_id=' . $_GET['lists_id']),array('class'=>'form-horizontal')) ?>
<?php echo input_hidden_tag('selected_fields') ?>
<div class="modal-body" >
  <div id="modal-body-content">    

  <div class="form-group">
  	<label class="col-md-3 control-label" for="parent_id"><?php echo TEXT_PARENT ?></label>
    <div class="col-md-9">	
  	  <?php
  	    $choices = array();
  	    foreach(global_lists::get_choices($_GET['lists_id']) as $k=>$v)
  	    {  	    	
  	    	if($k=='')
  	    	{
  	    		$choices[-1] = TEXT_NONE;
  	    		$choices[0] = TEXT_TOP_LEVEL;
  	    	}
  	    	else 
  	    	{
  	    		$choices[$k] = $v;
  	    	}
  	    }  	    
  	  	echo select_tag('parent_id',$choices ,-1,array('class'=>'form-control input-large chosen-select')) 
  	  ?>
      <?php echo tooltip_text(TEXT_CHOICES_PARENT_INFO); ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="bg_color"><?php echo TEXT_BACKGROUND_COLOR ?></label>
    <div class="col-md-9">
    	<div class="input-group input-small color colorpicker-default" data-color="<?php echo '#ff0000' ?>" >
  	   <?php echo input_tag('bg_color','',array('class'=>'form-control input-small')) ?>
        <span class="input-group-btn">
  				<button class="btn btn-default" type="button"><i style="background-color: #3865a8;"></i>&nbsp;</button>
  			</span>
  		</div>
      <?php echo tooltip_text(TEXT_CHOICES_BACKGROUND_COLOR_INFO); ?>
    </div>			
  </div>
      
  </div>
</div> 
<?php echo ajax_modal_template_footer() ?>

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