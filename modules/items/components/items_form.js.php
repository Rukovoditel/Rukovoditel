<script>
  $(function() { 

//add method to not accept space  	
  	jQuery.validator.addMethod("noSpace", function(value, element) { 
      return value == '' || value.trim().length != 0;  
    }, '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>');
    
//start form validation                    
    $('#items_form').validate({ignore:'',      
    
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
        if($("#items_form .is-unique-checking").length>0)
        {
          $("div#form-error-container").html('<div class="alert alert-warning"><?php echo TEXT_PLEASE_WAIT_UNIQUE_FIELDS_CHECKING ?></div>').show().delay(5000).fadeOut();
          return false;
        } 
        
        //stop submit if there are unique error
        if($("#items_form .unique-error").length>0)
        {
          $("#items_form .unique-error").addClass('error');
          $("div#form-error-container").html('<div class="alert alert-danger"><?php echo TEXT_UNIQUE_FIELD_VALUE_ERROR_GENERAL ?></div>').show().delay(5000).fadeOut();
          return false;
        }
       
        //replace submit button to Loading to stop double submit
        app_prepare_modal_action_loading(form)
                                                  
        <?php 
        //handle users validation
          if($current_entity_id==1)
          { 
            echo 'validate_user_form(form,\'' . url_for('users/validate_form',(isset($_GET['id']) ? 'id=' . $_GET['id']:'') ). '\');'; 
          }
        //handle add item from clalendar  
          elseif(strstr($app_redirect_to,'calendarreport'))
          {
            echo '
              $.ajax({type: "POST",
                url: $("#items_form").attr("action"),
                data: $("#items_form").serializeArray() 
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  $("#calendar").fullCalendar("refetchEvents");
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
          
  				$("div#form-error-container").html('<div class="alert alert-danger">'+message+'</div>').show().delay(5000).fadeOut();
  				
          //auto open tabs with erros
          app_highlight_form_tab_name_with_errors('items_form')                                                                                			
  			}                 
		}});
//end form validation    
    
    
//start unique field validation		
    $('.is-unique').focusout(function(){
                  
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
      
      var field_obj = $(this);
      
      //run ajax validation
      $.ajax({type: "POST",
              url: '<?php echo url_for("items/items","action=check_unique&path=" . $_GET["path"] . (isset($_GET["id"]) ? "&id=" . $_GET["id"]:"") ) ?>',
              data: {fields_id:fields_id,fields_value:fields_value} 
              }).done(function(data) {
                
                //remove spinner
                field_obj.removeClass('is-unique-checking')
                $('#is-unique-checking-process-'+fields_id).remove()
                
                //displye error message if field not unique
                if(data!='0')
                {
                  field_obj.addClass('error').addClass('unique-error')
                  field_obj.after('<label for="fields_'+fields_id+'" class="error" style="display: inline-block;"><?php echo TEXT_UNIQUE_FIELD_VALUE_ERROR ?></label>')
                }
                else
                {
                  field_obj.after('<div id="is-unique-checking-success-'+fields_id+'" class="fa fa-check is-unique-checking-success"></div>')
                }
                  
              });
    })
    
    //remove success icon if user continue enter value
    $('.is-unique').focusin(function(){
      var fields_id = $(this).attr('id').replace('fields_','');
      $('#is-unique-checking-success-'+fields_id).remove()      
    })
    
		//force check unique data field
		$('.datepicker').datepicker().on('changeDate', function(e) {
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
                                                                        
  });
  
</script> 