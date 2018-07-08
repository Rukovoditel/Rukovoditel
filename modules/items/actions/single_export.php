<?php

if(!users::has_access('export'))
{
	redirect_to('dashboard/access_forbidden');
}

$entity_cfg = entities::get_cfg($current_entity_id);

switch($app_module_action)
{  
  case 'export':
    
    if(isset($_POST['fields']))
    {
    	$current_entity_info = db_find('app_entities',$current_entity_id);
    	
      $export = array();
    
      $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
                                      
      //prepare forumulas query
      $listing_sql_query_select = fieldtype_formula::prepare_query_select($current_entity_id, '');
        
      $item_query = db_query("select e.* " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e where id='" . $current_item_id . "'");
      $item = db_fetch_array($item_query);
              
      $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
      while($tabs = db_fetch_array($tabs_query))
      {   
        
        $export_data = array();
        
        $path_info_in_report = array();
        
        if($current_entity_info['parent_id']>0)
        {
        	$path_info_in_report = items::get_path_info($current_entity_id,$item['id']);        	 
        }
                 
        $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in ('fieldtype_action') and f.id in (" . implode(',',$_POST['fields']). ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
        while($field = db_fetch_array($fields_query))
        {            
          //check field access
          if(isset($fields_access_schema[$field['id']]))
          {
            if($fields_access_schema[$field['id']]=='hide') continue;
          }
                  
          switch($field['type'])
          {
            case 'fieldtype_created_by':
                $value = $item['created_by'];
              break;
            case 'fieldtype_date_added':
                $value = $item['date_added'];                
              break;
            case 'fieldtype_action':                
            case 'fieldtype_id':
                $value = $item['id'];
              break;
            default:
                $value = $item['field_' . $field['id']]; 
              break;
          }
          
          $output_options = array('class'=>$field['type'],
                              'value'=>$value,
                              'field'=>$field,
                              'item'=>$item,
                              'is_export'=>true,                              
          										'is_print'=>true,
                              'path'=>$current_path,
          										'path_info' => $path_info_in_report,
          );
          
          if($field['type']=='fieldtype_dropdown_multilevel')
          {
          	$export_data = array_merge($export_data, fieldtype_dropdown_multilevel::output_info_box($output_options,true));          	
          }
          else 
          {          
	          if(in_array($field['type'],array('fieldtype_textarea_wysiwyg','fieldtype_textarea','fieldtype_barcode','fieldtype_qrcode','fieldtype_todo_list')))
	          {
	            $output = trim(fields_types::output($output_options));
	          }
	          else
	          {
	            $output = trim(strip_tags(fields_types::output($output_options)));
	          }   
	           
	          $export_data[] = array(fields_types::get_option($field['type'],'name',$field['name']),$output);
          }
        }  
        
        if(count($export_data)>0)
        {
          $export[$tabs['name']] = $export_data;
        }                  
      }
      
        
      //echo '<pre>';
      //print_r($export);    
      //exit();
      
      $html_comments ='';
      if(users::has_comments_access('view') and $entity_cfg['use_comments']==1 and isset($_POST['export_comments']))
      {
        $html_comments = '';
        
        $listing_sql = "select * from app_comments where entities_id='" . db_input($current_entity_id) . "' and items_id='" . db_input($current_item_id) . "'  order by date_added desc";        
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
            												'is_export'=>true,
            												'is_print'=>true,
                                    'path'=>$current_path,                           
                                    'choices_cache'=>$app_choices_cache);
                  
            $html_fields .='                      
                <tr><th>&bull;&nbsp;' . $field['name'] . ':&nbsp;</th><td>' . trim(strip_tags(fields_types::output($output_options))). '</td></tr>           
            ';
          }
          
          if(strlen($html_fields)>0)
          {
            $html_fields = '<table>' . $html_fields . '</table>';
          }
          
          $attachments = fields_types::output(array('class'=>'fieldtype_attachments','is_export'=>true,'value'=>$item['attachments'],'path'=>$current_path,'field'=>array('entities_id'=>$current_entity_id),'item'=>array('id'=>$current_item_id)));
          $attachments = '<div>' . strip_tags($attachments) . '</div>';
          $html_fields = '<div class="comments_fields">' . $html_fields. '</div>';
          
          if($entity_cfg['use_editor_in_comments']!=1)
          {
            $item['description'] = nl2br($item['description']);
          }
        
           $html_comments .= '
            <tr>                  
              <td align="left" valign="top">' . auto_link_text($item['description']) . $attachments . $html_fields .  '</td>
              <td class="nowrap" valign="top">' . date(CFG_APP_DATETIME_FORMAT,$item['date_added']) . '<br>' . $app_users_cache[$item['created_by']]['name']. '<br>' . '</td>
            </tr>
            <tr>
              <td colspan="2"><hr></td>
            </tr>
          '; 
        
        }
        
        if(strlen($html_comments))
        {
	        $html_comments = '
	          <br><hr><b>' . TEXT_COMMENTS . '</b><br><hr>
	            <table width="100%">' . $html_comments . '</table>';
        }	       
      }
      
      //echo $html_comments;
      //exit();
      
      
      $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            
            <style>               
              body { 
                font-family: DejaVu Sans, sans-serif; 
               }
               
              body, table, td {
                font-size: 12px;
                font-style: normal;
              }
              .comments_fields th{ 
                text-align: left; font-weight: normal; 
              }
      		              
              c{
                font-family: STXihei;
                font-style: normal;
                font-weight: 400;
              }
            </style>
        </head>        
        <body>
        
          <table>';
      
      foreach($export as $tab=>$fields)
      {
        $html .= '
          <tr>
            <td valign="top"><br><b>' . $tab. '</b></td>
            <td valign="top"></td>
          </tr>
        ';
        
        foreach($fields as $v)
        $html .= '
          <tr>
            <td valign="top" width="30%">' . $v[0]. ': </td>
            <td valign="top">' . $v[1]. '</td>
          </tr>
        ';
      }
      
      
      $html .= '</table> ' . $html_comments . '          
          </body>
      </html>
      ';
      
      if($_POST['export_type']=='print')
      {
      	echo $html . '
      		<script>
            window.print();
         </script> ';
      }
      elseif($_POST['export_type']=='word')
      {      	
      	//prepare images
      	$html = str_replace('src="' . DIR_WS_UPLOADS, 'src="' . url_for_file('') . DIR_WS_UPLOADS, $html);
      	 
      	$filename = str_replace(' ','_',trim($_POST['filename'])) . '.doc';
      	 
      	header("Content-Type: application/vnd.ms-word");
      	header("Expires: 0");
      	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      	header("content-disposition: attachment;filename={$filename}");
      	
      	echo $html;
      	
      }
      else
      {                   
	      //Handle Chinese & Japanese symbols
	      $html = preg_replace('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', '<c>${0}</c>',$html);
	      $html = str_replace('。','.',$html);
	      
	      //Handle Korean symbols 
	      $html = preg_replace('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', '<c>${0}</c>',$html);
	      
	                        
	      //echo $html;
	      //exit();          
	      
	      $filename = str_replace(' ','_',trim($_POST['filename']));
	                              
	      require_once("includes/libs/dompdf-0.8.2/autoload.inc.php");    
	                              
	      //echo $html;
	      //exit();
	      
	      $dompdf = new Dompdf\Dompdf();      
	      $dompdf->load_html($html);
	      $dompdf->render();
	              
	      $dompdf->stream($filename);
      }
    }
    exit();
  break;
}  


