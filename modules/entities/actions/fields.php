<?php

switch($app_module_action)
{
  case 'set_heading_field_id':
       //reset heading
       db_query("update app_fields set is_heading=0 where entities_id ='" . db_input($_GET['entities_id']) . "'");
       
       //set new heading
       db_query("update app_fields set is_heading=1 where id='" . $_POST['heading_field_id'] . "' and entities_id ='" . db_input($_GET['entities_id']) . "'");
       
       exit(); 
    break;
  case 'set_number_fixed_field_in_listing':
      entities::set_cfg('number_fixed_field_in_listing',$_POST['number_fields'],$_GET['entities_id']);
      exit(); 
    break;
  case 'sort_fields':
        if(isset($_POST['fields_in_listing'])) 
        {
          $sort_order = 0;
          foreach(explode(',',$_POST['fields_in_listing']) as $v)
          {
            $sql_data = array('listing_status'=>1,'listing_sort_order'=>$sort_order);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('form_fields_','',$v)) . "'");
            $sort_order++;
          }
        }
        
        if(isset($_POST['fields_excluded_from_listing'])) 
        {          
          foreach(explode(',',$_POST['fields_excluded_from_listing']) as $v)
          {
            $sql_data = array('listing_status'=>0,'listing_sort_order'=>0);
            db_perform('app_fields',$sql_data,'update',"id='" . db_input(str_replace('form_fields_','',$v)) . "'");            
          }
        }
      exit();
    break;
  case 'save':
      $sql_data = array('forms_tabs_id'=>$_POST['forms_tabs_id'],
                        'name'=>$_POST['name'],                        
                        'type'=>$_POST['type'],
                        'short_name'=>$_POST['short_name'],
                        'notes' => strip_tags($_POST['notes']),
                        'is_heading'=>(isset($_POST['is_heading']) ? $_POST['is_heading']:0),
                        'is_required'=>(isset($_POST['is_required']) ? $_POST['is_required']:0),
                        'required_message'=>$_POST['required_message'],
                        'tooltip'=>$_POST['tooltip'],
                        'tooltip_display_as'=>(isset($_POST['tooltip_display_as']) ? $_POST['tooltip_display_as']:''),
                        'configuration'=> (isset($_POST['fields_configuration']) ? fields_types::prepare_configuration($_POST['fields_configuration']):''),        
                        'entities_id'=>$_POST['entities_id']);
                        
      
      //reset heading fields, only one field can be heading                  
      if(isset($_POST['is_heading']))
      {
        db_query("update app_fields set is_heading=0 where entities_id ='" . db_input($_POST['entities_id']) . "'");
      }                   
      
      if(isset($_GET['id']))
      {        
        //check if field type changed and do action required when field type changed
        fields::check_if_type_changed($_GET['id'],$_POST['type']);
        
        db_perform('app_fields',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
        
        $fields_id = $_GET['id'];
      }
      else
      {     
        $sql_data['sort_order'] = (fields::get_last_sort_number($_POST['forms_tabs_id'])+1);
                  
        db_perform('app_fields',$sql_data);
        $fields_id = db_insert_id();
        
        entities::prepare_field($_POST['entities_id'],$fields_id,$_POST['type']);                
      }
      
      //create app_related_items_#_# table
      related_records::prepare_entities_related_items_table($_POST['entities_id'], $fields_id);
      
      if(isset($_POST['redirect_to']))
      {
        switch($_POST['redirect_to'])
        {
          case 'forms':
              redirect_to('entities/forms','entities_id=' . $_POST['entities_id']);
            break;
        }
      }
      
      redirect_to('entities/fields','entities_id=' . $_POST['entities_id']);      
    break;
  case 'delete':
      if(isset($_GET['id']))
      {      
        $msg = fields::check_before_delete($_GET['id']);
        
        if(strlen($msg)>0)
        {
          $alerts->add($msg,'error');
        }
        else
        {
          $name = fields::get_name_by_id($_GET['id']);
          
          db_delete_row('app_fields',$_GET['id']);
          
          db_delete_row('app_reports_filters',$_GET['id'],'fields_id');
          
          choices_values::delete_by_field_id($_GET['entities_id'],$_GET['id']);
                              
          entities::delete_field($_GET['entities_id'],$_GET['id']);
          
          db_query("delete from app_reports_filters_templates where fields_id='" . db_input($_GET['id']) ."'");
          
          $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
        }
        
        if(isset($_POST['redirect_to']))
        {
          switch($_POST['redirect_to'])
          {
            case 'forms':
                redirect_to('entities/forms','entities_id=' . $_GET['entities_id']);
              break;
          }
        }
        
        redirect_to('entities/fields','entities_id=' . $_GET['entities_id']);  
      }
    break;    
  case 'get_entities_form_tabs':
      $choices = forms_tabs::get_choices($_POST['entities_id']);
      
      if(count($choices)==1)
      {
        $html = input_hidden_tag('copy_to_form_tabs_id',key($choices));
      }
      else
      {
        $html = '
         <div class="form-group">
          	<label class="col-md-4 control-label" for="type">' . TEXT_SELECT_FORM_TAB . '</label>
            <div class="col-md-8">	
          	  ' . select_tag('copy_to_form_tabs_id',$choices,'',array('class'=>'form-control')) . '        
            </div>			
          </div>        
        ';
      }
      
      echo $html;
      
      exit();
    break;  
  case 'copy_selected':  
    if(strlen($_POST['selected_fields'])>0 and $_POST['copy_to_entities_id']>0)
    {
            
      $fields_query = db_query("select * from app_fields where entities_id='" . $_GET['entities_id'] . "' and id in (" . $_POST['selected_fields'] . ")");
      while($fields = db_fetch_array($fields_query))
      {
        //prepare sql data
        $sql_data = $fields;
        unset($sql_data['id']);
        $sql_data['entities_id'] = $_POST['copy_to_entities_id'];
        $sql_data['forms_tabs_id'] = $_POST['copy_to_form_tabs_id'];        
        $sql_data['is_heading'] = 0;
         
        db_perform('app_fields',$sql_data);        
        $new_fields_id = db_insert_id();
        
        entities::prepare_field($_POST['copy_to_entities_id'],$new_fields_id);
        
        //create app_related_items_#_# table
        related_records::prepare_entities_related_items_table($_POST['copy_to_entities_id'], $new_fields_id);
                      
        $choices_parent_id_to_replace = array();
        
        //check fields choices
        $fields_choices_query = db_query("select * from app_fields_choices where fields_id='" . $fields['id'] . "'");
        while($fields_choices = db_fetch_array($fields_choices_query))
        {
          //prepare sql data
          $sql_data = $fields_choices;
          unset($sql_data['id']);
          $sql_data['fields_id'] = $new_fields_id;
          
          db_perform('app_fields_choices',$sql_data);
          $new_fields_choices_id = db_insert_id();
          
          $choices_parent_id_to_replace[$fields_choices['id']] = $new_fields_choices_id;
        }                
        
        foreach($choices_parent_id_to_replace as $from_id=>$to_id)
        {
          db_query("update app_fields_choices set parent_id='" . $to_id . "' where parent_id='" . $from_id . "' and fields_id='" . $new_fields_id . "'");
        }
      }
      
      $alerts->add(TEXT_FIELDS_COPY_SUCCESS,'success');      
    } 
    
    redirect_to('entities/fields','entities_id=' . $_POST['copy_to_entities_id']);
    
    break;
}


require(component_path('entities/check_entities_id'));  