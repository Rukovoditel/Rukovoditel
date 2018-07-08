<?php

  switch($app_module_action)
  {         
    case 'download':
	    	$info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']). "'");
	    	if($info = db_fetch_array($info_query))
	    	{
	    		$filename = $info['filename'];
	    		
	        if(is_file(DIR_FS_BACKUPS . $filename))
	        {	          	         
	          header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
	          header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
	          header("Cache-Control: no-cache, must-revalidate");
	          header("Pragma: no-cache");
	          header("Content-Type: Application/octet-stream");
	          header("Content-disposition: attachment; filename=" . $filename);
	      
	          readfile(DIR_FS_BACKUPS . $filename);
	          
	          exit();
	        }
	        else
	        {
	          $alerts->add(TEXT_FILE_NOT_FOUD,'error');
	          
	          redirect_to('tools/db_backup');
	        }
	    	}
      break;
    case 'delete':
    		
    		$info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']). "'");
    		if($info = db_fetch_array($info_query))
    		{    				
    			$filename = $info['filename'];
    			
	        if(is_file(DIR_FS_BACKUPS . $filename))
	        {
	          unlink(DIR_FS_BACKUPS . $filename);
	          
	          $alerts->add(TEXT_BACKUP_DELETED,'success');
	        }
	        else
	        {
	          $alerts->add(TEXT_FILE_NOT_FOUD,'error');
	        }
	        
	        db_delete_row('app_backups', $info['id']);
    		}
        
        redirect_to('tools/db_backup');
      break;
    case 'backup':
    
        $backup = new backup();
        $backup->set_description($_POST['description']);
        $backup->create();
                        
        $alerts->add(TEXT_BACKUP_CREATED,'success');
        
        redirect_to('tools/db_backup');
      break;
    case 'export_template':
        
        $filename = str_replace(' ','_',CFG_APP_NAME) . '_' . date('Y-m-d_H-i') . '_Rukovoditel_' . PROJECT_VERSION . '.sql';
        
        $backup = new backup();
        $backup->set_filename($filename);
        $backup->create();
                          
        header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D,d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: Application/octet-stream");
        header("Content-disposition: attachment; filename=" . $filename);
    
        readfile(DIR_FS_BACKUPS . $filename);
        
        unlink(DIR_FS_BACKUPS . $filename);
        
        exit();
          
      break;
  }
  
  backup::reset();