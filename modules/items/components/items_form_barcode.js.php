$("#"+form.name+" .atuogenerate-value-by-template").each(function(){	
	if($(this).val().length==0)
	{
		template = $(this).attr('data-template');
		
		$("#"+form.name+" input.form-control").each(function(){
			fields_id = $(this).attr('id').replace('fields_','')
			template = template.split('['+fields_id+']').join($(this).val())							
		})		
		
		while(v = template.match(/\[auto:(\d+)\]/))
		{			
			length = v[1];	 
			number_str = ''; 
			for(i=0;i<length;i++)
			{
				number_str = number_str+Math.floor((Math.random() * 9));		
			}
			
			template = template.replace('[auto:'+length+']', number_str);
		}
				
		//alert(template) 
		$(this).val(template)
	}
}) 