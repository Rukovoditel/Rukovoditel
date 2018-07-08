<h3 class="page-title"><?php echo TEXT_REPORTS_GROUPS ?></h3>

<p><?php echo TEXT_REPORTS_GROUPS_INFO ?></p>

<?php echo button_tag(TEXT_BUTTON_ADD,url_for('reports_groups/form')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>           
    <th width="100%"><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_IN_MENU ?></th>           
    <th><?php echo TEXT_SORT_ORDER ?></th>
  </tr>
</thead>
<tbody>
<?php
  $reports_query = db_query("select * from app_reports_groups where created_by = '" . $app_user['id'] . "' order by sort_order, name");
  while($v = db_fetch_array($reports_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('reports_groups/delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('reports_groups/form','id=' . $v['id'])); ?></td>        
    <td><?php echo link_to($v['name'],url_for('dashboard/reports','id=' . $v['id'])) ?></td>
    <td><?php echo render_bool_value($v['in_menu']) ?></td>       
    <td><?php echo $v['sort_order'] ?></td>
  </tr>
<?php endwhile?>
<?php if(db_num_rows($reports_query)==0) echo '<tr><td colspan="7">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';?>  
</tbody>
</table>
</div>