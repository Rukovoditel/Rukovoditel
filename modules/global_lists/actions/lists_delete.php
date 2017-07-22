<?php
    
  $msg = global_lists::check_before_delete($_GET['id']);
        
  if(strlen($msg)>0)
  {
    $heading = TEXT_WARNING;
    $content = $msg;
    $button_title = false;
  }
  else
  {
    $heading = TEXT_HEADING_DELETE; 
    $content =  sprintf(TEXT_DEFAULT_DELETE_CONFIRMATION,global_lists::get_name_by_id($_GET['id']));
    $button_title = TEXT_BUTTON_DELETE;
  }