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
    
    $access_rules = new access_rules($options['field']['entities_id'], $options['item']);
    
    $redirect_to = '&gotopage[' . $options['reports_id'] . ']=' . $_POST['page'];
    
    if(isset($options['redirect_to']))
    {
      if(strlen($options['redirect_to'])>0)
      {
        $redirect_to .= '&redirect_to=' . $options['redirect_to'];
      }
    }
    
    if(users::has_access('delete',$access_rules->get_access_schema()))
    {
      $list[] = button_icon_delete(url_for('items/delete','id=' .$options['value'] . '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to));
    }
    
    if(users::has_access('update',$access_rules->get_access_schema()))
    {
      $list[] = button_icon_edit(url_for('items/form','id=' . $options['value']. '&entity_id=' . $options['field']['entities_id'] . '&path=' . $options['path'] . $redirect_to));
    }
    
    if(users::has_access('update',$access_rules->get_access_schema()) and $options['field']['entities_id']==1)
    {
      $list[] = button_icon(TEXT_CHANGE_PASSWORD,'fa fa-unlock-alt',url_for('items/change_user_password','path=' . $options['path'] . '-' . $options['value'] . $redirect_to),false);
    }
    
    //check access to action with assigned only
    if($options['hide_actions_buttons']==1)
    {	
    	$list = array();    	
    }
    else
    {
	    if(users::has_users_access_name_to_entity('action_with_assigned',$options['field']['entities_id']))
	    {
	    	if(!users::has_access_to_assigned_item($options['field']['entities_id'],$options['item']['id']))
	    	{
	    		$list = array();
	    	}
	    }
    }
    
    $list[] = button_icon(TEXT_BUTTON_INFO,'fa fa-info',url_for('items/info','path=' . $options['path'] . '-' . $options['value']),false);
    
    return implode(' ',$list);
  }
}