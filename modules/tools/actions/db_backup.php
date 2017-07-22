<?php

  switch($app_module_action)
  {    
    case 'restore':
       
    	$info_query = db_query("select * from app_backups where id='" . db_input($_GET['id']). "'");
    	if($info = db_fetch_array($info_query))
    	{
    		$filename = $info['filename'];
    		
    		if(is_file(DIR_FS_BACKUPS . $filename))
    		{
    			
    			//check if file is ZIP archive and unzip it
    			$is_zip_archive = false;
    			if(substr($filename,-4)=='.zip')
    			{	
	    			$zip = new ZipArchive;
	    			$res = $zip->open(DIR_FS_BACKUPS . $filename);
	    			if ($res === TRUE) 
	    			{    				
	    				$zip->extractTo(DIR_FS_BACKUPS);
	    				$zip->close();
	    			}
	    			
	    			$filename = substr($filename,0,-4);
	    			
	    			$is_zip_archive = true;
    			}
    			
    			//restore database
    			backup::restore($filename);
    			
    			if($is_zip_archive)
    			{
    				unlink(DIR_FS_BACKUPS . $filename);
    			}
    		}
    	}
    	             
       redirect_to('users/login','action=logoff');
      break;
      
      case 'restore_file':      	       	
      	if(strlen($filename = $_FILES['filename']['name'])>0)
      	{      		
      		if(substr($filename,-4)=='.sql' or substr($filename,-4)=='.zip')
      		{	      			        			      			      			      	
	      		if(move_uploaded_file($_FILES['filename']['tmp_name'], DIR_FS_BACKUPS . $filename))
	      		{	      			
	      			$is_zip_archive = false;
	      			if(substr($filename,-4)=='.zip')
	      			{
	      				$zip = new ZipArchive;
	      				$res = $zip->open(DIR_FS_BACKUPS . $filename);
	      				if ($res === TRUE)
	      				{
	      					$zip->extractTo(DIR_FS_BACKUPS);
	      					$zip->close();
	      				}
	      				
	      				$zip_filename = $filename; 
	      				$filename = substr($filename,0,-4);
	      			
	      				$is_zip_archive = true;
	      				
	      				if(substr($filename,-4)!='.sql')
	      				{
	      					$filename .= '.sql';
	      				}
	      			}
	      				      				      				      		
	      			//restore database
	      			backup::restore($filename);
	      			 
	      			unlink(DIR_FS_BACKUPS . $filename);
	      			
	      			if($is_zip_archive)
	      			{
	      				unlink(DIR_FS_BACKUPS . $zip_filename);
	      			}	      				      			
	      		}
      		}
      	}
            
      	redirect_to('users/login','action=logoff');
      break;
      
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
        
        $backup = new backup('admin_backup');
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