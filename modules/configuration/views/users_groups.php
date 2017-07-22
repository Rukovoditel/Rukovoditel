<h3 class="page-title"><?php echo TEXT_HEADING_USERS_ACCESS_GROUPS ?></h3>

<?php echo button_tag(TEXT_ADD_NEW_USER_GROUP,url_for('configuration/users_groups_form')) ?>
<?php echo ' ' . button_tag(TEXT_SORT_GROUPS,url_for('configuration/users_groups_sort')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>    
    <th width="100%"><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_IS_DEFAULT ?></th>
    <th><?php echo TEXT_IS_LDAP_DEFAULT ?></th>
    <th><?php echo TEXT_SORT_ORDER ?></th>    
  </tr>
</thead>
<tbody>
<?php if(db_count('app_access_groups')==0) echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; ?>
<?php
  $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
  while($v = db_fetch_array($groups_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('configuration/users_groups_delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('configuration/users_groups_form','id=' . $v['id'])) ?></td>    
    <td><?php echo $v['name'] ?></td>
    <td><?php echo render_bool_value($v['is_default']) ?></td>
    <td><?php echo render_bool_value($v['is_ldap_default']) ?></td>
    <td><?php echo $v['sort_order'] ?></td>
  </tr>
<?php endwhile?>  
</tbody>
</table>
</div>