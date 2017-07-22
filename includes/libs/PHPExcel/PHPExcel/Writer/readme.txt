in file Excel2007.php line 197
  $pFilename = @tempnam(PHPExcel_Shared_File::sys_get_temp_dir(), 'phpxltmp');
was replaced by 
  $pFilename = @tempnam(DIR_FS_TMP, 'phpxltmp');
to use DIR_FS_TMP
