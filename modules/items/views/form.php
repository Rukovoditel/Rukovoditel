<div class="items-form-conteiner">
<?php 

  $header_menu_button = ''; 
  
  //add templates menu in header
  if(class_exists('entities_templates'))
  {
    $header_menu_button = entities_templates::render_modal_header_menu($current_entity_id);
  }
  
  echo ajax_modal_template_header($header_menu_button . (strlen($entity_cfg['window_heading'])>0 ? $entity_cfg['window_heading'] : TEXT_INFO));
  
  $is_new_item = (!isset($_GET['id']) ? true:false);
  
  $app_items_form_name = (isset($_GET['is_submodal']) ? 'sub_items_form':'items_form');
?>

<?php echo form_tag($app_items_form_name, url_for('items/','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('enctype'=>'multipart/form-data','class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
     
<?php echo input_hidden_tag('path',$_GET['path']) ?>
<?php echo input_hidden_tag('redirect_to',$app_redirect_to) ?>
<?php if(isset($_GET['related'])) echo input_hidden_tag('related',$_GET['related']) ?>
<?php if(isset($_GET['gotopage'])) echo input_hidden_tag('gotopage[' . key($_GET['gotopage']). ']',current($_GET['gotopage'])) ?>

<?php    
  $html_user_password ='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="password"><span class="required-label">*</span>' . TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
            <div class="col-md-9">	
          	  ' . input_password_tag('password',array('class'=>'form-control input-medium','autocomplete'=>'off')) . '
              ' . tooltip_text(TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
            </div>			
          </div>        
        ';   


  $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
  
  //check fields access rules for item
  if(isset($_GET['id']))
  {
  	$access_rules = new access_rules($current_entity_id, $obj);
  	$fields_access_schema += $access_rules->get_fields_view_only_access();
  }
      
  $count_tabs = db_count('app_forms_tabs',$current_entity_id,"entities_id");
  
  if($count_tabs>1)
  {        
    //put tabs heading html in array
    $html_tab = array();
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
      $html_tab[$tabs['id']] = '<li class="form_tab_' . $tabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';      
    }
              
    $count_tabs = 0;
    
    //put tags content html in array    
    $html_tab_content = array();
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
              
      $html_tab_content[$tabs['id']] = '
        <div class="tab-pane fade" id="form_tab_' . $tabs['id'] . '">
      ' . (strlen($tabs['description']) ? '<p>' . $tabs['description'] . '</p>' : '');
                  
      $count_fields = 0;
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form() . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
      while($v = db_fetch_array($fields_query))
      {
        //check field access
        if(isset($fields_access_schema[$v['id']])) continue;
        
        //handle params from GET
        if(isset($_GET['fields'][$v['id']])) $obj['field_' . $v['id']] = db_prepare_input($_GET['fields'][$v['id']]);
        
        if($v['type']=='fieldtype_section')
        {
        	$html_tab_content[$tabs['id']] .= '<div class="form-group-' . $v['id'] . '">' . fields_types::render($v['type'],$v,$obj,array('count_fields'=>$count_fields)) . '</div>';
        }
        elseif($v['type']=='fieldtype_dropdown_multilevel')
        {
        	$html_tab_content[$tabs['id']] .= fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item));
        }
        else
        {        
        	$v['is_required'] = (in_array($v['type'],array('fieldtype_user_firstname','fieldtype_user_lastname','fieldtype_user_username','fieldtype_user_email')) ?  1 : $v['is_required']);
        	
	        $html_tab_content[$tabs['id']] .='
	          <div class="form-group form-group-' . $v['id'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . 
	              ($v['is_required']==1 ? '<span class="required-label">*</span>':'') .
	              ($v['tooltip_display_as']=='icon' ? tooltip_icon($v['tooltip']) :'') .
	              fields_types::get_option($v['type'],'name',$v['name']) . 
	            '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item)) . '</div>
	              ' . ($v['tooltip_display_as']!='icon' ? tooltip_text($v['tooltip']):'') . '
	            </div>			
	          </div>        
	        '; 
        }
        
        //including user password field for new user form
        if($v['type']=='fieldtype_user_username' and !isset($_GET['id']))
        {
          $html_tab_content[$tabs['id']] .= $html_user_password;
        }
        
        $count_fields++;     
      }
      
      $html_tab_content[$tabs['id']] .= '</div>';
      
      //if there is no fields for this tab then remove content from array
      if($count_fields==0)
      {
        unset($html_tab_content[$tabs['id']]);
      }
      
      $count_tabs++;
    }
        
    
    $html = '<ul class="nav nav-tabs" id="form_tabs">';
    
    //build tabs heading and skip tabs with no fields
    $count = 0;
    foreach($html_tab_content as $tab_id=>$content)
    {
      $html .= ($count==0 ? str_replace('class="form_tab_' . $tab_id,'class="form_tab_' . $tab_id . ' active',$html_tab[$tab_id]) : $html_tab[$tab_id]);
      $count++;
    }
    
    $html .= '</ul>';
    
    $html .= '<div class="tab-content">';
    
    //build tabs content
    $count = 0;
    foreach($html_tab_content as $tab_id=>$content)
    {
      $html .= ($count==0 ? str_replace('tab-pane fade','tab-pane fade active in',$content) : $content); 
      $count++;
    }
    
    $html .= '</div>';
  
  }
  else
  {  
  	$count_fields = 0;
    $html = '';
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    $tabs = db_fetch_array($tabs_query);
    
    if(strlen($tabs['description']))
    {
    	$html .= '<p>' . $tabs['description'] . '</p>';
    }
    
    
    $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(). ") and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {       
      //check field access
      if(isset($fields_access_schema[$v['id']])) continue;
      
      //handle params from GET
      if(isset($_GET['fields'][$v['id']])) $obj['field_' . $v['id']] = db_prepare_input($_GET['fields'][$v['id']]);
      
      if($v['type']=='fieldtype_section')
      {
      	$html .= '<div class="form-group-' . $v['id'] . '">' . fields_types::render($v['type'],$v,$obj,array('count_fields'=>$count_fields)) . '</div>';
      }
      elseif($v['type']=='fieldtype_dropdown_multilevel')
      {
      	$html .= fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item));
      }
      else
      {      
      	$v['is_required'] = (in_array($v['type'],array('fieldtype_user_firstname','fieldtype_user_lastname','fieldtype_user_username','fieldtype_user_email')) ?  1 : $v['is_required']);
      	
	      $html .='
	          <div class="form-group form-group-' . $v['id'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' .                
	              ($v['is_required']==1 ? '<span class="required-label">*</span>':'') .
	              ($v['tooltip_display_as']=='icon' ? tooltip_icon($v['tooltip']) :'') . 
	              fields_types::get_option($v['type'],'name',$v['name']) .               
	            '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $v['id'] . '_rendered_value">' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item)) . '</div>
	              ' . ($v['tooltip_display_as']!='icon' ? tooltip_text($v['tooltip']):'') . '
	            </div>			
	          </div>        
	        ';  
      }
        
      //including user password field for new user form
      if($v['type']=='fieldtype_user_username' and !isset($_GET['id']))
      {
        $html .= $html_user_password;
      } 
      
      $count_fields++;
    }
    
  }
  
  echo $html;
  
  
  //render templates fields values
  if(class_exists('entities_templates'))
  {
    echo entities_templates::render_fields_values($current_entity_id);
  }
?>
 </div>
</div>
 
<?php 
	$extra_button = '';
	
	//prepare back button for sub-modal
	if(isset($_GET['is_submodal']))
	{
		$extra_button = '<button type="button" class="btn btn-default btn-submodal-back"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK. '</button>';
	}
	
	//prepare delete button for gantt report
	require(component_path('items/items_form_gantt_delete_prepare'));
		
	echo ajax_modal_template_footer(false,$extra_button); 
	
	//check ruels for hidden fields by access
	if(isset($_GET['id']))
	{
		echo forms_fields_rules::prepare_hidden_fields($current_entity_id, $obj, $fields_access_schema);
	}
?>
 
</form> 
</div> 

<?php 
	if(is_ext_installed())
	{
		$smart_input = new smart_input($current_entity_id);
		echo $smart_input->render();
	}
?>

<?php require(component_path('items/items_form.js')); ?> 