<?php

$list_info_query = db_query("select * from app_global_lists where id='" . $_GET['lists_id']. "'");
if(!$list_info = db_fetch_array($list_info_query))
{
  redirect_to('global_lists/lists');
}

switch($app_module_action)
{
  case 'save':
      $sql_data = array('lists_id'=>$_GET['lists_id'],
                        'parent_id'=>(strlen($_POST['parent_id'])==0 ? 0 : $_POST['parent_id']),
                        'name'=>$_POST['name'],                                                
                        'is_default'=>(isset($_POST['is_default']) ? $_POST['is_default']:0),
                        'bg_color'=>$_POST['bg_color'],                        
                        'sort_order'=>$_POST['sort_order'],
                        );
                                                                              
      if(isset($_POST['is_default']))
      {
        db_query("update app_global_lists_choices set is_default = 0 where lists_id = '" . db_input($_GET['lists_id']). "'");
      }                        
      
      if(isset($_GET['id']))
      {    
      	//paretn can't be the same as record id
      	if($_POST['parent_id']==$_GET['id'])
      	{
      		$sql_data['parent_id'] = 0;
      	}
      	
        db_perform('app_global_lists_choices',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
      }
      else
      {               
        db_perform('app_global_lists_choices',$sql_data);
      }
      
      redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);      
    break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = global_lists::check_before_delete_choices($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = global_lists::get_choices_name_by_id($_GET['id']);
          
          $tree = global_lists::get_choices_tree($_GET['lists_id'],$_GET['id']);
          
          foreach($tree as $v)
          {
            db_delete_row('app_global_lists_choices',$v['id']);
          }
          
          db_delete_row('app_global_lists_choices',$_GET['id']);
          
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
        
        redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);  
      }
    break; 
    
  case 'multiple_edit':
    	 
   	if(strlen($_POST['selected_fields']))
   	{
   		foreach(explode(',',$_POST['selected_fields']) as $id)
   		{	
   			$sql_data = array();
   			
   			if($_POST['parent_id']>=0 and $_POST['parent_id']!=$id)
   			{
   				$sql_data['parent_id'] = $_POST['parent_id'];
   			}	
   			
   			if(strlen($_POST['bg_color']))
   			{
   				$sql_data['bg_color'] = trim($_POST['bg_color']);
   			}
   			
   			if(count($sql_data))
   			{
   				db_perform('app_global_lists_choices',$sql_data,'update',"id='" . db_input($id) . "'");
   			}
   		}
   	}
   	
   	redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);
   	break;
  case 'multiple_delete':
  	
  	if(strlen($_POST['selected_fields']))
  	{  		  	
  		db_query("delete from app_global_lists_choices where lists_id='" . _get::int('lists_id'). "' and id in (" . $_POST['selected_fields'] . ")");
  		
  		
  		
  		//check paretns
  		$reset_parents_id = array();
  		$choices_query = db_query("select * from app_global_lists_choices c where (select count(*) from  app_global_lists_choices c2 where c2.id=c.parent_id)=0");
  		while($choices = db_fetch_array($choices_query))
  		{
  			$reset_parents_id[] = $choices['id'];
  		}
  		
  		if(count($reset_parents_id))
  		{
  			db_query("update app_global_lists_choices set parent_id=0 where id in (" . implode(',',$reset_parents_id) . ")");
  		}
  	}
  	
  	redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);
  	break;
  case 'sort':
      $choices_sorted = $_POST['choices_sorted'];
      
      if(strlen($choices_sorted)>0)
      {
        $choices_sorted = json_decode($choices_sorted,true);
        
        //echo '<pre>';
        //print_r($choices_sorted);
        
        global_lists::choices_sort_tree($_GET['lists_id'],$choices_sorted);
      }
            
      redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);
    break;
  case 'import':
  	
  	
  	$worksheet = array();
  	
  	if(strlen($filename = $_FILES['filename']['name'])>0)
  	{
  		//rename file (issue with HTML.php:495 if file have UTF symbols)
  		$filename  = 'import_data.' . (strstr($filename,'.xls') ?  'xls' : 'xlsx');
  	
  		if(move_uploaded_file($_FILES['filename']['tmp_name'], DIR_WS_UPLOADS  . $filename))
  		{
  			require('includes/libs/PHPExcel/PHPExcel/IOFactory.php');
  	
  			$objPHPExcel = PHPExcel_IOFactory::load(DIR_WS_UPLOADS  . $filename);
  	
  			unlink(DIR_WS_UPLOADS  . $filename);
  	
  			$objWorksheet = $objPHPExcel->getActiveSheet();
  	
  			$highestRow = $objWorksheet->getHighestRow(); // e.g. 10
  			$highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
  	
  			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5
  			
  			$import_columns = _post::int('import_columns');
  			  			  			
  			$first_row = (isset($_POST['import_first_row']) ? 1:2);
  			
  			$sort_order = 0;
  			
  			$parent_id[0] = 0;
  			$check_parent_name[0] = '';
  			  			  		
  			for ($row = $first_row; $row <= $highestRow; ++$row)
  			{ 
  				$col = 0;
  				
  				for($col; $col<$import_columns;$col++)
  				{
  					$value = trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());
  				
	  				if(strlen($value) and $check_parent_name[$col]!=$value)
	  				{
				
	  					$sql_data = array(
	  							'lists_id'=>$_GET['lists_id'],
	  							'parent_id'=>$parent_id[$col],
	  							'name'=>$value,
	  							'is_default'=>0,
	  							'bg_color'=>'',
	  							'sort_order'=>(isset($_POST['sort_like_file']) ? $sort_order : 0),
	  					);
	  								  					  			
	  					db_perform('app_global_lists_choices',$sql_data);
	  					$id = db_insert_id();
	  					$parent_id[($col+1)] = $id;
	  					
	  					if($check_parent_name[$col]!=$value)
	  					{
	  						$check_parent_name[$col]=$value;
	  					}
	  				}  				  				  					
  				}
  				
  				$sort_order++;
  			}
  			  			
  		}
  		else
  		{
  			$alerts->add(TEXT_FILE_NOT_LOADED,'warning');  			
  		}
  	}
  	
  	redirect_to('global_lists/choices', 'lists_id=' . $_GET['lists_id']);
  	break;
}

