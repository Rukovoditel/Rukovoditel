<h3 class="page-title"><?php echo TEXT_HEADING_GLOBAL_LISTS ?></h3>

<p><?php echo TEXT_GLOBAL_LISTS_INFO ?></p>

<?php echo button_tag(TEXT_ADD,url_for('global_lists/lists_form')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>    
    <th><?php echo TEXT_ID ?></th>
    <th width="100%"><?php echo TEXT_NAME ?></th>        
  </tr>
</thead>
<tbody>
<?php if(db_count('app_global_lists')==0) echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; ?>
<?php
  $groups_query = db_fetch_all('app_global_lists','','name');
  while($v = db_fetch_array($groups_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('global_lists/lists_delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('global_lists/lists_form','id=' . $v['id'])) ?></td>
    <td><?php echo $v['id'] ?></td>    
    <td><?php echo link_to('<i class="fa fa-list"></i> ' . $v['name'],url_for('global_lists/choices','lists_id=' . $v['id'])) ?></td>    
  </tr>
<?php endwhile?>  
</tbody>
</table>
</div>