<?php 
  $header_menu_button = ''; 
  
  //add templates menu in header
  if(class_exists('comments_templates'))
  {
    $header_menu_button = comments_templates::render_modal_header_menu($current_entity_id);
  }
  
  echo ajax_modal_template_header($header_menu_button . TEXT_COMMENT_IFNO) 
?>

<?php echo form_tag('comments_form', url_for('items/comments','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body">
  
<?php echo input_hidden_tag('path',$_GET['path']) ?>

<?php $obj = (isset($_GET['id']) ?  db_find('app_comments',$_GET['id']): db_show_columns('app_comments')) ?>

<?php 
//reply to comment
	if(isset($_GET['reply_to']))
	{
		$reply_to_obj = db_find('app_comments',$_GET['reply_to']);
		if($entity_cfg->get('use_editor_in_comments')==1)
		{
			$obj['description'] = '<blockquote>' . $reply_to_obj['description'] . '</blockquote>' . "\n";
		}
		else 
		{
			$obj['description'] = $reply_to_obj['description'] . "\n";
		}
	}
?>
      
<?php

$html_tab = array();
$html_tab_content = array();

if(!isset($_GET['id']))
{	
  $fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
 
//build default tab   
  $html_default_tab = '';
  $fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list() . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.comments_status=1 and f.comments_forms_tabs_id=0 order by f.comments_sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {       
    //check field access
    if(isset($fields_access_schema[$v['id']])) continue;
    
    //set off required option for comment form
    $v['is_required'] = 0;
    
     $html_default_tab .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . fields_types::get_option($v['type'],'name',$v['name']) . '</label>
            <div class="col-md-9">	
          	  ' . fields_types::render($v['type'],$v,array('field_' . $v['id']=>''),array('parent_entity_item_id'=>$parent_entity_item_id,'form'=>'comment')) . '
              ' . tooltip_text($v['tooltip']) . '
            </div>			
          </div>        
        ';   
  }
     
//build tabs heading 
  $html_tab[0] = '<li class="form_tab_0 active"><a data-toggle="tab" href="#form_tab_0">' . TEXT_GENERAL_INFO . '</a></li>';
  $tabs_query = db_fetch_all('app_comments_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
  while($tabs = db_fetch_array($tabs_query))
  {
  	$html_tab[$tabs['id']] = '<li class="form_tab_' . $tabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';  	
  }
    
//build tabls content
  $tabs_query = db_fetch_all('app_comments_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
  while($tabs = db_fetch_array($tabs_query))
  {
  	$html = '';
  	$fields_query = db_query("select f.* from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list() . ',' . fields_types::get_users_types_list() . ") and  f.entities_id='" . db_input($current_entity_id) . "' and f.comments_status=1 and f.comments_forms_tabs_id='" . $tabs['id'] . "' order by f.comments_sort_order, f.name");
  	while($v = db_fetch_array($fields_query))
  	{
  		//check field access
  		if(isset($fields_access_schema[$v['id']])) continue;
  	
  		//set off required option for comment form
  		$v['is_required'] = 0;
  	
  		$html .='
          <div class="form-group">
          	<label class="col-md-3 control-label" for="fields_' . $v['id']  . '">' . fields_types::get_option($v['type'],'name',$v['name']) . '</label>
            <div class="col-md-9">
          	  ' . fields_types::render($v['type'],$v,array('field_' . $v['id']=>''),array('parent_entity_item_id'=>$parent_entity_item_id,'form'=>'comment')) . '
              ' . tooltip_text($v['tooltip']) . '
            </div>
          </div>
        ';
  	}
  	
  	if(strlen($html))
  	{
  		$html_tab_content[$tabs['id']] = '<div class="tab-pane fade" id="form_tab_' . $tabs['id'] . '">' . $html . '</div>';
  	}  	  	
  }
  
  //print_r($html_tab_content);
 
//render tabs heading if tabs exists  
  if(count($html_tab_content))
  {
  	$html = '<ul class="nav nav-tabs" id="form_tabs">';
  	
  	$html .= $html_tab[0];
  	
  	//build tabs heading and skip tabs with no fields
  	foreach($html_tab_content as $tab_id=>$content)
  	{
  		$html .= $html_tab[$tab_id];
  	}
  	
  	$html .= '</ul>';
  	
  	$html .= '
  		<div class="tab-content">
  				<div class="tab-pane fade active in" id="form_tab_0">';
  	echo $html;
  	
  }
  
//output fields for default tab  
  echo $html_default_tab;
}  
?>    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo TEXT_COMMENT ?></label>
      <div class="col-md-9">	
    	  <?php echo textarea_tag('description',$obj['description'],array('class'=>'form-control autofocus ' . ($entity_cfg->get('use_editor_in_comments')==1 ? 'editor-auto-focus':''))) ?>        
      </div>			
    </div>

<?php if($entity_cfg->get('disable_attachments_in_comments')!=1): ?>    
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo TEXT_ATTACHMENTS ?></label>
      <div class="col-md-9">	
    	  <?php echo fields_types::render('fieldtype_attachments',array('id'=>'attachments'),array('field_attachments'=>$obj['attachments'])) ?>
        <?php echo input_hidden_tag('comments_attachments','',array('class'=>'form-control required_group')) ?>        
      </div>			
    </div>
<?php endif ?>    
    
<?php

//render tabs content
	if(count($html_tab_content))
	{
		$html = '</div>';
		
		//build tabs content
		foreach($html_tab_content as $tab_id=>$content)
		{
			$html .= $content;
		}
		
		$html .= '</div>';
		
		echo $html;
	}	


  //render templates fields values
  if(class_exists('comments_templates'))
  {
    echo comments_templates::render_fields_values($current_entity_id);
  }
?>    
    
 </div>
</div>
 
<?php echo ajax_modal_template_footer('hide-save-button','<button type="button" onClick="submit_comments_form()" class="btn btn-primary btn-primary-modal-action">' . addslashes(TEXT_BUTTON_SAVE). '</button>') ?>    
                  
</form> 


<?php
  //include comments form validation 
  require(component_path('items/comments_form_validation.js')) 
?> 

   