<?php

class plugins
{
  static public function include_menu($key,$menu = array())
  {
    global $app_plugin_menu,$app_user,$app_redirect_to,$app_module_path, $app_path;
    
    if(isset($app_plugin_menu[$key]))
    {
      $menu = array_merge($menu,$app_plugin_menu[$key]);
    }
    
    return $menu;  
  }
  
  static public function handle_action($action)
  {
    global $app_module, $app_action, $app_module_path,$app_user,$app_redirect_to,$app_path, $current_entity_id;
    
    if(defined('AVAILABLE_PLUGINS'))
    {
      foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
      {                    
        //include plugin
        if(is_file('plugins/' . $plugin .'/handles/' . $action . '.php'))
        {
          require('plugins/' . $plugin .'/handles/' . $action . '.php');
        }
      }
    }  
  }
  
  static public function include_part($part)
  {
    global $app_module, $app_action, $app_module_path,$app_user,$app_redirect_to,$app_path,$app_chat;
    
    if(defined('AVAILABLE_PLUGINS'))
    {
      foreach(explode(',',AVAILABLE_PLUGINS) as $plugin)
      {                            
        //include plugin
        if(is_file('plugins/' . $plugin .'/includes/' . $part . '.php'))
        {          
          require('plugins/' . $plugin .'/includes/' . $part . '.php');
        }
      }
    }  
  }
  
  static public function render_simple_menu_items($key,$url_params='')
  {
    $html = '';
    if(count($plugin_menu = self::include_menu($key))>0)
    {
      foreach($plugin_menu as $v)
      {
        if($v['modalbox']==true)
        {
          $html .= '<li>' . link_to_modalbox($v['title'],$v['url'] . $url_params) . '</li>';
        }
        else
        {        		
          $html .= '<li>' . link_to($v['title'],$v['url'] . $url_params) . '</li>';
        }
      }
    }
    
    return $html;  
  }
  
  static public function include_dashboard_with_selected_menu_items($reports_id)
  {
    global $app_plugin_menu, $app_user;
    
    $html = '';
    
    if(count($app_plugin_menu)>0)
    {

      $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
      $reports_info = db_fetch_array($reports_info_query);
      
      $access_schema = users::get_entities_access_schema($reports_info['entities_id'],$app_user['group_id']);
      
      if(users::has_access('update',$access_schema))
      {
        //update records        
        $html .= '<li>' . link_to_modalbox(TEXT_EXT_UPDATE_RECORDS,url_for('ext/with_selected/update','reports_id=' . $reports_id)) . '</li>';
        
        //link records
        if(count(related_records::get_fields_choices_available_to_relate_to_entity($reports_info['entities_id']))>0)
        {                    
          $html .= '<li>' . link_to_modalbox(TEXT_EXT_LINK_RECORDS,url_for('ext/with_selected/link','path=' . $reports_info['entities_id'] . '&reports_id=' . $reports_info['id'] . '&entities_id=' . $reports_info['entities_id'])) . '</li>';
        } 
      }
              
      if(users::has_access('create',$access_schema))
      {   
        //copy records           
        $html .= '<li>' . link_to_modalbox(TEXT_COPY_RECORDS,url_for('ext/with_selected/copy','reports_id=' . $reports_id)) . '</li>';
                    
        //move records
        $entity_info = db_find('app_entities',$reports_info['entities_id']);
                  
        if($entity_info['parent_id']>0)
        {          
          $html .= '<li>' . link_to_modalbox(TEXT_MOVE_RECORDS,url_for('ext/with_selected/move','reports_id=' . $reports_id)) . '</li>';
        }
      }
    }
    
    return $html;
  
  }
  
}  