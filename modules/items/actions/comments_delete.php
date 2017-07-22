<?php

if(users::has_comments_access('delete'))
{  
  $heading = TEXT_HEADING_DELETE;
  $content = TEXT_ARE_YOU_SURE;
  $button_title = TEXT_BUTTON_DELETE; 
}
else
{
  $heading = TEXT_WARNING;
  $content = TEXT_NO_ACCESS;
  $button_title = 'hide-save-button';  
}