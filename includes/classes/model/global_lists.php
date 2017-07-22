<?php

class global_lists
{
  static function check_before_delete($id)
  {
    return '';
  }
  
  public static function check_before_delete_choices($id)
  {     
    return '';
  }
  
  static function get_lists_choices($add_empty=true)
  {
    $choices = array();
    
    if($add_empty)
    {
      $choices[''] = '';
    }
    
    $groups_query = db_fetch_all('app_global_lists','','name');
    while($v = db_fetch_array($groups_query))
    {
      $choices[$v['id']] = $v['name'];
    }   
    
    return $choices; 
  }
  
  static function get_name_by_id($id)
  {
    $item = db_find('app_global_lists',$id);
    
    return $item['name'];  
  }
  
  static function get_choices_name_by_id($id)
  {
    $item = db_find('app_global_lists_choices',$id);
    
    return $item['name'];  
  }
  
  public static function get_choices_default_id($lists_id)
  {
    $obj_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($lists_id). "' and is_default=1 limit 1");
    
    if($obj = db_fetch_array($obj_query))
    {
      return $obj['id'];
    }
    else
    {
      return 0;
    } 
        
  } 
    
  static function get_choices_tree($lists_id,$parent_id = 0,$tree = array(),$level=0)
  {
    $choices_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($lists_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
    
    while($v = db_fetch_array($choices_query))
    {
      $tree[] = array_merge($v,array('level'=>$level));
      
      $tree = self::get_choices_tree($lists_id,$v['id'],$tree,$level+1);
    }
    
    return $tree;
  }
  
  static function get_choices_html_tree($lists_id,$parent_id = 0,$tree = '')
  {
    $count_query = db_query("select count(*) as total from app_global_lists_choices where lists_id = '" . db_input($lists_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
    $count = db_fetch_array($count_query);
    
    if($count['total']>0)
    {
      $tree .= '<ol class="dd-list">';
      
      $choices_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($lists_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
      
      while($v = db_fetch_array($choices_query))
      {        
        $tree .= '<li class="dd-item" data-id="' . $v['id'] . '"><div class="dd-handle">' . $v['name'] . '</div>'; 
        
        $tree = self::get_choices_html_tree($lists_id,$v['id'],$tree);
        
        $tree .= '</li>'; 
      }
      
      $tree .= '</ol>';
    }
    
    return $tree;
  }
  
  public static function get_choices($lists_id,$add_empty = true, $empty_text = '')
  {
    $choices = array();
    
    $tree = self::get_choices_tree($lists_id);
            
    if(count($tree)>0)
    {
      if($add_empty)
      {
        $choices[''] = $empty_text;
      }
      
      foreach($tree as $v)
      {
        $choices[$v['id']] = str_repeat(' - ',$v['level']) . $v['name'];
      }            
    }
    
    return $choices;        
  }   
  
  static function choices_sort_tree($lists_id,$tree,$parent_id=0)
  {
    $sort_order = 0;
    foreach($tree as $v)
    {
      db_query("update app_global_lists_choices set parent_id='" . $parent_id . "', sort_order='" . $sort_order . "' where id='" . db_input($v['id']) . "' and lists_id='" . db_input($lists_id) . "'");
      
      if(isset($v['children']))
      {
        self::choices_sort_tree($lists_id,$v['children'],$v['id']);
      }
        
      $sort_order++;
    }
  }
  
  public static function get_cache()
  {
    $list = array();
    
    $choices_query = db_query("select * from app_global_lists_choices");
    
    while($v = db_fetch_array($choices_query))
    {
      $list[$v['id']] = $v;
    }
    
    return $list;
  }
  
  public static function render_value($values = array(), $is_export=false)
  {
    global $app_global_choices_cache;
    
    if(!is_array($values))
    {
      $values = explode(',',$values);
    }
    
    $html  = '';
    foreach($values as $id)
    {
      if(isset($app_global_choices_cache[$id]))
      {
        if($is_export)
        {
          $html .= (strlen($html)==0 ? $app_global_choices_cache[$id]['name'] : ', ' . $app_global_choices_cache[$id]['name']);
        }
        elseif(strlen($app_global_choices_cache[$id]['bg_color'])>0)
        {
          $html .= render_bg_color_block($app_global_choices_cache[$id]['bg_color'],$app_global_choices_cache[$id]['name']);
        }
        else
        {
          $html .= '<div>' . $app_global_choices_cache[$id]['name'] . '</div>';
        } 
      }
    }
    
    return $html;
  }   
}