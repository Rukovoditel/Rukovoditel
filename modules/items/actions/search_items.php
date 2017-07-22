<?php

if(isset($_POST['entities_id']) and isset($_POST['search_keywords']))
{
  
  $items_search = new items_search($_POST['entities_id']);
  $items_search->set_search_keywords($_POST['search_keywords']);
  
  if(isset($_GET['path']))
  {
    $items_search->set_path($_GET['path']);  
  }
  
  $choices = $items_search->get_choices();  
  
  if(count($choices)==1)
  {
    $path_info = items::get_path_info($_POST['entities_id'],key($choices));
    
    $html =  '
      <div class="alert alert-info"><a href="' . url_for('items/info','path=' . $path_info['full_path']) . '" target="_blank">' . current($choices). '</a></div>
      <p>' . submit_tag(TEXT_BUTTON_LINK) . '</p>' . input_hidden_tag('items[]',key($choices));
  }
  elseif(count($choices)>1)
  {
    $attributes = array('class'=>'form-control chosen-select required',
                          'multiple'=>'multiple',
                          'data-placeholder'=>TEXT_SELECT_SOME_VALUES);
    $html = select_tag('items[]',$choices,'',$attributes) . 
            '<br><br><p>' . submit_tag(TEXT_BUTTON_LINK) . '</p>';
  }
  else    
  {
    $html = '<div class="alert alert-warning">' . TEXT_NO_RECORDS_FOUND . '</div>';
  }

  $html = '
  <div class="form-group">  	
    <div class="col-md-12">	  	        
      ' . $html . '
    </div>			
  </div>';
  
  echo $html;
}

exit();