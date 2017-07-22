<?php

switch($app_module_action)
{
  case 'sort':
      if(isset($_POST['sort_items']))
      {
        $sort_order = 0;
        foreach(explode(',',$_POST['sort_items']) as $v)
        {
          db_query("update app_entities_menu set sort_order='" . $sort_order . "' where id='" . str_replace('item_','',$v). "'");
          
          $sort_order++;
        }
      }
      exit();
    break;
  case 'sort_items':
    	if(isset($_POST['sort_items']))
    	{
    		db_query("update app_entities_menu set entities_list='" . str_replace('item_','',$_POST['sort_items']) . "' where id='" . db_input($_GET['id']). "'",true);    	
    	}
    	exit();
    	break;
  case 'save':
    $sql_data = array('name' => db_prepare_input($_POST['name']),
    									'icon' => db_prepare_input($_POST['icon']),
    									'entities_list' => (isset($_POST['entities_list']) ? implode(',',$_POST['entities_list']) : ''),
                      'sort_order'=>db_prepare_input($_POST['sort_order']),                                              
                      );
    
    
    if(isset($_GET['id']))
    {        
      db_perform('app_entities_menu',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
    }
    else
    {               
      db_perform('app_entities_menu',$sql_data);                  
    }
        
    redirect_to('entities/menu');      
  break;
  case 'delete':
      if(isset($_GET['id']))
      {     
      	$obj = db_find('app_entities_menu',$_GET['id']);
                 
        db_delete_row('app_entities_menu',$_GET['id']);        
                              
        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$obj['name']),'success');
                        
        redirect_to('entities/menu');  
      }
    break;   
}