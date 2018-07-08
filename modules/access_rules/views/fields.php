
<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo  TEXT_ACCESS_ALLOCATION_RULES ?></h3>

<p><?php echo TEXT_ACCESS_ALLOCATION_RULES_INFO ?></p>

<?php 
$form_fields_query = db_query("select r.*, f.name, f.type, f.id as fields_id, f.configuration from app_access_rules_fields r, app_fields f where r.fields_id=f.id and r.entities_id='" . _get::int('entities_id'). "'");

if(db_num_rows($form_fields_query)==0)
{	
	echo button_tag(TEXT_ADD_FIELD,url_for('access_rules/fields_form','entities_id=' . $_GET['entities_id']),true);
}

?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    
    <th><?php echo TEXT_ACTION?></th>
    <th>#</th>    
    <th width="100%"><?php echo TEXT_RULE_FOR_FIELD ?></th>           
  </tr>
</thead>
<tbody>
<?php

if(db_num_rows($form_fields_query)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

while($v = db_fetch_array($form_fields_query)):
?>
<tr>  
  <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('access_rules/fields_delete','id=' . $v['id'] . '&entities_id=' . $_GET['entities_id'])) . ' ' . button_icon_edit(url_for('access_rules/fields_form','id=' . $v['id']. '&entities_id=' . $_GET['entities_id'])) ?></td>
  <td><?php echo $v['id'] ?></td>
  <td><?php echo '<a href="' . url_for('access_rules/rules','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $v['fields_id']) . '">' . fields_types::get_option($v['type'],'name',$v['name']) . '</a>' ?></td>   
</tr>  
<?php endwhile ?>
</tbody>
</table>
</div>
