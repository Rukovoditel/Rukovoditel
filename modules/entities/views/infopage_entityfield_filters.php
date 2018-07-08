
<?php require(component_path('entities/navigation')) ?>

<?php $fields_info = db_find('app_fields',$fields_id); ?>    
<h3 class="page-title"><?php echo  TEXT_NAV_LISTING_FILTERS_CONFIG . ' "' . $fields_info['name'] . '" (' . $entities_info['name'] . ')' ?></h3>

<p><?php echo TEXT_LISTING_FILTERS_CFG_INFO ?></p>

<?php 
  echo button_tag(TEXT_BUTTON_ADD_NEW_REPORT_FILTER,url_for('entities/infopage_entityfield_filters_form','reports_id=' . $reports_info['id'] . '&entities_id=' . $_GET['entities_id'] . '&related_entities_id=' . $_GET['related_entities_id'] . '&fields_id=' . $_GET['fields_id'])) . ' ' 
			. button_tag(TEXT_BUTTON_CONFIGURE_SORTING,url_for('reports/sorting','reports_id=' . $reports_info['id'] . '&related_entities_id=' . $_GET['related_entities_id']  . '&fields_id=' . $_GET['fields_id']. '&redirect_to=infopage_entityfield_filters'))  . ' '
			. button_tag(TEXT_NAV_LISTING_CONFIG,url_for('reports/configure','reports_id=' . $reports_info['id'] . '&related_entities_id=' . $_GET['related_entities_id'] . '&fields_id=' . $_GET['fields_id'] . '&redirect_to=infopage_entityfield_filters'))
?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>        
    <th width="100%"><?php echo TEXT_FIELD ?></th>    
    <th><?php echo TEXT_VALUES ?></th>
            
  </tr>
</thead>
<tbody>
<?php  
  $filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($reports_info['id']) . "' order by rf.id");
  
  if(db_num_rows($filters_query)==0) echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND. '</td></tr>';
  
  while($v = db_fetch_array($filters_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php 
    echo button_icon_delete(url_for('entities/infopage_entityfield_filters_delete','id=' . $v['id'] . '&reports_id=' . $reports_info['id']. '&entities_id=' . $_GET['entities_id'] . '&related_entities_id=' . $_GET['related_entities_id'] . '&fields_id=' . $_GET['fields_id'])) . ' ' . button_icon_edit(url_for('entities/infopage_entityfield_filters_form','id=' . $v['id'] . '&reports_id=' . $reports_info['id']. '&entities_id=' . $_GET['entities_id'] . '&related_entities_id=' . $_GET['related_entities_id'] . '&fields_id=' . $_GET['fields_id']))  ?></td>    
    <td><?php echo fields_types::get_option($v['type'],'name',$v['name']) ?></td>    
    <td class="nowrap"><?php echo reports::render_filters_values($v['fields_id'],$v['filters_values'],'<br>',$v['filters_condition']) ?></td>            
  </tr>
<?php endwhile?>  
</tbody>
</table>
</div>

<?php echo '<a class="btn btn-default" href="' . url_for('entities/item_page_configuration','entities_id=' .  $_GET['related_entities_id'] ). '">' . TEXT_BUTTON_BACK . '</a>';?>