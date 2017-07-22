<h3 class="page-title"><?php echo TEXT_HEADING_IMPORT_DATA ?></h3>

<?php $import_fields = array(); ?>

<?php echo form_tag('import_data', url_for('tools/import_data_preview'),array('class'=>'form-horizontal','enctype'=>'multipart/form-data')) ?>

<p><?php echo TEXT_IMPORT_DATA_INFO ?></p>

<div class="alert alert-info"><?php echo TEXT_IMPORT_DATA_TOOLTIP ?></div>
  
  <div class="form-body">
    
  <div class="form-group">
  	<label class="col-md-3 control-label" for="entities_id"><?php echo TEXT_SELECT_ENTITY ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('entities_id',entities::get_choices(),'',array('class'=>'form-control input-large required')) ?>
    </div>			
  </div> 
  
  <div id="parent_item_id_container"></div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_FILENAME ?></label>
    <div class="col-md-9">	
  	  <?php echo input_file_tag('filename',array('class'=>'required')) ?>
      <span class="help-block">*.xls, *.xlsx</span>      
    </div>			
  </div>  
  
      <?php echo submit_tag(TEXT_BUTTON_CONTINUE) ?>  
   </div>


</form> 

<script>
  $(function() { 
    $('#import_data').validate({
    	ignore:'',
      rules: {
          filename: {
            required: true,
            extension: "xls|xlsx"          
          }
          
        }
    }); 
    
    $('#entities_id').change(function(){
      $('#parent_item_id_container').load('<?php echo url_for("tools/import_data","action=set_parent_item_id")?>',{entity_id:$(this).val()},function(response, status, xhr) {
          if (status == "error") {                                 
             $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
          }
          else
          {
          	appHandleChosen()          	
          }                     
        });
    })                                                                 
  });
  
</script>   
    
 
