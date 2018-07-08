<?php

switch($app_module_action)
{
  case 'save':
      $cfg = new entities_cfg($_GET['entities_id']);
      
      if(!isset($_POST['cfg']['item_page_hidden_fields']))
      {
      	$_POST['cfg']['item_page_hidden_fields'] = '';
      }
                  
      foreach($_POST['cfg'] as $k=>$v)
      {                
        $cfg->set($k,$v);
      }
      
      $alerts->add(TEXT_CONFIGURATION_UPDATED,'success');
                      
      redirect_to('entities/item_page_configuration','entities_id=' . $_GET['entities_id']);
      
    break;
}

require(component_path('entities/check_entities_id'));

$cfg = new entities_cfg($_GET['entities_id']);

