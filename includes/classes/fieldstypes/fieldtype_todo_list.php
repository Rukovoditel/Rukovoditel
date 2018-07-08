<?php

class fieldtype_todo_list
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_TODO_LIST_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_USE_COMMENTS, 'name'=>'use_comments','type'=>'dropdown','params'=>array('class'=>'form-control input-large'),'choices'=>array(''=>'','auto'=>TEXT_AUTO_ADD_COMMENT,'form'=>TEXT_OPEN_COMMENT_FORM),'tooltip_icon'=>TEXT_FIELDTYPE_TODO_LIST_USE_COMMENTS_INFO);
    
    $cfg[] = array('title'=>TEXT_FOR_SUCCESSFUL_CHECK, 'name'=>'text_check','type'=>'input', 'params'=>array('class'=>'form-control input-large'));
    $cfg[] = array('title'=>TEXT_FOR_UNCHECK, 'name'=>'text_unckeck','type'=>'input', 'params'=>array('class'=>'form-control input-large'));
    
                             
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg = fields_types::parse_configuration($field['configuration']);
            
    $attributes = array('rows'=>'3',
                        'class'=>'form-control ' . $cfg['width'] .  ($field['is_heading']==1 ? ' autofocus':'') . ' fieldtype_todo_list field_' . $field['id'] . ($field['is_required']==1 ? ' required noSpace':''));
    
    return textarea_tag('fields[' . $field['id'] . ']',str_replace(array('&lt;','&gt;'),array('<','>'),$obj['field_' . $field['id']]),$attributes);
  }
  
  function process($options)
  {
    return str_replace(array('<','>'),array('&lt;','&gt;'),$options['value']);
  }
  
  function output($options)
  {
  	
  	$html_listing = '';
  	if(strlen($options['value']))
  	{  		
  		foreach(preg_split('/\r\n|\r|\n/', $options['value']) as $key=>$value)
  		{  			  			
  			if(substr($value,0,1)=='*')
  			{  				
  				$value = '<strike>' . substr($value,1) . '</strike>';
  			}
  				
  			$html_listing .= '<div>' . $value . '</div>';
  		}
  	}	
  	
  	if(isset($options['is_export']))
  	{
  	 	return (!isset($options['is_print']) ? str_replace(array('&lt;','&gt;'),array('<','>'),$options['value']) : $html_listing);	
  	}
  	else
  	{
  		if(isset($options['is_listing']))
  		{
  			return $html_listing;  			
  		}
  		else
  		{
  			$cfg = new fields_types_cfg($options['field']['configuration']);
  			
  			$html = '';
  			if(strlen($options['value']))
  			{
  				$html = '<table class="todo-list">';
  				foreach(preg_split('/\r\n|\r|\n/', $options['value']) as $key=>$value)
  				{
  					$is_checked = '';
  					
  					if(substr($value,0,1)=='*')
  					{
  						$is_checked = 'checked';
  						$value = substr($value,1);
  					}
  					
  					$html .= '
  							<tr>
  								<td>' . input_checkbox_tag('todo_list_' . $key,$key,array('class'=>'todo-list-item','checked'=>$is_checked)) . '</td>
  								<td><label class="todo_list_' . $key . (strlen($is_checked) ? ' checked':'' ). '" for="todo_list_' . $key . '">' . $value . '</label></td>
  							</tr>';
  				}
  				$html .= '</table>';
  				
  				//prepare ajax complete code
  				$js_done = '';
  				
  				switch($cfg->get('use_comments'))
  				{
  					case 'auto':
  						$js_done = '
  									load_comments_listing("items_comments_listing",1,"");
  								';
  						break;
  					case 'form':
  						$js_done = '
  								description = (is_checked==1 ? \'' . addslashes($cfg->get('text_check')). '\':\'' . addslashes($cfg->get('text_unckeck')) . '\')+" "+checked_text;  								
  								open_dialog(\'' . url_for('items/comments_form','path=' . $options['path'] ) . '&description=\'+encodeURIComponent(description));  								
  								';
  						break;
  				}
  				
  				$js_function_name = 'todo_list_action_' . $options['field']['id'];
  				
  				$html .= '
  					<script>
  						function ' . $js_function_name . '()
  						{
  							//todo list
							  $(".todo-list-item").change(function(){
							  	list_id = $(this).val();
  								
  								var checked_text = $(".todo_list_"+list_id).html();
  								
							  	if($(this).prop("checked"))
							  	{	
							  		$(".todo_list_"+list_id).addClass("checked")
  									var is_checked = 1  									
							  	}
							  	else
							  	{
							  		$(".todo_list_"+list_id).removeClass("checked")
  									var is_checked = 0  									
							  	}
  								
  								$.ajax({
  									method:"POST",
  									url:"' . url_for('items/todo_list','action=update&path=' . $options['path']) . '",
  									data:{field_id:' . $options['field']['id'] . ',list_id:list_id,is_checked:is_checked}
  								}).done(function(data){
  									' . $js_done . '	
  								})
							  })
  						}
  								
  						$(function(){
  							' . $js_function_name . '()	
  						})		
  					</script>
  						';
  			}
  			return $html;
  		}
    	
  	}
  }
}