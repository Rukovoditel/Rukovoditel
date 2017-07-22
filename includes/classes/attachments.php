<?php

class attachments
{
  public static function render_preview($field_id, $attachments_list)
  {    
    $html = '';
    
    if(count($attachments_list)>0)
    {
      $html .= '
      <div class="table-scrollable attachments-form-list">
      <table class="table table-striped table-hover">
        <tbody>'; 
      foreach($attachments_list as $v)
      {
        $file = attachments::parse_filename($v);
        
        $html .= '
          <tr>
            <td>' . $file['name']. '</td>
            <td><label class="checkbox">' . input_checkbox_tag('delete_attachments[' . $file['file'] . ']',1) . ' ' . TEXT_DELETE . '</label></td>
          </tr>
        ';
      }
      
      $html .= '
        </tbody>
      </table>
      </div>';
            
    }
    
    $html .= input_hidden_tag('fields[' . $field_id . ']',implode(',',$attachments_list));
    
    return $html;  
  
  }
  
  public static function delete_attachments($entities_id, $items_id)
  {
    $fields_query = db_query("select * from app_fields where entities_id='" . db_input($entities_id) . "' and type in ('fieldtype_attachments','fieldtype_input_file')");
    while($fields = db_fetch_array($fields_query))
    {
      $items_query = db_query("select * from app_entity_" . $entities_id . " where id='" . db_input($items_id) . "'");
      if($items = db_fetch_array($items_query))
      {
        if(strlen($files = $items['field_' . $fields['id']])>0)
        {
          foreach(explode(',',$files) as $file)
          {
            $file = attachments::parse_filename($file);
            if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] .'/'. $file['file_sha1']))
            {
              unlink(DIR_WS_ATTACHMENTS . $file['folder']  .'/' . $file['file_sha1']);                            
            }
          }
        }
      }
    }        
  }
  
  public static function delete_comments_attachments($comments_id)
  {
    $comments_query = db_query("select * from app_comments where id='" . db_input($comments_id) . "' and length(attachments)>0");
    if($comments = db_fetch_array($comments_query))
    {
      foreach(explode(',',$comments['attachments']) as $file)
      {
        $file = attachments::parse_filename($file);
        if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] .'/'. $file['file_sha1']))
        {
          unlink(DIR_WS_ATTACHMENTS . $file['folder']  .'/' . $file['file_sha1']);                            
        }
      }
    }
  }
  
  public static function prepare_image_filename($filename)
  {
    $filename = str_replace(array(" ",","),"_",trim($filename));
    
    if(!is_dir(DIR_WS_IMAGES  . date('Y'))) 
    {
      mkdir(DIR_WS_IMAGES  . date('Y'));
    }
    
    if(!is_dir(DIR_WS_IMAGES  . date('Y') . '/' . date('m')))
    {
      mkdir(DIR_WS_IMAGES  . date('Y'). '/' . date('m'));
    }
    
    if(!is_dir(DIR_WS_IMAGES  . date('Y') . '/' . date('m') . '/' . date('d')))
    {
      mkdir(DIR_WS_IMAGES  . date('Y'). '/' . date('m'). '/' . date('d'));
    }
            
    return array('file'=> $filename,
                 'folder'=>date('Y') . '/' . date('m') . '/' . date('d'));
  }
  
  public static function prepare_filename($filename)
  {
    $filename = str_replace(array(" ",","),"_",trim($filename));
    
    if(!is_dir(DIR_WS_ATTACHMENTS  . date('Y'))) 
    {
      mkdir(DIR_WS_ATTACHMENTS  . date('Y'));
    }
    
    if(!is_dir(DIR_WS_ATTACHMENTS  . date('Y') . '/' . date('m')))
    {
      mkdir(DIR_WS_ATTACHMENTS  . date('Y'). '/' . date('m'));
    }
    
    if(!is_dir(DIR_WS_ATTACHMENTS  . date('Y') . '/' . date('m') . '/' . date('d')))
    {
      mkdir(DIR_WS_ATTACHMENTS  . date('Y'). '/' . date('m'). '/' . date('d'));
    }
            
    return array('name'=>time() . '_' . $filename,
                 'file'=>sha1(time() . '_' . $filename),
                 'folder'=>date('Y') . '/' . date('m') . '/' . date('d'));
  }
  
  public static function parse_filename($filename)
  {
    //get filetime
    $filename_array = explode('_',$filename);    
    $filetime = (int)$filename_array[0];
    
    //get foler
    $folder = date('Y',$filetime) . '/' . date('m',$filetime) . '/' . date('d',$filetime);
    
    //get filename
    $name = substr($filename,strpos($filename,'_')+1);
    
    //get extension
    $filename_array = explode('.',$filename);    
    $extension = strtolower($filename_array[sizeof($filename_array)-1]);
    
    if(is_file('images/fileicons/' . $extension . '.png'))
    {
      $icon = 'images/fileicons/' . $extension . '.png'; 
    }
    else
    {
      $icon = 'images/fileicons/attachment.png';
    }
    
    
    if(is_file($file_path = DIR_WS_ATTACHMENTS  . $folder  .'/'. sha1($filename)))
    {           
      $size = attachments::file_size_convert(filesize($file_path));
    }
    //old way that was in version 1.0
    elseif(is_file($file_path= DIR_WS_ATTACHMENTS  . strtolower($name[0])  .'/'. $filename))
    {
      $size = attachments::file_size_convert(filesize($file_path));
      $folder = strtolower($name[0]);
    }
    else
    {
      $size = 0;
    }
        
    return array('name' => $name,
                 'file' =>$filename,
                 'file_sha1' =>sha1($filename),
                 'file_path' =>$file_path,
                 'folder' => DIR_WS_ATTACHMENTS . $folder . '/',
                 'is_image'=> is_image($file_path),
                 'is_pdf'=> is_pdf($filename),
                 'is_exel'=>is_excel($filename),                  
                 'icon'=>$icon,
                 'size'=> $size,
                 'folder' => $folder);
  }
  
  public static function file_size_convert($bytes)
  {
      $bytes = floatval($bytes);
          $arBytes = array(
              0 => array(
                  "UNIT" => "TB",
                  "VALUE" => pow(1024, 4)
              ),
              1 => array(
                  "UNIT" => "GB",
                  "VALUE" => pow(1024, 3)
              ),
              2 => array(
                  "UNIT" => "MB",
                  "VALUE" => pow(1024, 2)
              ),
              3 => array(
                  "UNIT" => "KB",
                  "VALUE" => 1024
              ),
              4 => array(
                  "UNIT" => "B",
                  "VALUE" => 1
              ),
          );
  
      foreach($arBytes as $arItem)
      {
          if($bytes >= $arItem["VALUE"])
          {
              $result = $bytes / $arItem["VALUE"];
              $result = str_replace(".", "," , strval(round($result, 1)))." ".$arItem["UNIT"];
              break;
          }
      }
      return $result;
  } 
  
  public static function copy($files)
  {
    $new_files_list = array();
    
    if(strlen($files)>0)
    {
      $files = explode(',',$files);
      
      foreach($files as $file)
      {
        $file_info = self::parse_filename($file);
                
        $new_file = self::prepare_filename($file_info['name']);
        $new_file_path = DIR_WS_ATTACHMENTS  . '/' . $new_file['folder'] . '/' . $new_file['file']; 
        
        $current_file_path = $file_info['file_path'];
           
        if(is_file($current_file_path))
        {        
          if (copy($current_file_path, $new_file_path)) 
          {
            $new_files_list[] = $new_file['name']; 
          }
        }
      }
    }
    
    return implode(',',$new_files_list);   
  }
  
  public static function resize($filepath)
  {
  	$max_img_width = (int)CFG_MAX_IMAGE_WIDTH;
  	$max_img_height = (int)CFG_MAX_IMAGE_HEIGHT;
  	
  	if(($max_img_width>0 or $max_img_height>0) and CFG_RESIZE_IMAGES==1 and is_image($filepath))
  	{
  		$img = getimagesize($filepath);
  		
  		//skip large images
  		$skip_size = (int)CFG_SKIP_IMAGE_RESIZE;
  		if($skip_size>0)
  		{
  			if($img[0]>$skip_size or $img[1]>$skip_size)
  			{
  				return false;
  			}
  		}
  		
  		//skip image types
  		if(strlen(CFG_RESIZE_IMAGES_TYPES)>0)
  		{
  			if(!in_array($img[2],explode(',',CFG_RESIZE_IMAGES_TYPES)))
  			{
  				return false;
  			}
  		}
  		  		  	
  		//resize image
  		if($img[0]>$max_img_width or $img[1]>$max_img_height)
  		{
  			if($img[0]>$img[1])
  			{
  				image_resize($filepath,$filepath,$max_img_width);
  			}
  			else
  			{
  				image_resize($filepath,$filepath,'',$max_img_height);
  			}
  		}  		  	
  	}
  }
}