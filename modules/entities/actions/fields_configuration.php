<?php

if(isset($_POST['field_type']))
{
  $field_type = new $_POST['field_type'];
  
  //echo '<h3 class="form-section">' . fields_types::get_tooltip($_POST['field_type']) . '</h3>';
  
  echo '
    <div class="form-group">
    	<label class="col-md-3 control-label">' . TEXT_INFO. '</label>
      <div class="col-md-9"><p class="form-control-static">' .  fields_types::get_tooltip($_POST['field_type']) . '</p>
      </div>			                                                                                                   
    </div>
  ';
  
  if(method_exists($field_type,'get_configuration'))
  {  
    echo fields_types::render_configuration($field_type->get_configuration(array('entities_id'=>$_POST['entities_id'])),$_POST['id']);
  }
}

exit();