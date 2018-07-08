<?php

if(!export_templates::has_users_access($current_entity_id,$_GET['templates_id']))
{
  redirect_to('dashboard/access_forbidden');
}

$template_info = db_find('app_ext_export_templates',$_GET['templates_id']);

switch($app_module_action)
{
  case 'print':
  
			$export_template = export_templates::get_html($current_entity_id, $current_item_id,$_GET['templates_id']);
      
      $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            
            <style>               
              body { 
                  color: #000;
                  font-family: \'Open Sans\', sans-serif;
                  padding: 0px !important;
                  margin: 0px !important;                                   
               }
               
               body, table, td {
                font-size: 12px;
                font-style: normal;
               }
               
               table{
                 border-collapse: collapse;
                 border-spacing: 0px;                
               }
      		
      				' . $template_info['template_css'] . '	
               
            </style>
      						
						' . ($template_info['page_orientation']=='landscape' ? '<style type="text/css" media="print"> @page { size: landscape; } </style>':''). '      						
        </head>        
        <body>
         ' . $export_template . '
         <script>
            window.print();
         </script>            
        </body>
      </html>
      ';
                  
                             
      echo $html;
      
      exit();
        
    break;      
  case 'export':

      $export_template = export_templates::get_html($current_entity_id, $current_item_id,$_GET['templates_id']);
      
      $html = '
      <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
            
            <style>               
              body { 
                font-family:   DejaVu Sans, sans-serif;                 
               }
               
              body, table, td {
                font-size: 12px;
                font-style: normal;
              }
              
              table{
                border-collapse: collapse;
                border-spacing: 0px;                
              }
                                          
              c{
                font-family: STXihei;
                font-style: normal;
                font-weight: 400;
              }
      		
      				' . $template_info['template_css'] . '
            </style>
        </head>        
        <body>
         ' . $export_template . '            
        </body>
      </html>
      ';
                  
      //Handle Chinese & Japanese symbols
      $html = preg_replace('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', '<c>${0}</c>',$html);
      $html = str_replace('。','.',$html);
      
      //Handle Korean symbols 
      $html = preg_replace('/[\x{3130}-\x{318F}\x{AC00}-\x{D7AF}]/u', '<c>${0}</c>',$html);
      
                        
      //echo $html;
      //exit();          
      
      $filename = str_replace(' ','_',trim($_POST['filename']));
                              
      require_once("includes/libs/dompdf-0.8.2/autoload.inc.php");    
                                          
      $dompdf = new Dompdf\Dompdf(); 
      
      if($template_info['page_orientation']=='landscape')
      {
      	$dompdf->set_paper('letter', 'landscape');
      }
      
      $dompdf->load_html($html);
      $dompdf->render();
              
      $dompdf->stream($filename);
        
      exit();
    break;
    
    
  case 'export_word':
    
    	$export_template = export_templates::get_html($current_entity_id, $current_item_id,$_GET['templates_id']);
    
    	$html = '<html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    
            <style>
              body {
                  color: #000;
                  font-family: \'Open Sans\', sans-serif;
                  padding: 0px !important;
                  margin: 0px !important;
               }
        
               body, table, td {
                font-size: 12px;
                font-style: normal;
               }
        
               table{
                 border-collapse: collapse;
                 border-spacing: 0px;
               }
    			
    					' . $template_info['template_css'] . '
    							
    					' . ($template_info['page_orientation']=='landscape' ? '
    							@page section{ size:841.7pt 595.45pt;mso-page-orientation:landscape;margin:1.25in 1.0in 1.25in 1.0in;mso-header-margin:.5in;mso-footer-margin:.5in;mso-paper-source:0; }
    							div.section {page:section;}
    							':''). '
        
            </style>
        </head>
        <body>    							
         <div class="section">' . $export_template . '</div>         
        </body>
      </html>
      ';
    	
    	//prepare images
    	$html = str_replace('src="' . DIR_WS_UPLOADS, 'src="' . url_for_file('') . DIR_WS_UPLOADS, $html);
        	
    	$filename = str_replace(' ','_',trim($_POST['filename'])) . '.doc';
    	
    	header("Content-Type: application/vnd.ms-word");
    	header("Expires: 0");
    	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    	header("content-disposition: attachment;filename={$filename}");
    	
    	echo $html;
    	    
    	exit();
    
    	break;    
}  