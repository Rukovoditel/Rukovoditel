<div id="quick-comment">		
	<?php echo form_tag('quick_comments_form', url_for('items/comments','action=save&is_quick_comment=true' ),array('class'=>'form-horizontal')) ?>
		<?php echo input_hidden_tag('path',$_GET['path']) ?>	
		<?php echo textarea_tag('quick_comments_description','',array('class'=>'form-control required','placeholder'=>TEXT_COMMENT_PLACEHOLDER))?>		
		<?php echo submit_tag(TEXT_BUTTON_SAVE,array('class'=>'btn btn-primary btn-primary-modal-action')) . ' <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div> <button onClick="quick_comment_toggle()" type="button" class="btn btn-default">' . TEXT_BUTTON_CANCEL. '</button>' ?>							 
	</form>				
</div>

<script>
	function quick_comment_toggle()
	{
		$('#quick-comment').toggle();
		$('#quick_comments_description').focus();
	}
	
  $(function() {   
    $("#quick_comments_form").validate({
    	submitHandler: function(form)
      {
    		app_prepare_modal_action_loading(form);	
    		form.submit(); 
      }
    });                                                                                   
  });   
</script>