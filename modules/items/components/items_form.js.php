<script> 
  $(function() { 

//add method to not accept space  	
  	jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value == '' || value.trim().length != 0;  
    }, '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>');
    
//start form validation                    
    $('#<?php echo $app_items_form_name ?>').validate({ignore:'.ignore-validation',      
    
    //rules for ckeditor
      rules:{
        <?php echo fields::render_required_ckeditor_ruels($current_entity_id); ?>        
      },
      
    //custom error messages
      messages: {			    
        <?php echo fields::render_required_messages($current_entity_id); ?>			   
			},
      
      submitHandler: function(form)
      {    
        
        //include barcode handler
        <?php require(component_path('items/items_form_barcode.js')); ?>
              	
        //stop submit form during unique fields checking
        if($("#"+form.name+" .is-unique-checking").length>0)
        {
          $("#"+form.name+" #form-error-container").html('<div class="alert alert-warning"><?php echo TEXT_PLEASE_WAIT_UNIQUE_FIELDS_CHECKING ?></div>').show().delay(5000).fadeOut();
          return false;
        } 
        
        //stop submit if there are unique error
        if($("#"+form.name+"  .unique-error").length>0)
        {
          $("#"+form.name+"  .unique-error").addClass('error');
          $("#"+form.name+" #form-error-container").html('<div class="alert alert-danger"><?php echo TEXT_UNIQUE_FIELD_VALUE_ERROR_GENERAL ?></div>').show().delay(5000).fadeOut();
          return false;
        }
       
        //replace submit button to Loading to stop double submit
        app_prepare_modal_action_loading(form)

        //update ckeditor fields
        if(CKEDITOR.instances)
        { 
        	for ( instance in CKEDITOR.instances )
        	{
          	CKEDITOR.instances[instance].updateElement();
        	}
        }
                                                  
        <?php 
        //handle users validation
          if($current_entity_id==1)
          { 
            echo 'validate_user_form(form,\'' . url_for('users/validate_form',(isset($_GET['id']) ? 'id=' . $_GET['id']:'') ). '\');'; 
          }
        //handle add item from gantt
          elseif(strstr($app_redirect_to,'ganttreport'))
          {
          	echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function(data) {
                  $("#ajax-modal").modal("hide")
                   gantt_save(data);
                });
            ';
          }
        //handle add item from clalendar  
          elseif(strstr($app_redirect_to,'calendarreport'))
          {
            echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray() 
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  $("#calendar' . str_replace('calendarreport','',$app_redirect_to) . '").fullCalendar("refetchEvents");
                });
            ';
          }
        //handle sub items form submit  
          elseif($app_redirect_to=='parent_modal')
          {          	
          	echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function(item_id) {
                	field_id = $("#sub-items-form").attr("data-field-id")	                	
                	parent_entity_item_id = $("#sub-items-form").attr("data-parent-entity-item-id")
                   
                	current_field_values = $("#fields_"+field_id).val();
                		
                	$("#fields_"+field_id+"_rendered_value").html(\'<div style="width: 18px;"><div class="ajax-loading-small"></div></div>\')
                	$("#fields_"+field_id+"_rendered_value").load("' . url_for('items/render_field_value','path=' . $app_path ). '&fields_id="+field_id+"&item_id="+item_id+"&parent_entity_item_id="+parent_entity_item_id+"&current_field_values="+current_field_values)
                		
                	$("#sub-items-form").remove();
									$(".paretn-items-form").show();	
                });
            ';
          }
        //default form submit if no errors
          else
          { 
            echo 'form.submit();'; 
          } 
        ?>        
      },
      
    //custom erro placment to handle radio etc. 
      errorPlacement: function(error, element) {
        if (element.attr("type") == "radio") 
        {
           error.insertAfter(".radio-list-"+element.attr("data-raido-list"));
        } 
        else 
        {
           error.insertAfter(element);
        }                
      },     
      
    //custom invalid handler
      invalidHandler: function(e, validator) {
  			var errors = validator.numberOfInvalids();
  			if (errors) 
        {
  				var message = '<?php echo TEXT_ERROR_GENERAL ?>';
          
  				$("#<?php echo $app_items_form_name ?> #form-error-container").html('<div class="alert alert-danger">'+message+'</div>').show().delay(5000).fadeOut();
  				
          //auto open tabs with erros
          app_highlight_form_tab_name_with_errors('<?php echo $app_items_form_name ?>')                                                                                			
  			}                 
		}});
//end form validation    
    
    
//start unique field validation		
    $('#<?php echo $app_items_form_name ?> .is-unique').focusout(function(){
                  
      var fields_id = $(this).attr('id').replace('fields_','');
      var fields_value = $(this).val();
      
      //skip validation if value not entered
      if(fields_value.length==0)
      {
        return false;
      }
                            
      //add spinner
      $(this).addClass('is-unique-checking')      
      $(this).after('<div id="is-unique-checking-process-'+fields_id+'" class="fa fa-spinner fa-spin is-unique-checking-process"></div>')
      $(this).removeClass('unique-error')
      $('#is-unique-checking-success-'+fields_id).remove()
                  
      //get error msg
      if($(this).attr('data-unique-error-msg'))
      {
      	var unique_error_msg = $(this).attr('data-unique-error-msg');
      }  
      else
      {
        //set default msg if not stetup
      	unique_error_msg = '<?php echo TEXT_UNIQUE_FIELD_VALUE_ERROR ?>';
      }
      
      var field_obj = $(this);

<?php
	if($app_items_form_name=='registration_form')
	{
		$url = url_for("users/registration","action=check_unique&entities_id=1");
	}
	elseif($app_items_form_name=='public_form')
	{
		$url = url_for("ext/public/form","action=check_unique&entities_id=" . $public_form["entities_id"] . "&id=" . $public_form['id']);
	}
	elseif($app_items_form_name=='account_form')
	{
		$url = url_for("users/account","action=check_unique&entities_id=1");
	}
	else
	{
		$url = url_for("items/items","action=check_unique&path=" . $_GET["path"] . (isset($_GET["id"]) ? "&id=" . $_GET["id"]:"") );
	}
?>
      
      //run ajax validation
      $.ajax({type: "POST",
              url: '<?php echo  $url ?>',
              data: {fields_id:fields_id,fields_value:fields_value,form_session_token:'<?php echo $app_session_token ?>'} 
              }).done(function(data) {
                
                //remove spinner
                field_obj.removeClass('is-unique-checking')
                $('#is-unique-checking-process-'+fields_id).remove()
                
                //displye error message if field not unique
                if(data!='0')
                {
                  field_obj.addClass('error').addClass('unique-error')
                  field_obj.after('<label for="fields_'+fields_id+'" class="error" style="display: inline-block;">'+unique_error_msg+'</label>')
                }
                else
                {
                  field_obj.after('<div id="is-unique-checking-success-'+fields_id+'" class="fa fa-check is-unique-checking-success"></div>')
                }
                  
              });
    })
    
    //remove success icon if user continue enter value
    $('#<?php echo $app_items_form_name ?> .is-unique').focusin(function(){
      var fields_id = $(this).attr('id').replace('fields_','');
      $('#is-unique-checking-success-'+fields_id).remove()      
    })
    
    //extra handle datapiker language since cfg in layout ignored
		$.fn.datepicker.dates['en'] = {
		    days: [<?php echo TEXT_DATEPICKER_DAYS ?>],
		    daysShort: [<?php echo TEXT_DATEPICKER_DAYSSHORT ?>],
		    daysMin: [<?php echo TEXT_DATEPICKER_DAYSMIN ?>],
		    months: [<?php echo TEXT_DATEPICKER_MONTHS ?>],
		    monthsShort: [<?php echo TEXT_DATEPICKER_MONTHSSHORT ?>],
		    today: "<?php echo TEXT_DATEPICKER_TODAY ?>"    
		};    
    
		//force check unique data field
		$('.datepicker').datepicker({
      rtl: App.isRTL(),
      autoclose: true,
      weekStart: app_cfg_first_day_of_week,
      format: 'yyyy-mm-dd',
  	}).on('changeDate', function(e) {
    	$('.fieldtype_input_date.is-unique',this).trigger('focusout');
    	var fields_id = $('.fieldtype_input_date',this).attr('id');
    	$('.error[for="'+fields_id+'"').remove() 
    });
		
//end unisque field validation   

/*
 * start vpic vin decoder
 */
	$('.vpic-vin-decoder').click(function(){
		field_id = $(this).attr('data-field-id');
		vin_number = $('#fields_'+field_id).val()
		$('#field_'+field_id+'_vin_data').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>'); 
		$('#field_'+field_id+'_vin_data').load('<?php echo url_for('dashboard/vpic','action=input_vin_decode') ?>',{field_id:field_id,vin_number:vin_number})		
	})
/* end vpic vin decoder */	


//start btn-submodal-open
	app_handle_submodal_open_btn()

	//button back
	$('.btn-submodal-back').click(function(){
			$('#sub-items-form').remove();
			$('.paretn-items-form').show();
	})
//end btn-submodal-open


//curecny convert
	app_currency_converter('#<?php echo $app_items_form_name ?>')
	
  });
  
</script>


<!-- include form fields display rules  -->
<?php require(component_path('items/forms_fields_rules.js')); ?>
	                                                                      