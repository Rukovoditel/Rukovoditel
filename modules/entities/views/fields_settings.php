<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_FIELD_SETTINGS . ': ' . $fields_info['name'] ?></h3>

<?php echo form_tag('fields_form', url_for('entities/fields_settings','action=save&fields_id=' . $_GET['fields_id'] . '&entities_id=' . $_GET['entities_id'])) ?>

<?php
  //defautl field configuration
  $cfg = fields_types::parse_configuration($fields_info['configuration']);
  
  $exclude_cfg_keys = array();
   
  //get field configuraiton by type
  switch($fields_info['type'])
  {
    case 'fieldtype_related_records':
        $exclude_cfg_keys = array('fields_in_listing','fields_in_popup');
        
        require(component_path('entities/fieldtype_related_records_settings'));
      break;
      
    case 'fieldtype_entity':
        $exclude_cfg_keys = array('fields_in_popup');
        
        require(component_path('entities/fieldtype_entity_settings'));
      break;
  }
    
  //prepare other configuration if exist
  foreach($cfg as $k=>$v)
  {
    if(!in_array($k,$exclude_cfg_keys))
    {
      echo input_hidden_tag('fields_configuration[' . $k . ']',$v);
    } 
  } 
?>

<br>
<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>





