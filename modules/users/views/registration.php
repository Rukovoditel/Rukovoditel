
<h3 class="form-title"><?php echo (strlen(CFG_PUBLIC_REGISTRATION_PAGE_HEADING)>0 ? CFG_PUBLIC_REGISTRATION_PAGE_HEADING : TEXT_REGISTRATION_NEW_USER)?></h3>

<?php echo (strlen(CFG_PUBLIC_REGISTRATION_PAGE_CONTENT)>0 ? '<p>' . nl2br(CFG_PUBLIC_REGISTRATION_PAGE_CONTENT) . '</p>':'') ?>

<?php  	
  $is_new_item = true;
  $excluded_fileds_types = "'fieldtype_user_accessgroups','fieldtype_user_status','fieldtype_user_skin','fieldtype_attachments','fieldtype_users'";
?>

<?php echo form_tag('items_form', url_for('users/registration','action=save'),array('enctype'=>'multipart/form-data','class'=>'form-horizontal')) ?>
<div class="form-body">
    
<?php    
  $html_user_password ='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="password">' . TEXT_FIELDTYPE_USER_PASSWORD_TITLE . '</label>
            <div class="col-md-9">	
          	  ' . input_password_tag('password',array('class'=>'form-control input-medium','autocomplete'=>'off')) . '
              ' . tooltip_text(TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP) . '
            </div>			
          </div>        
        ';   


  $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
      
  $count_tabs = db_count('app_forms_tabs',$current_entity_id,"entities_id");
  
  if($count_tabs>1)
  {
    $count = 0;
    
    //put tabs heading html in array
    $html_tab = array();
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
      $html_tab[$tabs['id']] = '<li class="form_tab_' . $tabs['id'] . ($count==0 ? ' active':'') . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';
      $count++;
    }
              
    $count_tabs = 0;
    
    //put tags content html in array    
    $html_tab_content = array();
    
    $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
    while($tabs = db_fetch_array($tabs_query))
    {
              
      $html_tab_content[$tabs['id']] = '
        <div class="tab-pane fade ' . ($count_tabs==0 ? 'active in':'') . '" id="form_tab_' . $tabs['id'] . '">
      ';
      
      $count_fields = 0;
      $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_type_list_excluded_in_form() . "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
      while($v = db_fetch_array($fields_query))
      {
        //check field access
        if(isset($fields_access_schema[$v['id']])) continue;
        
        if(isset($_POST['fields'][$v['id']])) $obj = array('field_' . $v['id'] => $_POST['fields'][$v['id']]);
        
        $html_tab_content[$tabs['id']] .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . 
              ($v['is_required']==1 ? '<span class="required-label">*</span>':'') .
              ($v['tooltip_display_as']=='icon' ? tooltip_icon($v['tooltip']) :'') .
              fields_types::get_option($v['type'],'name',$v['name']) . 
            '</label>
            <div class="col-md-9">	
          	  ' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item)) . '
              ' . ($v['tooltip_display_as']!='icon' ? tooltip_text($v['tooltip']):'') . '
            </div>			
          </div>        
        ';   
        
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
    foreach($html_tab_content as $tab_id=>$content)
    {
      $html .= $html_tab[$tab_id];   
    }
    
    $html .= '</ul>';
    
    $html .= '<div class="tab-content">';
    
    //build tabs content
    foreach($html_tab_content as $tab_id=>$content)
    {
      $html .= $content;   
    }
    
    $html .= '</div>';
  
  }
  else
  {  
    $html = '';
    $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_type_list_excluded_in_form(). "," . $excluded_fileds_types . ") and  f.entities_id='" . db_input($current_entity_id) . "' order by f.sort_order, f.name");
    while($v = db_fetch_array($fields_query))
    {       
      //check field access
      if(isset($fields_access_schema[$v['id']])) continue;
      
      if(isset($_POST['fields'][$v['id']])) $obj = array('field_' . $v['id'] => $_POST['fields'][$v['id']]);
        
      $html .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' .                
              ($v['is_required']==1 ? '<span class="required-label">*</span>':'') .
              ($v['tooltip_display_as']=='icon' ? tooltip_icon($v['tooltip']) :'') . 
              fields_types::get_option($v['type'],'name',$v['name']) .               
            '</label>
            <div class="col-md-9">	
          	  ' . fields_types::render($v['type'],$v,$obj,array('parent_entity_item_id'=>$parent_entity_item_id, 'form'=>'item', 'is_new_item'=>$is_new_item)) . '
              ' . ($v['tooltip_display_as']!='icon' ? tooltip_text($v['tooltip']):'') . '
            </div>			
          </div>        
        ';  
        
      //including user password field for new user form
      if($v['type']=='fieldtype_user_username' and !isset($_GET['id']))
      {
        $html .= $html_user_password;
      }          
    }
            
  }
  
  echo $html;
  
?>

<?php if(app_recaptcha::is_enabled()): ?>
<div class="form-group">
	<label class="col-md-3 control-label"></label>
	<div class="col-md-9">
		<?php echo app_recaptcha::render() ?>
	</div>	
</div>
<?php endif ?>

</div>
 
<?php 

$html = '
  <div class="modal-footer">
    <div id="form-error-container"></div>    
      <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>
      <button type="submit" class="btn btn-primary btn-primary-modal-action">' .  (strlen(CFG_REGISTRATION_BUTTON_TITLE) ? CFG_REGISTRATION_BUTTON_TITLE : TEXT_BUTTON_REGISTRATCION) . '</button>
    	<a href="' . url_for('users/login'). '" class="btn btn-default">' .  TEXT_BUTTON_BACK . '</a>
  </div>';


echo $html;
?>

</form> 

<script>
	$('.is-unique').removeClass('is-unique');
</script>

<?php require(component_path('items/items_form.js')); ?>    