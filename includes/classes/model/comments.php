<?php

class comments
{
	public static function get_available_filedtypes_in_comments()
	{
		
		return array(
				'fieldtype_input_numeric',
				'fieldtype_input_numeric_comments',
				'fieldtype_input_date',
				'fieldtype_input_datetime',
				'fieldtype_checkboxes',
				'fieldtype_radioboxes',
				'fieldtype_dropdown',
				'fieldtype_grouped_users',
				'fieldtype_progress',
				'fieldtype_textarea',				
				'fieldtype_users',
				'fieldtype_entity');
	}
  public static function get_last_comment_info($entities_id, $items_id,$path)
  {
    global $app_users_cache;
    
    $comments_query_sql = "select * from app_comments where entities_id='" . $entities_id . "' and items_id='" . $items_id . "'  order by date_added desc limit 1";
    $items_query = db_query($comments_query_sql);
    if($item = db_fetch_array($items_query))
    {              
      $descripttion = htmlspecialchars(addslashes(strlen($description = strip_tags($item['description']))>255 ? substr($description,0,255) . '...' : $description));
      
      return '<sup class="last_comment_info" data-toggle="popover" title="' . format_date_time($item['date_added']) . '" data-content="' . str_replace(array("\n","\r","\n\r"),' ',$descripttion) . '" onClick="location.href=\'' . url_for('items/info','path=' . $path). '\'" >' . $app_users_cache[$item['created_by']]['name'] . '</sup>';;
    }
    else
    {
      return '';
    }
  }
  
  public static function delete_item_comments($entity_id,$item_id)
  {
    $comments_query = db_query("select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input($item_id) . "'");
    while($comments = db_fetch_array($comments_query))
    {
      db_query("delete from app_comments_history where comments_id = '" . db_input($comments['id']) . "'");
    }
    
    db_query("delete from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input($item_id) . "'");
  }
    
  public static function render_content_box($entity_id,$item_id,$user_id)
  {
    global $current_path, $app_users_cache;
    
    $user_info = db_find('app_entity_1',$user_id);
    
    $fields_access_schema = users::get_fields_access_schema($entity_id,$user_info['field_6']);
    $choices_cache = fields_choices::get_cache();
    
    
    $count = 0;
    $html = '<table width="100%">';
    $limit = (int)CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS;
    $listing_sql = "select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input($item_id) . "' order by id desc limit " . ($limit+1);
    $items_query = db_query($listing_sql);    
    while($item = db_fetch_array($items_query))
    {
    
    
      $html_fields = '';
      $comments_fields_query = db_query("select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input($item['id']) . "' and f.id=ch.fields_id order by ch.id");
      while($field = db_fetch_array($comments_fields_query))
      {
        //check field access
        if(isset($fields_access_schema[$field['id']]))
        {
          if($fields_access_schema[$field['id']]=='hide') continue;
        }
            
        $output_options = array('class'=>$field['type'],
                                'value'=>$field['fields_value'],
                                'field'=>$field,                            
                                'choices_cache'=>$choices_cache,
                                'path'=>$current_path);
                                                                        
          
        $html_fields .='                      
            <tr><th style="text-align: left; font-family:Arial;font-size:13px; vertical-align: top">&bull;&nbsp;' . $field['name'] . ':&nbsp;</th><td style="font-family:Arial;font-size:13px;">' . fields_types::output($output_options). '</td></tr>           
        ';
      }
      
      if(strlen($html_fields)>0)
      {
        $html_fields = '<table style="padding-top: 7px;">' . $html_fields . '</table>';
      }  
                   
      $attachments = fields_types::output(array('class'=>'fieldtype_attachments','path'=>$current_path,'value'=>$item['attachments'],'field'=>array('entities_id'=>$entity_id),'item'=>array('id'=>$item_id)));
      
      if($count==1)
      {
        $html .= '
          <tr>
            <td colspan="2" style="padding-top: 10px;"><h4>' . TEXT_PREVIOUS_COMMENTS . '</h4></td>            
          </tr>
        ';
      } 
    
      $html .= '
        <tr>
          <td style="vertical-align:top;font-family:Arial;font-size:13px;color:black;padding:2px;border-bottom:1px dashed LightGray">' . auto_link_text($item['description']) . $attachments . $html_fields . '</td>
          <td align="right" style="vertical-align:top;font-family:Arial;font-size:13px;color:black;padding:2px;border-bottom:1px dashed LightGray;white-space:nowrap;">' . date(CFG_APP_DATETIME_FORMAT,$item['date_added']) . '<br>' . $app_users_cache[$item['created_by']]['name']. '<br>' . render_user_photo($app_users_cache[$item['created_by']]['photo']). '</td>
        </tr>
      ';
      
      $count++;
    }
    
    $html .= '</table>';
    
    return $html;
  }
}