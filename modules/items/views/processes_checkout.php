
<?php echo ajax_modal_template_header(sprintf(TEXT_EXT_PROCESS_HEADING,$app_process_info['name'])) ?>
    
<div class="modal-body">  
	<div class="form-body ajax-modal-width-790">  
<?php 
//display configramtion text
	if(strlen($app_process_info['confirmation_text']))
	{
		echo '<p>' . $app_process_info['confirmation_text'] . '</p>';
	}
	
	echo '<p class="form-section form-section-0" style="margin-bottom: 15px;">' . TEXT_EXT_PAYMENT_METHOD. '</p>';
	
	$modules = new modules('payment');
	$payment_modules = $modules->get_active_modules();
	
	if(count($payment_modules))
	{
		$html = '';
		$count = 0;
		foreach($payment_modules as $modules_id=>$modules_title)
		{
			if(!in_array($modules_id,explode(',',$app_process_info['payment_modules']))) continue;
				
			$params  = ($count==0 ? array('checked'=>'checked'):array());
			$params['class']= 'payment_module';
			$params['id']= 'payment_module_' . $modules_id;
			
			$module_info = db_find('app_ext_modules',$modules_id);
			$module = new $module_info['module'];
			
			$cfg = modules::get_configuration($module->configuration(),$modules_id);
						
			$modules_title = (strlen($cfg['custom_title']) ? $cfg['custom_title'] : $modules_title);
						
			$html .= '
				<div style="margin-bottom: 5px;">
					<table><tr><td>'  . input_radiobox_tag('payment_module',$modules_id,$params) . '</td><td><label style="margin-bottom: 0; margin-top: 2px;" for="payment_module_' .$modules_id  . '">' . $modules_title . '</label></td></tr></table>
		    </div>
			';
			
			$count++;
		}
	}
	else 
	{
		$html = TEXT_NO_RECORDS_FOUND;
	}
	
	echo $html;
	
	
?> 

	<div id="payment_module_confirmation" style="min-height: 110px;"></div>
	
	</div>
</div>
 
<?php echo ajax_modal_template_footer('hide-save-button') ?>

</form>  

<script>
	function payment_module_confirmation(module_id)
	{	
		$('#payment_module_confirmation').html('<div class="ajax-loading"></div>');
			
		$('#payment_module_confirmation').load('<?php echo url_for('items/processes_checkout','action=confirmation&id=' . _get::int('id') . '&path=' . $app_path) ?>&module_id='+module_id, function(){
			$('#payment_confirmation').submit(function(){
				$('.btn-pay').before('<div class="ajax-loading-small"></div>');
				$('.btn-pay').hide();				
			})	
		})
	}

	$(function(){
		$('.payment_module').change(function(){
			payment_module_confirmation($(this).val())
		})

		if($('.payment_module:checked').length)
		{
			payment_module_confirmation($('.payment_module:checked').val())			
		}
	})
</script>  

