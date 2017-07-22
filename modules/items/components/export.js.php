<script>
	function open_my_templates_tab()
	{
		$('#items_export_tabs a[href="#my_templates_tab"]').tab('show')
	}
	
	function load_items_export_templates()
	{
		$('#items_export_templates').load('<?php echo url_for('items/export','action=get_templates&path=' . $_GET['path'])?>',function(){
			$('[name=is_default_template]').change(function(){
				id = $(this).attr('data-id');
				set_default_items_export_templates(id)
			})		
		})
				
	}

	function load_items_export_templates_button()
	{
		$('#items_export_templates_button').load('<?php echo url_for('items/export','action=get_templates_button&path=' . $_GET['path'])?>')		
	}

	function set_default_items_export_templates(id)
	{			
		$.ajax({
			type: "POST",
			url: '<?php echo url_for('items/export','action=set_default_templates&path=' . $_GET['path'])?>',
			data: {id:id}			
		}).done(function(){
			
		})		
	}

	function get_default_items_export_templates()
	{			
		$.ajax({
			type: "POST",
			url: '<?php echo url_for('items/export','action=get_default_templates&path=' . $_GET['path'])?>',					
		}).done(function(msg){
			if(msg.length>0)
			{
				data = JSON.parse(msg)
				use_items_export_template(data[0],data[1],data[2])
			}
		})		
	}

	function delete_items_export_templates(id)
	{
		$('.templates-row-'+id).hide('slow');
			
		$.ajax({
			type: "POST",
			url: '<?php echo url_for('items/export','action=delete_templates&path=' . $_GET['path'])?>',
			data: {id:id}			
		}).done(function(){
			load_items_export_templates_button()	
			$('#items_export_templates_selected').hide()
		})		
	}

	function update_items_export_templates()
	{
		id = $('#items_export_templates_selected_id').val()
			
		fields_list = new Array();

		$('.export_fields').each(function(){
			if($(this).attr('checked'))
			{
				fields_list.push($(this).val());
			}
		})
		
		$('#items_export_templates_selected').css("opacity", 0.5)
			
		$.ajax({
			type: "POST",
			url: '<?php echo url_for('items/export','action=update_templates_fields&path=' . $_GET['path'])?>',
			data: {id:id,fields_list:fields_list.toString()}			
		}).done(function(){			
			load_items_export_templates_button()
			$('#items_export_templates_selected').css("opacity", 1)	
		})		
	}

	function update_items_export_templates_name(id,name)
	{
		$.ajax({
			type: "POST",
			url: '<?php echo url_for('items/export','action=update_templates_name&path=' . $_GET['path'])?>',
			data: {id:id,name:name}			
		}).done(function(){
			load_items_export_templates_button()
			$('#items_export_templates_selected').hide()	
		})
	}

	function use_items_export_template(use_fields_list, id, name)
	{
		if(use_fields_list.length>0)
		{
			unchecked_all_by_classname('export_fields');
				
			fields_list = use_fields_list.split(',')

			$.each( fields_list, function( key_next, value_next ) {
				checked_all_by_classname('export_fields_'+value_next)
			})
		}

		$('#items_export_templates_selected').show();
		$('#items_export_templates_selected_data').html(name+'<input type="hidden" value="'+id+'" id="items_export_templates_selected_id">')

		$('#filename').val(name)
	}
	
	$(function(){

		//load templates
		load_items_export_templates()
		load_items_export_templates_button();
		get_default_items_export_templates();
		
		//validate forms
		$('#export_form').validate();		
		$('#export_templates_form').validate();
			

		//add new template
		$('#export_templates_form').submit(function(){

			$('#action_response_msg').html('');
			
			if($('#templates_name').val().length==0)
			{
				return false;
			}
			
	  	fields_list = new Array();

				$('.export_fields').each(function(){
					if($(this).attr('checked'))
					{
						fields_list.push($(this).val());
					}
				})
				
				$('#export_fields_list').val(fields_list.toString());
					
				$.ajax({
					type: "POST",
					url: $(this).attr('action'),
					data:  $(this).serializeArray()
				}).done(function(msg){
					if(msg.length>0)
					{
						$('#action_response_msg').html('<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>'+msg+'</div>');
					}
					else
					{
						$('#templates_name').val('');
					}		
					
					load_items_export_templates();
					load_items_export_templates_button()
				})
			return false;
		})

		
	})
</script>