
<?php echo ajax_modal_template_header(TEXT_LINK_RECORD) ?>

<?php
  $entity_info = db_find('app_entities',$_GET['related_entities']);
?>
    
<div class="modal-body">    

<?php echo form_tag('search_item_form', url_for('items/search_items','path=' . $_GET['path']), array('class'=>'form-horizontal','onSubmit' => 'return app_search_item_by_id()')) ?>

  <div class="form-group">
  	<label class="col-md-6 control-label"><?php echo TEXT_ENTITY ?>:</label>
    <div class="col-md-6">	  	        
       <p class="form-control-static"><?php echo $entity_info['name'] ?></p>
    </div>			
  </div>

  <div class="form-group">  	
    <div class="col-md-12">	  	        
      <div class="input-group ">    		
        <?php echo input_tag('search_keywords','',array('class'=>'form-control','placeholder'=>TEXT_SEARCH))?>
        <?php echo input_hidden_tag('entities_id',$entity_info['id']) ?>
    		<span class="input-group-btn">  			          
          <button type="submit" class="btn btn-info"  title="<?php echo TEXT_SEARCH ?>" ><i class="fa fa-search"></i></button>
    		</span>
    	</div>      
      <?php echo tooltip_text(TEXT_SEARCH_RECORD_BY_ID_NAME_TIP)?>
    </div>			
  </div>
</form>  
  
<?php echo form_tag('add_related_items', url_for('items/related_item','action=add_related_item&path=' . $_GET['path']), array('class'=>'form-horizontal')) ?>

<?php echo input_hidden_tag('related_entities_id',$entity_info['id']) ?>
  
  <div id="search_item_result"></div>
  
</form> 

 

</div>
 
<?php echo ajax_modal_template_footer('hide-save-button') ?>

<script>
$(function(){
  $("#search_item_form").submit(function(){
    $("#search_item_result").addClass("ajax-loading");
    url = $("#search_item_form").attr("action");
       
    $("#search_item_result").load(url,$(this).serializeArray(),function(){
      $("#search_item_result").removeClass("ajax-loading");
      appHandleChosen()
      
    })
    return false;
  
  })
    
  $("#add_related_items").validate({ignore:""});
  
      
  
})  
</script>
    
    
 
