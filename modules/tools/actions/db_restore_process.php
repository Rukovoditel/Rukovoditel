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
		$filename = $_POST['filename'];
		
		if(substr($filename,-4)=='.sql' or substr($filename,-4)=='.zip')
		{
			if(is_file(DIR_FS_BACKUPS . $filename))
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
		
		redirect_to('users/login','action=logoff');
		break;
}

$app_layout = 'public_layout.php';