
<?php echo ajax_modal_template_header(TEXT_HEADING_REPORTS_FILTER_IFNO) ?>

<?php echo form_tag('reports_filters', url_for('entities/entityfield_filters','action=save&reports_id=' . $_GET['reports_id'] . (isset($_GET['id']) ? '&id=' . $_GET['id']:''). '&entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="fields_id"><?php echo TEXT_SELECT_FIELD ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('fields_id',fields::get_filters_choices($reports_info['entities_id'],false),$obj['fields_id'],array('class'=>'form-control required','onChange'=>'load_fitlers_options(this.value)')) ?>
    </div>			
  </div>
     
<div id="filters_options"></div>
 
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>


  $(function() { 
    $('#reports_filters').validate();
    
    load_fitlers_options($('#fields_id').val());                                                                      
  });
  
  
function load_fitlers_options(fields_id)
{
  if(fields_id>0)
  {
    $('#filters_options').html('<div class="ajax-loading"></div>');
    
    $('#filters_options').load('<?php echo url_for("reports/filters_options")?>',{fields_id:fields_id, id:'<?php echo $obj["id"] ?>'},function(response, status, xhr) {
      if (status == "error") {                                 
         $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
      }
      else
      {   
        appHandleUniform();
      }
    });
  }
}  
  
</script>  

    
 
