<h3 class="page-title"><?php echo TEXT_USERS_NOTIFICATIONS ?></h3>


<?php echo button_tag(TEXT_DELETE_SELECTED, url_for("users/notifications",'action=delete_selected'),false,array('class'=>'btn btn-primary')) ?>

<div class="row">
  <div class="col-md-12">
    <div id="users_notifications_listing"></div>
  </div>
</div>


<script>
  function load_items_listing(listing_container,page,search_keywords)
  {      
    $('#'+listing_container).append('<div class="data_listing_processing"></div>');
    $('#'+listing_container).css("opacity", 0.5);
    
    $('#'+listing_container).load('<?php echo url_for("users/notifications_listing") ?>',{page:page},
      function(response, status, xhr) {
        if (status == "error") {                                 
           $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
        }
        
        $('#'+listing_container).css("opacity", 1);

        appHandleUniformInListing()

        handle_itmes_select(listing_container)
                                                                                                                    
      }
    );
  }

  function handle_itmes_select(listing_container)
  {  
    $('#'+listing_container+' .items_checkbox').click(function(){                
      $.ajax({type: "POST",url: '<?php echo url_for("users/notifications","action=select")?>',data: {id:$(this).val(),checked: $(this).attr('checked')}});
    })
    
    $('#'+listing_container+' .select_all_items').click(function(){

        	
    	$.ajax({type: "POST",url: '<?php echo url_for("users/notifications","action=select_all")?>',data: {checked: $(this).attr('checked')} });
      
      if($(this).attr('checked'))
      {
        
        $('#'+listing_container+' .items_checkbox').each(function(){            
          $(this).attr('checked',true)
          $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').addClass('checked')          
        })
      }
      else
      {
        $('#'+listing_container+' .items_checkbox').each(function(){
          $(this).attr('checked',false)
          $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').removeClass('checked')
        })
      }            
    })
  }  


  $(function() {     
    load_items_listing('users_notifications_listing',1,'');                                                                         
  });
  
    
</script> 
