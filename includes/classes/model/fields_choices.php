<?php

class fields_choices
{

  public static function check_before_delete($id)
  {     
    return '';
  }
  
  public static function get_name_by_id($id)
  {
    $obj = db_find('app_fields_choices',$id);
    
    return $obj['name'];
  }
  
  public static function get_default_id($fields_id)
  {
    $obj_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($fields_id). "' and is_default=1 limit 1");
    
    if($obj = db_fetch_array($obj_query))
    {
      return $obj['id'];
    }
    else
    {
      return 0;
    } 
        
  }  
  
  public static function get_tree($fields_id,$parent_id = 0,$tree = array(),$level=0)
  {
    $choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($fields_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
    
    while($v = db_fetch_array($choices_query))
    {
      $tree[] = array_merge($v,array('level'=>$level));
      
      $tree = fields_choices::get_tree($fields_id,$v['id'],$tree,$level+1);
    }
    
    return $tree;
  }
  
  
  static function get_html_tree($fields_id,$parent_id = 0,$tree = '')
  {
    $count_query = db_query("select count(*) as total from app_fields_choices where fields_id = '" . db_input($fields_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
    $count = db_fetch_array($count_query);
    
    if($count['total']>0)
    {
      $tree .= '<ol class="dd-list">';
      
      $choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($fields_id). "' and parent_id='" . db_input($parent_id). "' order by sort_order, name");
      
      while($v = db_fetch_array($choices_query))
      {        
        $tree .= '<li class="dd-item" data-id="' . $v['id'] . '"><div class="dd-handle">' . $v['name'] . '</div>'; 
        
        $tree = self::get_html_tree($fields_id,$v['id'],$tree);
        
        $tree .= '</li>'; 
      }
      
      $tree .= '</ol>';
    }
    
    return $tree;
  }
  
  static function sort_tree($fields_id,$tree,$parent_id=0)
  {
    $sort_order = 0;
    foreach($tree as $v)
    {
      db_query("update app_fields_choices set parent_id='" . $parent_id . "', sort_order='" . $sort_order . "' where id='" . db_input($v['id']) . "' and fields_id='" . db_input($fields_id) . "'");
      
      if(isset($v['children']))
      {
        self::sort_tree($fields_id,$v['children'],$v['id']);
      }
        
      $sort_order++;
    }
  }  
  
  public static function get_choices($fields_id,$add_empty = true, $empty_text = '')
  {
    $choices = array();
    
    $tree = fields_choices::get_tree($fields_id);
            
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
  
  public static function get_cache()
  {
    $list = array();
    
    $choices_query = db_query("select * from app_fields_choices");
    
    while($v = db_fetch_array($choices_query))
    {
      $list[$v['id']] = $v;
    }
    
    return $list;
  }
  
  public static function render_value($values = array(), $is_export=false)
  {
    global $app_choices_cache;
    
    if(!is_array($values))
    {
      $values = explode(',',$values);
    }
    
    $html  = '';
    foreach($values as $id)
    {
      if(isset($app_choices_cache[$id]))
      {
        if($is_export)
        {
          $html .= (strlen($html)==0 ? $app_choices_cache[$id]['name'] : ', ' . $app_choices_cache[$id]['name']);
        }
        elseif(strlen($app_choices_cache[$id]['bg_color'])>0)
        {
          $html .= render_bg_color_block($app_choices_cache[$id]['bg_color'],$app_choices_cache[$id]['name']);
        }
        else
        {
          $html .= '<div>' . $app_choices_cache[$id]['name'] . '</div>';
        } 
      }
    }
    
    return $html;
  }
    
}