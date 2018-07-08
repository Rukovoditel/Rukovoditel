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
  
  static public function include_dashboard_with_selected_menu_items($reports_id, $url_params = '')
  {
    global $app_plugin_menu, $app_user;
    
    $html = '';
            
    $reports_info_query = db_query("select * from app_reports where id='" . db_input($reports_id). "'");
    $reports_info = db_fetch_array($reports_info_query);
    
    if(class_exists('processes'))
    {
    	$processes = new processes($reports_info['entities_id']);    	
    	$processes->rdirect_to = (strstr($url_params, 'parent_item_info_page') ? 'parent_item_info_page':'dashboard');   
    	$html .= $processes->render_buttons('menu_with_selected',$reports_info['id']);
    }
    
    $access_schema = users::get_entities_access_schema($reports_info['entities_id'],$app_user['group_id']);
    
    if(count($app_plugin_menu)>0 and users::has_access('update_selected',$access_schema))
    {           
      if(users::has_access('update',$access_schema))
      {
        //update records        
        $html .= '<li>' . link_to_modalbox('<i class="fa fa-edit"></i> ' . TEXT_EXT_UPDATE_RECORDS,url_for('ext/with_selected/update','reports_id=' . $reports_id . $url_params)) . '</li>';
        
        //link records
        if(count(related_records::get_fields_choices_available_to_relate_to_entity($reports_info['entities_id']))>0)
        {                    
          $html .= '<li>' . link_to_modalbox('<i class="fa fa-link"></i> ' . TEXT_EXT_LINK_RECORDS,url_for('ext/with_selected/link','reports_id=' . $reports_info['id'] . '&entities_id=' . $reports_info['entities_id'] . (strlen($url_params) ? $url_params : '&path=' . $reports_info['entities_id']))) . '</li>';
        } 
      }
          
      //copy records
      if(users::has_access('copy',$access_schema))
      {                     
        $html .= '<li>' . link_to_modalbox('<i class="fa fa-files-o"></i> ' . TEXT_COPY_RECORDS,url_for('ext/with_selected/copy','reports_id=' . $reports_id . $url_params)) . '</li>';
      }
      
      //move records
      if(users::has_access('move',$access_schema))
      {
        $entity_info = db_find('app_entities',$reports_info['entities_id']);
                  
        if($entity_info['parent_id']>0)
        {          
          $html .= '<li>' . link_to_modalbox('<i class="fa fa-arrows-h"></i> ' . TEXT_MOVE_RECORDS,url_for('ext/with_selected/move','reports_id=' . $reports_id . $url_params)) . '</li>';
        }
      }
    }
    
    if(entities::has_subentities($reports_info['entities_id'])==0 and users::has_access('delete',$access_schema) and users::has_access('delete_selected',$access_schema) and $reports_info['entities_id']!=1)
    {
    	$html .= '<li>' . link_to_modalbox('<i class="fa fa-trash-o"></i> ' . TEXT_BUTTON_DELETE,url_for('items/delete_selected','reports_id=' . $reports_info['id'] . (strlen($url_params) ? $url_params : '&redirect_to=dashboard&path=' . $reports_info['entities_id']) )) . '</li>';
    }
    
    return $html;
  
  }
  
}  