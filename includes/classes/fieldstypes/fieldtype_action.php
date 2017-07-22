<?php

class fieldtype_action
{
  public $options;
  
  function __construct()
  {
    $this->options = array('name'=>TEXT_FIELDTYPE_ACTION_TITLE);
  }
  
  function output($options)
  {
    $list = array();
    
    $redirect_to = '&gotopage[' . $options['reports_id'] . ']=' . $_POST['page'];
    
    if(isset($options['redirect_to']))
    {
      if(strlen($options['redirect_to'])>0)
      {
        $redirect_to .= '&redirect_to=' . $options['redirect_to'];
      }
    }
    
    if(users::has_access('delete'))
    {
      $list[] = button_icon_delete(url_for('items/delete','id=' .$options['value'] . '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to));
    }
    
    if(users::has_access('update'))
    {
      $list[] = button_icon_edit(url_for('items/form','id=' . $options['value']. '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to));
    }
    
    if(users::has_access('update') and $options['field']['entities_id']==1)
    {
      $list[] = button_icon(TEXT_CHANGE_PASSWORD,'fa fa-unlock-alt',url_for('items/change_user_password','path=' . $options['path'] . '-' . $options['value'] . $redirect_to),false);
    }
    
    $list[] = button_icon(TEXT_BUTTON_INFO,'fa fa-info',url_for('items/info','path=' . $options['path'] . '-' . $options['value']),false);
    
    return implode(' ',$list);
  }
}