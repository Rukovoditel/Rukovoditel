<?php

//checking access
if(isset($_GET['id']) and !users::has_comments_access('update'))
{          
  echo include_modalbox_template(TEXT_WARNING,TEXT_NO_ACCESS);
  exit();
}
elseif(!users::has_comments_access('create'))
{
  echo include_modalbox_template(TEXT_WARNING,TEXT_NO_ACCESS);
  exit();
}

$entity_cfg = new entities_cfg($current_entity_id);