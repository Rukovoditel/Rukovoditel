<div class="row">
  <div class="col-sm-5">
    <div class="entitly-listing-buttons-left">
      <?php 
      	if(users::has_comments_access('create'))
      	{
      		echo '
    				<div class="btn-group">' . 
      				button_tag(TEXT_BUTTON_ADD_COMMENT, url_for('items/comments_form','path=' . $_GET['path'])) . 
      				'<button onClick="quick_comment_toggle()" style="margin-left: -5px;" title="' . addslashes(TEXT_QUICK_COMMENT) . '" type="button" class="btn btn-default"><i class="fa fa-paper-plane-o" aria-hidden="true"></i></button>
      			</div>';
      	}
      	?>
    </div>
  </div>
  <div class="col-sm-7">
    <div class="entitly-listing-buttons-right">    
      <?php echo render_comments_search_form($entity_info['id'],'items_comments_listing') ?>
    </div>                    
  </div>
</div> 

<?php require(component_path('items/quick_comment_form')); ?>

<div class="row">
  <div class="col-md-12">
    <div id="items_comments_listing"></div>
  </div>
</div>

<script>
  function load_items_listing(listing_container,page,search_keywords)
  {      
    $('#'+listing_container).append('<div class="data_listing_processing"></div>');
    $('#'+listing_container).css("opacity", 0.5);
    
    $('#'+listing_container).load('<?php echo url_for("items/comments_listing")?>',{path:'<?php echo $_GET["path"]?>',page:page,search_keywords:$('#'+listing_container+'_search_keywords').val()},
      function(response, status, xhr) {
        if (status == "error") {                                 
           $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')                    
        }
        $('#'+listing_container).css("opacity", 1);    
        
        appHandlePopover();   
        
        ckeditor_images_content_prepare();                                                                                         
      }
    );
  }
  
  function reset_search()
  {
    $('#items_comments_listing_search_keywords').val('')
    load_items_listing('items_comments_listing',1)
  }   

  $(function() {     
    load_items_listing('items_comments_listing',1,'');                                                                         
  });
  
    
</script> 