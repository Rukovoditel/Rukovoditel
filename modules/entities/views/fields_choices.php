<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo   $field_info['name'] . ': '.  TEXT_NAV_FIELDS_CHOICES_CONFIG ?></h3>

<?php echo ($field_info['type']=='fieldtype_autostatus' ? '<p class="note note-info">' . TEXT_FIELDTYPE_AUTOSTATUS_OPTIONS_TIP . '</p>':'')?>

<?php echo button_tag(TEXT_BUTTON_ADD_NEW_VALUE,url_for('entities/fields_choices_form','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']),true,array('class'=>'btn btn-primary')) . ' ' . button_tag(TEXT_BUTTON_SORT,url_for('entities/fields_choices_sort','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']),true,array('class'=>'btn btn-default')) ?>

<?php echo ($field_info['type']=='fieldtype_autostatus' ? button_tag('<i class="fa fa-sitemap"></i> ' . TEXT_FLOWCHART,url_for('entities/fields_choices_flowchart','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']),false,array('class'=>'btn btn-default')):'') ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th>Action</th>
    <th>#</th>    
    <th width="100%"><?php echo TEXT_NAME ?></th>            

<?php if($field_info['type']!='fieldtype_autostatus'): ?>    
    <th><?php echo TEXT_IS_DEFAULT ?></th>
<?php endif ?>
    
    <th><?php echo TEXT_BACKGROUND_COLOR ?></th>        
    <th><?php echo TEXT_SORT_ORDER ?></th>
    <th><?php echo TEXT_VALUE ?></th>
  </tr>
</thead>
<tbody>
<?php

$tree = fields_choices::get_tree($_GET['fields_id']);

if(count($tree)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

foreach($tree as $v):

$html = '';
if($field_info['type']=='fieldtype_autostatus')
{
	$count = 0;
	$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input(_get::int('entities_id')). "' and reports_type='fields_choices" . $v['id'] . "'");
	if($reports_info = db_fetch_array($reports_info_query))
	{
		$count = db_count('app_reports_filters',$reports_info['id'],'reports_id');
	}
	
	$html = link_to(str_repeat('&nbsp;-&nbsp;',$v['level']) . $v['name'],url_for('entities/fields_choices_filters','choices_id=' . $v['id'] . '&entities_id=' . _get::int('entities_id'). '&fields_id=' . _get::int('fields_id'))) . tooltip_text(TEXT_FILTERS . ': ' . $count);
}
?>
<tr>
  <td style="white-space: nowrap;"><?php 
      echo button_icon_delete(url_for('entities/fields_choices_delete','id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id'])); 
      echo ' ' . button_icon_edit(url_for('entities/fields_choices_form','id=' . $v['id']. '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id']));
      echo ' ' . button_icon(TEXT_BUTTON_CREATE_SUB_VALUE,'fa fa-plus',url_for('entities/fields_choices_form','parent_id=' . $v['id']. '&entities_id=' . $_GET['entities_id']. '&fields_id=' . $_GET['fields_id'])); 
  ?></td>
  <td><?php echo $v['id'] ?></td>  
  <td><?php echo ($field_info['type']=='fieldtype_autostatus' ? $html : str_repeat('&nbsp;-&nbsp;',$v['level']) . $v['name'])  ?></td>

<?php if($field_info['type']!='fieldtype_autostatus'): ?>  
  <td><?php echo render_bool_value($v['is_default']) ?></td>
<?php endif ?>
  
  <td><?php echo render_bg_color_block($v['bg_color']) ?></td>
  <td><?php echo $v['sort_order'] ?></td>      
  <td><?php echo $v['value'] ?></td>
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>

<?php echo '<a class="btn btn-default" href="' . url_for('entities/fields','entities_id=' . $_GET['entities_id']) . '">' . TEXT_BUTTON_BACK. '</a>'; ?>






