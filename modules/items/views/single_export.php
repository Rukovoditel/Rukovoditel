<?php echo ajax_modal_template_header(TEXT_HEADING_EXPORT) ?>



<div class="modal-body">  

<ul class="nav nav-tabs" id="items_export_tabs">
  <li class="active"><a href="#select_fields_tab"  data-toggle="tab"><?php echo TEXT_SELECT_FIELD_TO_EXPORT ?></a></li>
  <li><a href="#my_templates_tab"  data-toggle="tab"><?php echo TEXT_MY_TEMPLATES ?></a></li>   
</ul>



<div class="tab-content">
  <div class="tab-pane fade active in" id="select_fields_tab">

<?php echo form_tag('export-form', url_for('items/single_export','action=export&path=' . $_GET['path'])) ?>
	
	<div id="items_export_templates_button"></div>
	<div id="items_export_templates_selected" style="display:none">
		<br>
		<div class="alert alert-info">
			<span id="items_export_templates_selected_data"></span>
			<div style="float: right"><a title="<?php echo addslashes(TEXT_UPDATE_SELECTED_TEMPLATE_INFO)?>" href="javascript: update_items_export_templates()"><i class="fa fa-refresh" aria-hidden="true" ></i> <?php echo TEXT_BUTTON_UPDATE ?></a></div>
		</div>
	</div>

<p>
<?php


$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);

$tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($current_entity_id) . "' order by  sort_order, name");
while($tabs = db_fetch_array($tabs_query))
{
  $fileds_html = '';
  
  $fields_query = db_query("select f.* from app_fields f where  f.type not in ('fieldtype_action') and f.entities_id='" . db_input($current_entity_id) . "' and forms_tabs_id='" . db_input($tabs['id']) . "' order by f.sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {      
    //check field access
    if(isset($fields_access_schema[$v['id']]))
    {
      if($fields_access_schema[$v['id']]=='hide') continue;
    }
            
    $fileds_html .= '<div><label>' . input_checkbox_tag('fields[]',$v['id'],array('id'=>'fields_' . $v['id'],'class'=>'export_fields export_fields_' . $v['id'] . ' fields_tabs_' . $tabs['id'],'checked'=>'checked')) . ' ' . fields_types::get_option($v['type'],'name',$v['name']) . '</label></div>'; 
  }
  
  if(strlen($fileds_html)>0)
  {
    echo '<p><div><label><b>' . input_checkbox_tag('all_tab_fields_' . $tabs['id'],'',array('checked'=>'checked','onChange'=>'select_all_by_classname(\'all_tab_fields_' . $tabs['id'] . '\',\'fields_tabs_' . $tabs['id'] . '\')')) . $tabs['name']. '</b></label></div>' . $fileds_html . '</p>';
  }
} 

  
?>
</p>

  
<?php if(users::has_comments_access('view') and $entity_cfg['use_comments']==1): ?>  
  <p><label><?php echo input_checkbox_tag('export_comments','comments',array('class'=>'export_fields export_fields_comments')) . ' ' . TEXT_EXPORT_COMMENTS ?></label></p>
<?php endif ?>  

  <p><?php echo TEXT_FILENAME . '<br>' . input_tag('filename',items::get_heading_field($current_entity_id,$current_item_id) ,array('class'=>'form-control input-medium')) ?></p>
  
  <br>
	
	<div>
		<?php 
		
		echo '
			<button type="button" class="btn btn-primary btn-template-export"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-primary btn-template-export-word"><i class="fa fa-file-word-o" aria-hidden="true"></i></button>
			<button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' .  TEXT_PRINT . '</button>'
  		. input_hidden_tag('export_type','pdf');
		?>		
	</div>

</form> 


<script>
  $('.btn-template-export').click(function(){
    $('#export_type').val('pdf');
    $('#export-form').attr('target','_self')
    $('#export-form').submit();
  })
  
   $('.btn-template-export-word').click(function(){
    $('#export_type').val('word');
    $('#export-form').attr('target','_self')
    $('#export-form').submit();
  })
  
  $('.btn-template-print').click(function(){
    $('#export_type').val('print');
    $('#export-form').attr('target','_new')
    $('#export-form').submit();
  })
</script>
 
  
    </div>
  <div class="tab-pane fade" id="my_templates_tab">
		
		<?php echo form_tag('export_templates_form', url_for('items/export','action=save_templates&path=' . $_GET['path']),array('class'=>'form-inline')) ?>
		<?php echo TEXT_ADD_NEW_TEMPLATE ?>
			<div class="row">
				<div class="col-md-7">					
					<?php echo input_tag('templates_name','',array('class'=>'form-control required','placeholder'=>TEXT_ENTER_TEMPLATE_NAME)) ?>
					<?php echo input_hidden_tag('export_fields_list') ?>				
				</div>
				<div class="col-md-5">				
					<?php echo submit_tag(TEXT_BUTTON_ADD) ?>
				</div>
			</div>
		</form>
		<p><?php echo tooltip_text(TEXT_SAVE_TAMPLATE_NOTE)?></p>
		<div id="action_response_msg"></div>
		<br>
			
		<div id="items_export_templates"></div>	
		 
  </div>
</div>
  

</div>
 
<?php echo ajax_modal_template_footer('hide-save-button') ?>

<?php require(component_path('items/export.js')); ?>

  