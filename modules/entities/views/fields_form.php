
<?php echo ajax_modal_template_header(TEXT_HEADING_FIELD_IFNO) ?>

<?php echo form_tag('fields_form', url_for('entities/fields','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body ajax-modal-width-790">
     
<?php echo input_hidden_tag('entities_id',$_GET['entities_id']) ?>
<?php if(isset($_GET['redirect_to']))echo input_hidden_tag('redirect_to',$_GET['redirect_to']) ?>

<?php
$forms_tabs_choices = forms_tabs::get_choices($_GET['entities_id']);
if(count($forms_tabs_choices)==1) echo input_hidden_tag('forms_tabs_id',key($forms_tabs_choices))  
?>

<ul class="nav nav-tabs">
  <li class="active"><a href="#general_info"  data-toggle="tab"><?php echo TEXT_GENERAL_INFO ?></a></li>
  <li><a href="#is_required_tab"  data-toggle="tab"><?php echo TEXT_IS_REQUIRED ?></a></li>
  <li><a href="#tooltip"  data-toggle="tab"><?php echo TEXT_TOOLTIP ?></a></li>  
  <li><a href="#note"  data-toggle="tab"><?php echo TEXT_NOTE ?></a></li>
</ul>
 
<div class="tab-content">
  <div class="tab-pane fade active in" id="general_info">
  
    <?php if(count($forms_tabs_choices)>1): ?>
    <div class="form-group">
    	<label class="col-md-3 control-label" for="forms_tabs_id"><?php echo tooltip_icon(TEXT_FORM_TAB_INFO) . TEXT_FORM_TAB ?></label>
      <div class="col-md-9">	
    	  <?php echo select_tag('forms_tabs_id',$forms_tabs_choices, (isset($_GET['forms_tabs_id']) ? $_GET['forms_tabs_id'] : $obj['forms_tabs_id']),array('class'=>'form-control input-medium required')) ?>        
      </div>			
    </div>
    <?php endif ?>
    
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo tooltip_icon(TEXT_FIELD_NAME_INFO) . TEXT_NAME ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>        
      </div>			
    </div>
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="short_name"><?php echo tooltip_icon(TEXT_FIELD_SHORT_NAME_INFO) . TEXT_SHORT_NAME ?></label>
      <div class="col-md-9">	
    	  <?php echo input_tag('short_name',$obj['short_name'],array('class'=>'form-control input-small')) ?>        
      </div>			
    </div>
            
    <div class="form-group">
    	<label class="col-md-3 control-label" for="type"><?php echo tooltip_icon(TEXT_FIELD_TYPE_INFO) . TEXT_TYPE ?></label>
      <div class="col-md-9">	
    	  <?php echo select_tag('type',fields_types::get_choices(), $obj['type'],array('class'=>'form-control input-large required','onChange'=>'fields_types_configuration(this.value)')) ?>        
      </div>			
    </div>
    
    <div class="form-group" id="is-heading-container">
    	<label class="col-md-3 control-label" for="is_heading"><?php echo tooltip_icon(TEXT_IS_HEADING_INFO) . TEXT_IS_HEADING ?></label>
      <div class="col-md-9">	
    	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_heading','1',array('checked'=>$obj['is_heading'])) ?></label></div>        
      </div>			
    </div>
    
    <div id="fields_types_configuration"></div>  
          
  </div>
  <div class="tab-pane fade" id="is_required_tab">
  
    <div class="form-group">
    	<label class="col-md-3 control-label" for="is_required"><?php echo tooltip_icon(TEXT_IS_REQUIRED_INFO) . TEXT_IS_REQUIRED ?></label>
      <div class="col-md-9">	
    	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('is_required','1',array('checked'=>$obj['is_required'])) ?></label></div>        
      </div>			
    </div>
    
     <div class="form-group">
    	<label class="col-md-3 control-label" for="required_message"><?php echo TEXT_REQUIRED_MESSAGE ?></label>
      <div class="col-md-9">	
    	  <?php echo textarea_tag('required_message',$obj['required_message'],array('rows'=>3,'class'=>'form-control')) ?>
        <?php echo tooltip_text(TEXT_REQUIRED_MESSAGE_INFO); ?>
      </div>			
    </div>
    
  </div>
  <div class="tab-pane fade" id="tooltip">
  
    <div class="form-group">
    	<label class="col-md-3 control-label" for="tooltip"><?php echo TEXT_TOOLTIP ?></label>
      <div class="col-md-9">	
    	  <?php echo textarea_tag('tooltip',$obj['tooltip'],array('rows'=>3,'class'=>'form-control')) ?>
        <?php echo tooltip_text(TEXT_TOOLTIP_INFO); ?>
      </div>			
    </div>
    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="tooltip_display_as"><?php echo tooltip_icon(TEXT_TOOLTIP_DISPLAY_AS_ICON_INFO) . TEXT_TOOLTIP_DISPLAY_AS_ICON ?></label>
      <div class="col-md-9">	
    	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('tooltip_display_as','icon',array('checked'=>$obj['tooltip_display_as'])) ?></label></div>        
      </div>			
    </div>    
     
  </div>  
  
  <div class="tab-pane fade" id="note">
	  <div class="form-group">
	  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_ADMINISTRATOR_NOTE ?></label>
	    <div class="col-md-9">	
	  	  <?php echo textarea_tag('notes',$obj['notes'],array('class'=>'form-control')) ?>
	    </div>			
	  </div> 
  </div>
</div>

 
 
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>

  $(function() { 
    $('#fields_form').validate({ignore:'',invalidHandler: function(e, validator) {
			var errors = validator.numberOfInvalids();
      
        if (errors) {
  				var message = '<?php echo TEXT_ERROR_GENERAL ?>';
  				$("div#form-error-container").html('<div class="alert alert-danger">'+message+'</div>');
  				$("div#form-error-container").show();
          $("div#form-error-container").delay(5000).fadeOut();				
  			}         
		}});
    
                    
    fields_types_configuration($('#type').val());
    
    check_is_heading_option()
                                                                              
  });
  
function fields_types_configuration(field_type)
{ 
  check_is_heading_option()
  
  $('#fields_types_configuration').html('<div class="ajax-loading"></div>');
   
  $('#fields_types_configuration').load('<?php echo url_for("entities/fields_configuration")?>',{field_type:field_type, id:'<?php echo $obj["id"] ?>',entities_id:'<?php echo $_GET["entities_id"]?>'},function(response, status, xhr) {
    if (status == "error") {                                 
       $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
    }
    else
    {
      appHandleUniform();
      
      jQuery(window).resize();      
    }    
  });  
   
}  

function check_is_heading_option()
{
   selected_type = $('#type').val()
   
   if($.inArray(selected_type,["fieldtype_input_numeric_comments","fieldtype_input_url","fieldtype_attachments","fieldtype_input_file","fieldtype_image","fieldtype_textarea_wysiwyg","fieldtype_formula","fieldtype_related_records","fieldtype_boolean"])==-1)
   {
     $('#is-heading-container').show() 
   }
   else
   {
     $('#is-heading-container').hide()
     $('#is_heading').prop('checked',false)     
   }
} 

  
</script>   