
<?php echo ajax_modal_template_header(TEXT_RULE_FOR_FIELD) ?>

<?php echo form_tag('rules_form', url_for('access_rules/rules','action=save&entities_id=' . $_GET['entities_id'] . '&fields_id=' . _get::int('fields_id') . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>
<div class="modal-body">
  <div class="form-body ajax-modal-width-790">
  
  
<?php 
$fields_id = _get::int('fields_id');

$field_info = db_find('app_fields', $fields_id);

$cfg = new fields_types_cfg($field_info['configuration']);

$choices = array();
$tree = ($cfg->get('use_global_list')>0 ? global_lists::get_choices_tree($cfg->get('use_global_list')) : fields_choices::get_tree($fields_id));
foreach($tree as $v)
{	
	$choices[$v['id']] = $v['name'];	
}

?>

  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_SELECT_FIELD_VALUES ?></label>
    <div class="col-md-8">	
  	  <?php echo select_tag('choices[]',$choices,$obj['choices'],array('class'=>'form-control input-xlarge chosen-select required','multiple'=>'multiple')) ?>  	  
    </div>			
  </div>  
  
<?php 
$choices = array();
$groups_query = db_query("select ag.* from app_access_groups ag where ag.id in (select ea.access_groups_id from app_entities_access ea where ea.entities_id='" . _get::int('entities_id'). "' and length(ea.access_schema)>0) order by ag.sort_order, ag.name"); ;
while($v = db_fetch_array($groups_query))
{	
	$choices[$v['id']] = $v['name'];	
}
?>  
	<div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_USERS_GROUPS ?></label>
    <div class="col-md-8">	
  	  <?php echo select_tag('users_groups[]',$choices,$obj['users_groups'],array('class'=>'form-control input-xlarge chosen-select required','multiple'=>'multiple')) ?>  	  
    </div>			
  </div>
  
<?php 
$choices = array();

$choices = array(		
		'update' => TEXT_UPDATE_ACCESS,
		'delete' => TEXT_DELETE_ACCESS,
		'export' => TEXT_EXPORT_ACCESS,
);

//extra access available in extension
if(is_ext_installed())
{
	$choices += array(			
			'copy' => TEXT_COPY_RECORDS,
			'move' => TEXT_MOVE_RECORDS,
	);
}

?>
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo tooltip_icon(TEXT_ACCESS_RULES_SELECT_ACCESS) . TEXT_ACCESS ?></label>
    <div class="col-md-8">	
  	  <?php echo select_tag('access_schema[]',$choices,$obj['access_schema'],array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')) ?>  	  
    </div>			
  </div>
  
<?php 

$choices = array();

$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list() . ") and f.entities_id='" . $_GET['entities_id'] . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
while($fields = db_fetch_array($fields_query))
{
	$choices[$fields['id']] = $fields['name'];
}
?> 

  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo tooltip_icon(TEXT_ACCESS_RULES_FIELDS_VIEW_ONLY_ACCESS) . TEXT_VIEW_ONLY ?></label>
    <div class="col-md-8">	
  	  <?php echo select_tag('fields_view_only_access[]',$choices,$obj['fields_view_only_access'],array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')) ?>  	  
    </div>			
  </div> 
  

<?php 
$choices = array('-1'=>'');
$choices += comments::get_access_choices();

$choices = array(
		'false'=>'',
		'no'=>TEXT_NO,
		'view_create_update_delete'=>TEXT_YES,
		'view_create'=>TEXT_CREATE_ONLY_ACCESS,
		'view'=>TEXT_VIEW_ONLY_ACCESS);

?>
  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo tooltip_icon(TEXT_USE_DEFAULT_IF_NOT_SELECTED) . TEXT_NAV_COMMENTS_ACCESS ?></label>
    <div class="col-md-8">	
  	  <?php echo select_tag('comments_access_schema',$choices,str_replace(',','_',$obj['comments_access_schema']),array('class'=>'form-control input-large')) ?>  	  
    </div>			
  </div>   
  
      
   </div>
</div>

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>

$(function() {
	$('#rules_form').validate({ignore:'',
		submitHandler: function(form){
			app_prepare_modal_action_loading(form)
			form.submit();
		}
  });                                                                        
});  
  
</script>   
    
 
