
//funciton to use entity template by fields
function use_entity_template(templates_id)
{    
  $('.entities_template_'+templates_id+' li').each(function(){
    
    //get fields info
    fields_id = $(this).attr('data-fields-id');
    fields_type = $(this).attr('data-fields-type');
    fields_value = $(this).html();
    
    //reset field type for some field types 
    if((fields_type=='fieldtype_entity' || fields_type=='fieldtype_users') && $('.field_'+fields_id).attr('type')=='checkbox')
    {
      fields_type = 'fieldtype_checkboxes';
    }
    
    if((fields_type=='fieldtype_entity' || fields_type=='fieldtype_users') && $('.field_'+fields_id).attr('multiple')=='multiple')
    {
      fields_type = 'fieldtype_dropdown_multiple';
    }
    
    //if user don't have access to field then insert template_fields
    //template filed will be using only fro new item (see items.php)
    if(!$('.field_'+fields_id).length && fields_type!='comments_template_description')
    {      
      $('.entities_template_'+templates_id).after('<input name="template_fields['+fields_id+']" type="hidden" value="'+fields_value+'">' )
    }
    else
    {    
      //set value by field type        
      switch(fields_type)
      {
        case 'comments_template_description':
            $('#description').val(fields_value)
            if($('#description').hasClass('editor') || $('#description').hasClass('editor-auto-focus'))
            {            	
              CKEDITOR_holders['description'].setData( fields_value );    
            }
          break;
        case 'fieldtype_textarea_wysiwyg':
            $('#fields_'+fields_id).val(fields_value)
            CKEDITOR_holders['fields_'+fields_id].setData( fields_value );
          break;
        case 'fieldtype_radioboxes':            
            $("input[name='fields["+fields_id+"]']").each(function(){
              
              if($(this).val()==fields_value)
              {
                $(this).attr('checked',true)
                $('#uniform-'+$(this).attr('id')+' span').addClass('checked')
              } 
            })
          break;  
        case 'fieldtype_checkboxes':            
            $("input[name='fields["+fields_id+"][]']").each(function(){
              fields_value_array = fields_value.split(',')
              if(jQuery.inArray($(this).val(),fields_value_array)!=-1)
              {
                $(this).attr('checked',true)
                $('#uniform-'+$(this).attr('id')+' span').addClass('checked')
              }
              else
              {
                $(this).attr('checked',false)
                $('#uniform-'+$(this).attr('id')+' span').removeClass('checked')
              } 
            })
          break;
        case 'fieldtype_dropdown_multiple':
            //reset selected values
            $("#fields_"+fields_id+" option:selected").prop("selected", false);
            
            $.each(fields_value.split(","), function(i,e){
                $("#fields_"+fields_id+" option[value='" + e + "']").prop("selected", true);
            });
            
            $('#fields_'+fields_id).trigger("chosen:updated")
          break;        
        default:  
            $('#fields_'+fields_id).val(fields_value)
            
            if($('#fields_'+fields_id).hasClass('chosen-select'))
            {
              $('#fields_'+fields_id).trigger("chosen:updated")
            }
          break;
      } 
    }  
             
  })
} 