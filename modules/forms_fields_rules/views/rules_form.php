
<?php echo ajax_modal_template_header(TEXT_RULE_FOR_FIELD) ?>

<?php echo form_tag('rules_form', url_for('forms_fields_rules/rules','action=save&entities_id=' . $_GET['entities_id'] . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body ajax-modal-width-790">
  
<?php 
$choices = array();
$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('fieldtype_dropdown','fieldtype_radioboxes','fieldtype_user_accessgroups') and f.entities_id='" . _get::int('entities_id') . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{
	$choices[$v['id']] = fields_types::get_option($v['type'],'name',$v['name']); 
}
?>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_SELECT_FIELD ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('fields_id',$choices,$obj['fields_id'],array('class'=>'form-control input-medium required ','onChange'=>'get_fields_choices()')) ?>
  	  <?php echo tooltip_text(TEXT_AVAILABLE_FIELDS . ': ' . TEXT_FIELDTYPE_DROPDOWN_TITLE . ', ' . TEXT_FIELDTYPE_RADIOBOXES_TITLE)?>
    </div>			
  </div>  
  
	<div id="fields_choices"></div>  
   
   </div>
</div>

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
	function get_fields_choices()
	{		
		fields_id = $('#fields_id').val();

		$('#fields_choices').html('<div class="ajax-loading"></div>');
			
		$('#fields_choices').load('<?php echo url_for('forms_fields_rules/rules','action=get_fields_choices&entities_id=' . $_GET['entities_id'] . '&id=' . $obj['id'])?>&fields_id='+fields_id, function(response, status, xhr){

			if (status == "error") {                                 
	       $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
	    }
	    else
	    {	    		    
	    	appHandleChosen()
	      
	      jQuery(window).resize();      
	    }			 	
		})
	}
	
  $(function() {
  	$('#rules_form').validate({ignore:''});       
    get_fields_choices();                                                                
  });
  
</script>   
    
 
