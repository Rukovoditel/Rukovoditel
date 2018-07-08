<?php

$html = '
	<h3 class="form-title">' . TEXT_DB_RESTORE_PROCESS . '</h3>
	<p>' . TEXT_DB_RESTORE_PROCESS_INFO . '</p>			
	<div id="db_restore_process" style="margin: 45px 0;">
		<div class="ajax-loading"></div>	
	</div>		
';

switch($app_module_action)
{
	case 'restore_by_id':
		$html .= '
			<script>
				$(function(){
					$("#db_restore_process").load("' . url_for('tools/db_restore_process','action=restore&id=' . $_GET['id']) . '")
				})				
			</script>
		';
		break;
	case 'restore_from_file';
	$is_file = false;
	if(strlen($filename = $_FILES['filename']['name'])>0)
	{
		if(substr($filename,-4)=='.sql' or substr($filename,-4)=='.zip')
		{
			if(move_uploaded_file($_FILES['filename']['tmp_name'], DIR_FS_BACKUPS . $filename))
			{
				$is_file = true;
				
				$html .= '
					<script>
						$(function(){
							$("#db_restore_process").load("' . url_for('tools/db_restore_process','action=restore_file') . '",{filename:"' . $filename . '"})
						})
					</script>
				';
			}
		}
	}
	
	if(!$is_file)
	{
		$html = '<div class="alert alert-danger">' . TEXT_FILE_NOT_FOUD . '</div>';
	}
	
	break;
}

echo $html;