
<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo  TEXT_FORMS_FIELDS_DISPLAY_RULES ?></h3>

<p><?php echo TEXT_FORMS_FIELDS_DISPLAY_RULES_INFO ?></p>

<?php echo button_tag(TEXT_BUTTON_ADD_NEW_RULE,url_for('forms_fields_rules/rules_form','entities_id=' . $_GET['entities_id']),true) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    
    <th><?php echo TEXT_ACTION?></th>
    <th>#</th>    
    <th width="100%"><?php echo TEXT_RULE_FOR_FIELD ?></th>    
    <th><?php echo TEXT_VALUES ?></th>
    <th><?php echo TEXT_DISPLAY_FIELDS ?></th>    
    <th><?php echo TEXT_HIDE_FIELDS ?></th>    
  </tr>
</thead>
<tbody>
<?php
$form_fields_query = db_query("select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_forms_fields_rules r, app_fields f where r.fields_id=f.id and r.entities_id='" . _get::int('entities_id'). "'");

if(db_num_rows($form_fields_query)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

while($v = db_fetch_array($form_fields_query)):
?>
<tr>  
  <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('forms_fields_rules/rules_delete','id=' . $v['id'] . '&entities_id=' . $_GET['entities_id'])) . ' ' . button_icon_edit(url_for('forms_fields_rules/rules_form','id=' . $v['id']. '&entities_id=' . $_GET['entities_id'])) ?></td>
  <td><?php echo $v['id'] ?></td>
  <td><?php echo fields_types::get_option($v['type'],'name',$v['name']) ?></td>  
  <td>

<?php  
	if(strlen($v['choices']))
	{		
		if($v['type']=='fieldtype_user_accessgroups')
		{
			foreach(explode(',',$v['choices']) as $id)
			{
				echo access_groups::get_name_by_id($id) . '<br>';
			}
		}
		else
		{	
			$cfg = new fields_types_cfg($v['configuration']);
			
			if($cfg->get('use_global_list')>0)
			{
				$choices_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($cfg->get('use_global_list')). "' and id in (" . $v['choices'] . ") order by sort_order, name");
			}
			else 
			{
				$choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($v['fields_id']). "' and id in (" . $v['choices'] . ") order by sort_order, name");
			}
			
			while($choices = db_fetch_array($choices_query))
			{
				echo $choices['name'] . '<br>';
			}
		}
	}
?>

  </td>
  <td>

<?php 
	if(strlen($v['visible_fields']))
	{
		$fields_query = db_query("select * from app_fields where id in (" . $v['visible_fields'] . ")");
		while($fields = db_fetch_array($fields_query))
		{
			echo $fields['name'] . '<br>';
		}
	} 
?>

	</td>
  <td>

<?php 
	if(strlen($v['hidden_fields']))
	{
		$fields_query = db_query("select * from app_fields  where id in (" . $v['hidden_fields'] . ")");
		while($fields = db_fetch_array($fields_query))
		{
			echo $fields['name'] . '<br>';
		}
	} 
?>
  
  </td>
     
</tr>  
<?php endwhile ?>
</tbody>
</table>
</div>
