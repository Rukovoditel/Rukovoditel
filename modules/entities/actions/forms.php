<?php

switch($app_module_action)
{
  case 'sort_fields':
        //print_r($_POST);
        $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name");
        while($tabs = db_fetch_array($tabs_query))
        {
          if(isset($_POST['forms_tabs_' . $tabs['id']]))
          {
            $sort_order = 0;
            foreach(explode(',',$_POST['forms_tabs_' . $tabs['id']]) as $v)
            {
              db_perform('app_fields',array('forms_tabs_id'=>$tabs['id'], 'sort_order'=>$sort_order),'update',"id='" . db_input(str_replace('form_fields_','',$v)) . "'");
              $sort_order++;
            }
          }
        }
      exit();
    break;
  case 'sort_tabs':            
      if(isset($_POST['forms_tabs_ol']))
      {
        $sort_order = 0;
        foreach(explode(',',str_replace('forms_tabs_','',$_POST['forms_tabs_ol'])) as $v)
        {
          db_perform('app_forms_tabs',array('sort_order'=>$sort_order),'update',"id='" . db_input($v) . "'");
          $sort_order++;
        }
      }      
      exit();
    break;
  case 'save_tab':
      $sql_data = array('name'=>$_POST['name'],
                        'entities_id'=>$_POST['entities_id'],
                        'description'=>$_POST['description'],
                        'sort_order'=>(forms_tabs::get_last_sort_number($_POST['entities_id'])+1)
                        );
      
      if(isset($_GET['id']))
      {        
        db_perform('app_forms_tabs',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");       
      }
      else
      {              
        db_perform('app_forms_tabs',$sql_data);
      }
      
      redirect_to('entities/forms','entities_id=' . $_POST['entities_id']);      
    break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = forms_tabs::check_before_delete($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = forms_tabs::get_name_by_id($_GET['id']);
          
          db_delete_row('app_forms_tabs',$_GET['id']);
          
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
        
      
        redirect_to('entities/forms','entities_id=' . $_GET['entities_id']);  
      }
    break;  
      
}