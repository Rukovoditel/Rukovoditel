<?php

$entity_info = db_find('app_entities',$_POST['entities_id']);

$import_to = $entity_info['name']; 

if($entity_info['parent_id']>0)
{
  $parent_entity_info = db_find('app_entities',$entity_info['parent_id']);
  
  $parent_item_query = db_query("select * from app_entity_" . $entity_info['parent_id'] . " where id='" . db_input($_POST['parent_item_id']) . "'");
  
  if($parent_item = db_fetch_array($parent_item_query))
  {
    $path_info = items::get_path_info($entity_info['parent_id'],$parent_item['id']);
    
    $app_breadcrumb = items::get_breadcrumb(explode('/',$path_info['full_path']));
    
    $to = array();
    foreach($app_breadcrumb as $v)
    {
      $to[] = $v['title'];
    }
    
    $import_to = implode(' &rsaquo; ' , $to) . ' &rsaquo; ' . $import_to; 
  }
  else
  {
    $alerts->add(sprintf(TEXT_PARENT_ITEM_ID_NOT_FOUND,$_POST['parent_item_id'],$parent_entity_info['name']),'warning');
    redirect_to('tools/import_data');
  }
}



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
                
    for ($row = 1; $row <= $highestRow; ++$row) 
    {    
      $is_empty_row = true;  
      $worksheet_cols = array();
          
      for ($col = 0; $col <= $highestColumnIndex; ++$col) 
      {        
        $value = trim($objWorksheet->getCellByColumnAndRow($col, $row)->getValue());  
        $worksheet_cols[$col] = $value;
        
        if(strlen($value)>0) $is_empty_row = false;
      } 
                  
      if(!$is_empty_row)
      {
        $worksheet[] = $worksheet_cols;
      }       
    }    
  }
  else
  {
    $alerts->add(TEXT_FILE_NOT_LOADED,'warning');
    redirect_to('tools/import_data');
  }                           
}
