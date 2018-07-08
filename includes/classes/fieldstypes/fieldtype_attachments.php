<?php

class fieldtype_attachments
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_ATTACHMENTS_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_USE_IMAGE_PREVIEW, 'name'=>'use_image_preview','type'=>'checkbox','tooltip'=>TEXT_USE_IMAGE_PREVIEW_TIP);
    $cfg[] = array('title'=>TEXT_DISPLAY_FILE_DATE_ADDED, 'name'=>'display_date_added','type'=>'checkbox');
    $cfg[] = array('title'=>TEXT_FILES_UPLOAD_LIMIT, 'name'=>'upload_limit','type'=>'input', 'tooltip_icon'=>TEXT_FILES_UPLOAD_LIMIT_TIP,'params'=>array('class'=>'form-control input-xsmall'));
    $cfg[] = array('title'=>TEXT_FILES_UPLOAD_SIZE_LIMIT, 'name'=>'upload_size_limit','type'=>'input', 'tooltip_icon'=>TEXT_FILES_UPLOAD_SIZE_LIMIT_TIP,'tooltip'=>TEXT_MAX_UPLOAD_FILE_SIZE . ' ' . CFG_SERVER_UPLOAD_MAX_FILESIZE . 'MB ' . TEXT_MAX_UPLOAD_FILE_SIZE_TIP,'params'=>array('class'=>'form-control input-xsmall'));
        
    return $cfg;
  } 
  
  function render($field,$obj,$params = array())
  {
    global $uploadify_attachments, $uploadify_attachments_queue, $current_path, $app_user, $app_items_form_name, $public_form,$app_session_token;
    
    if(!isset($field['configuration'])) $field['configuration'] = '';
    
    $cfg = new fields_types_cfg($field['configuration']);
    
    $field_id = $field['id'];
    
    $uploadify_attachments[$field_id] = array();
    $uploadify_attachments_queue[$field_id] = array();
    
    if(strlen($obj['field_' . $field['id']])>0)
    {
      $uploadify_attachments[$field_id] = explode(',',$obj['field_' . $field['id']]);
    }
    
    $timestamp = time();
    
    $delete_file_url = '';
    
    if($app_items_form_name=='registration_form')
    {
    	$form_token = md5($app_session_token . $timestamp);
    	$uploadScript = url_for('users/registration','action=attachments_upload&field_id=' . $field_id ,true);
    	$previewScript = url_for('users/registration','action=attachments_preview&field_id=' . $field_id . '&token=' . $form_token);    	
    }
    elseif($app_items_form_name=='public_form')
    {
    	$form_token = md5($app_session_token . $timestamp);
    	$uploadScript = url_for('ext/public/form','action=attachments_upload&id=' . $public_form['id'] . '&field_id=' . $field_id ,true);
    	$previewScript = url_for('ext/public/form','action=attachments_preview&field_id=' . $field_id . '&id=' . $public_form['id'] . '&token=' . $form_token);    	
    }
    elseif($app_items_form_name=='account_form')
    {
    	$form_token = md5($app_user['id'] . $timestamp);
    	$uploadScript = url_for('users/account','action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id ,true);
    	$previewScript = url_for('users/account','action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token);
    	$delete_file_url = url_for('users/account','action=attachments_delete_in_queue');
    }
    else
    {
    	$form_token = md5($app_user['id'] . $timestamp);
    	$uploadScript = url_for('items/items','action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id ,true);
    	$previewScript = url_for('items/items','action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token);
    	$delete_file_url = url_for('items/items','action=attachments_delete_in_queue&path=' . $_GET['path']);
    }
    
    $uploadLimit = (strlen($cfg->get('upload_limit')) ? (int)$cfg->get('upload_limit') : 0);
    $onComplateAction = ($uploadLimit>0 ? 'onUploadComplete':'onQueueComplete');
           
    
    $attachments_preview_html = attachments::render_preview($field_id, $uploadify_attachments[$field_id],$delete_file_url);
    
    $html = '
      <div class="form-control-static"> 
        <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload_' . $field_id . '" id="uploadifive_attachments_upload_' . $field_id . '" /> 
      </div>
      
      <div id="uploadifive_queue_list_' . $field_id . '"></div>
      <div id="uploadifive_attachments_list_' . $field_id . '">
        ' . $attachments_preview_html . '        
      </div>
      
      <script type="text/javascript">
		
      var is_file_uploading = null;  
        		
      function uploadifive_oncomplate_filed_' . $field_id . '()
      {
      	is_file_uploading = null  
        $(".uploadifive-queue-item.complete").fadeOut();
        $("#uploadifive_attachments_list_' . $field_id . '").append("<div class=\"loading_data\"></div>");
        $("#uploadifive_attachments_list_' . $field_id . '").load("' .  $previewScript  . '"); 	
  		}		
      
  		$(function() {
  			$("#uploadifive_attachments_upload_' . $field_id  . '").uploadifive({
  				"auto"             : true,  
          "dnd"              : false, 
          "buttonClass"      : "btn btn-default btn-upload",
          "buttonText"       : "<i class=\"fa fa-upload\"></i> ' . TEXT_ADD_ATTACHMENTS. '",				
  				"formData"         : {
  									   "timestamp" : ' . $timestamp . ',
  									   "token"     : "' .  $form_token . '",
  									   "form_session_token" : "' . $app_session_token. '"		
  				                     },
  				"queueID"          : "uploadifive_queue_list_' . $field_id . '",
          "fileSizeLimit" : "' . (strlen($cfg->get('upload_size_limit')) ? (int)$cfg->get('upload_size_limit') : CFG_SERVER_UPLOAD_MAX_FILESIZE) . 'MB",
          "queueSizeLimit" : ' . $uploadLimit . ',
  				"uploadScript"     : "' . $uploadScript . '",
          "onUpload"         :  function(filesToUpload){
            is_file_uploading = true;  					
          },
  				"' . $onComplateAction . '" : function(file, data) {
            uploadifive_oncomplate_filed_' . $field_id . '()
          },
          "onError":function(errorType) {
             is_file_uploading = null;             
           },
          "onCancel"     : function() { 	
             is_file_uploading = null;  				
           } 		
  			});
                        
        $("button[type=submit]").bind("click",function(){                                                 
            if(is_file_uploading)
            {
              alert("' . TEXT_PLEASE_WAYIT_FILES_LOADING . '"); return false;
            }                           
          });
        
  		});
	</script>
    '; 
    
    return $html;
  }
  
  function process($options)
  {
    $attachments = explode(',',$options['value']);
            
    if(isset($_POST['delete_attachments']))
    {    	
      foreach($_POST['delete_attachments'] as $filename=>$v)
      {                 
          
        if(($key = array_search($filename,$attachments))!==false)
        {                    
          unset($attachments[$key]);
        }
                        
        $file = attachments::parse_filename($filename);
        if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] .'/'. $file['file_sha1']))
        {
          unlink(DIR_WS_ATTACHMENTS . $file['folder']  .'/' . $file['file_sha1']);
        }
      }            
    }
    
    //remove out of data tmp attachments
    db_query("delete from app_attachments where date_added!='" . date('Y-m-d') . "'");
    
    $options['value'] = implode(',',$attachments);
            
    return $options['value'];
  }
  
  function output($options)
  {    
    if(strlen($options['value'])>0)
    {
    	if(isset($options['is_public_form']))
    	{
    		$html = array();
    		foreach(explode(',',$options['value']) as $filename)
    		{
    			$file = attachments::parse_filename($filename);
    			$html[] = link_to($file['name'],url_for('ext/public/check','action=download_attachment&id=' . $options['is_public_form'] . '&item=' . $options['item']['id'] . '&field=' . $options['field']['id'] . '&file=' . urlencode(base64_encode($filename))),array('target'=>'_blank')) . '  <small>(' . $file['size']. ')</small>';
    		}
    		
    		return implode('<br>',$html);
    	}
      elseif(isset($options['is_export']))
      {
        $html = array();
        foreach(explode(',',$options['value']) as $filename)
        {
          $file = attachments::parse_filename($filename);
          $html[] = $file['name'];
        }
        
        return implode(', ',$html);
      }
      else
      { 
      	$is_listing = (isset($options['is_listing']) ? true : false);
      	
        $cfg = new fields_types_cfg($options['field']['configuration']);
                        
        $image_gallery = array();
        
        $fancybox_css_class ='';
        
        $html = '';
        
        if(!$is_listing)
        {
	        $html .= '
	        <div class="table-scrollable">
	          <table class="table">
	            <tbody>
	              <tr>
	                <td>';       
        } 
                        
        $use_file_storage = false;
        
        //check if field using file storage
        if(class_exists('file_storage') and $options['field']['id']>0)
        {
        	$use_file_storage = file_storage::check($options['field']['id']);
        }
        
        if($use_file_storage)
        {
        	$html .= '
		                  <ul style="padding: 0px; margin: 0px;">';
        	foreach(explode(',',$options['value']) as $filename)
        	{
        		$file = attachments::parse_filename($filename);
        		 
        		$link = link_to($file['name'],url_for('items/info','path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($filename))) . '&field=' . $options['field']['id'] );
        		
        		$link .= ' ' . link_to('<i class="fa fa-download"></i>',url_for('items/info' ,'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($filename))) . '&field=' . $options['field']['id'] );
        		
        		$html .= '
		              <li style="list-style-image: url(' . url_for_file($file['icon']) . '); margin-left: 20px;">' .  $link. '</li>
		            ';
        	}	
        	
        	$html .= '
		                  </ul>';
        	
        }
        else 
        {	
        
		       $html .= ' 		
		                  <ul style="padding: 0px; margin: 0px;">';
		        foreach(explode(',',$options['value']) as $filename)
		        {
		          $file = attachments::parse_filename($filename);
		          		          		         
		          if($file['is_image'])
		          {
		            if(strlen($fancybox_css_class)==0)
		            {
		              $fancybox_css_class = 'fancybox' . time();
		            }
		            		            
		            $link = link_to($file['name'],url_for('items/info', 'path=' . $options['path'] . '&action=preview_attachment_image&file=' . urlencode(base64_encode($filename))),array('class'=>$fancybox_css_class,'title'=>$file['name'],'data-fancybox-group'=>'gallery'));
		            
		            //generate image preview
		            if($cfg->get('use_image_preview')==1 and !$is_listing)
		            {
		              $img = '<img src="' . url_for('items/info','path=' . $options['path'] . '&action=download_attachment&preview=1&file=' . urlencode(base64_encode($filename))) . '">';
		              $image_gallery[] = array('image'=>link_to($img,url_for('items/info', 'path=' . $options['path'] .'&action=preview_attachment_image&file=' . urlencode(base64_encode($filename))),array('class'=>$fancybox_css_class,'title'=>$file['name'],'data-fancybox-group'=>'gallery')),
		                                       'download_link'=>link_to('<i class="fa fa-download"></i> ' . TEXT_DOWNLOAD,url_for('items/info', 'path=' . $options['path'] .'&action=download_attachment&file=' . urlencode(base64_encode($filename)))));
		              
		              continue; 
		            }
		          }
		          elseif($file['is_pdf'])
		          {
		            $link = link_to($file['name'],url_for('items/info','path=' . $options['path'] . '&action=download_attachment&preview=1&file=' . urlencode(base64_encode($filename))),array('target'=>'_blank'));  
		          }
		          elseif($file['is_exel'])
		          {          
		            $link = link_to($file['name'],url_for('items/info','path=' . $options['path'] . '&action=preview_attachment_exel&file=' . urlencode(base64_encode($filename))),array('target'=>'_blank'));  
		          }
		          else
		          {
		            $link = link_to($file['name'],url_for('items/info','path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($filename))));
		          }
		          
		          $link .= ' ' . link_to('<i class="fa fa-download"></i>',url_for('items/info','path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($filename)))); 
		          
		          $html .= '
		              <li style="list-style-image: url(' . url_for_file($file['icon']) . '); margin-left: 20px;">' .  $link. ' <small>(' . $file['size']. ')' . self::add_file_date_added($file,$cfg) . '</small></li>
		            ';
		        }
		        $html .= '  
		                  </ul>';
        
        }
        
        if(!$is_listing)
        {
	        $html .='
	                </td>
	              </tr>
	            </tbody>
	          </table>
	        </div>';
        }
        
        //display preview if available
        if(count($image_gallery)>0 and !$is_listing)
        {
          if(count($image_gallery)==count(explode(',',$options['value']))) $html='';
            
          $html .= '
            <div class="attachments-gallery">
              <ul>';
          foreach($image_gallery as $v)
          {
            $html .= '
              <li>
                <div class="gallery-image">' . $v['image']. '</div>
                <div class="gallery-download-link">' . $v['download_link'] . '</div>
              </li>'; 
          }
            
          $html .= '</ul>
            </div>
            <div style="clear:both"></div>
            ';
        }   
        
        if(strlen($fancybox_css_class)>0)
        {
          $html .= '
            <script type="text/javascript">
            	$(document).ready(function() {
            		$(".' . $fancybox_css_class . '").fancybox({type: "ajax"});
            	});
            </script>
          ';
        }
        
        return $html ;
      }
    }
    else
    {
      return '';
    }
  }
  
  static function add_file_date_added($file, $cfg)
  {
  	if($cfg->get('display_date_added')==1)
  	{
  		return ' - ' . format_date_time($file['date_added']);
  	}
  	else
  	{
  		return '';
  	}
  }
}