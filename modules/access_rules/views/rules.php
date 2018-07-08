
<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo  sprintf(TEXT_ACCESS_RULES_FOR_FIELD,$field_info['name']) ?></h3>

<p><?php echo TEXT_ACCESS_RULES_FOR_FIELD_INFO ?></p>

<?php echo button_tag(TEXT_BUTTON_ADD_NEW_RULE,url_for('access_rules/rules_form','entities_id=' . $_GET['entities_id'] . '&fields_id=' . _get::int('fields_id')),true) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    
    <th><?php echo TEXT_ACTION?></th>       
    <th><?php echo TEXT_RULE_FOR_FIELD ?></th>    
    <th width="100%"><?php echo TEXT_VALUES ?></th>
    <th><?php echo TEXT_USERS_GROUPS ?></th>    
    <th><?php echo TEXT_ACCESS ?></th>    
    <th><?php echo TEXT_VIEW_ONLY ?></th>
    <th><?php echo TEXT_NAV_COMMENTS_ACCESS ?></th>
  </tr>
</thead>
<tbody>
<?php

$access_choices = access_groups::get_access_choices();

$form_fields_query = db_query("select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_access_rules r, app_fields f where r.fields_id=f.id and r.entities_id='" . _get::int('entities_id'). "'");

if(db_num_rows($form_fields_query)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

while($v = db_fetch_array($form_fields_query)):
?>
<tr>  
  <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('access_rules/rules_delete','id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']. '&fields_id=' . _get::int('fields_id'))) . ' ' . button_icon_edit(url_for('access_rules/rules_form','id=' . $v['id']. '&entities_id=' . $_GET['entities_id']. '&fields_id=' . _get::int('fields_id'))) ?></td>  
  <td><?php echo fields_types::get_option($v['type'],'name',$v['name']) ?></td>  
  <td>

<?php  
	if(strlen($v['choices']))
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
?>

  </td>
  <td>
<?php 
	if(strlen($v['users_groups']))
	{
		foreach(explode(',',$v['users_groups']) as $id)
		{
			echo access_groups::get_name_by_id($id) . '<br>';
		}
	}
?>
	</td>
	
  <td>
<?php 
  echo TEXT_VIEW_ACCESS . '<br>';
  
	if(strlen($v['access_schema']))
	{
		foreach(explode(',',$v['access_schema']) as $id)
		{
			echo (isset($access_choices[$id]) ? $access_choices[$id] . '<br>':'');
		}
	}
?>
  </td>
 <td>
<?php 
	if(strlen($v['fields_view_only_access']))
	{
		foreach(explode(',',$v['fields_view_only_access']) as $id)
		{
			echo fields::get_name_by_id($id) . '<br>';
		}
	}
?>
	</td>
	<td>
<?php 
	$access_choices = comments::get_access_choices();
	$comments_access_schema = ($v['comments_access_schema']=='no' ? '': str_replace(',','_',$v['comments_access_schema']));
	if(isset($access_choices[$comments_access_schema]))
	{		
		echo  $access_choices[$comments_access_schema];
	}
?>	
	</td>  
     
</tr>  
<?php endwhile ?>
</tbody>
</table>
</div>

<?php echo '<a class="btn btn-default" href="' . url_for('access_rules/fields','entities_id=' . _get::int('entities_id')) . '">' . TEXT_BUTTON_BACK . '</a>';?>
