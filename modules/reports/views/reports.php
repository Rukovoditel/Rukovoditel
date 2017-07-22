<h3 class="page-title"><?php echo TEXT_HEADING_REPORTS ?></h3>

<?php echo button_tag(TEXT_BUTTON_ADD_NEW_REPORT,url_for('reports/form')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>
    <th><?php echo TEXT_REPORT_ENTITY ?></th>        
    <th width="100%"><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_IN_MENU ?></th>
    <th><?php echo TEXT_IN_DASHBOARD ?></th>        
    <th><?php echo TEXT_DISPLAY_IN_HEADER ?></th>
  </tr>
</thead>
<tbody>
<?php
  $reports_query = db_query("select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' order by e.name, r.name");
  while($v = db_fetch_array($reports_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('reports/delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('reports/form','id=' . $v['id'])) . ' ' . button_icon(TEXT_BUTTON_CONFIGURE_FILTERS,'fa fa-cogs',url_for('reports/filters','reports_id=' . $v['id']),false); ?></td>    
    <td><?php echo $v['entities_name'] ?></td>
    <td><?php echo link_to($v['name'],url_for('reports/view','reports_id=' . $v['id'])) ?></td>
    <td><?php echo render_bool_value($v['in_menu']) ?></td>
    <td><?php echo render_bool_value($v['in_dashboard']) ?></td>    
    <td><?php echo render_bool_value($v['in_header']) ?></td>
  </tr>
<?php endwhile?>
<?php if(db_num_rows($reports_query)==0) echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';?>  
</tbody>
</table>
</div>