<h3 class="page-title"><?php echo TEXT_USERS_ALERTS ?></h3>

<p><?php echo TEXT_USERS_ALERTS_INFO; ?></p>

<?php echo button_tag(TEXT_BUTTON_ADD,url_for('users_alerts/form')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>    
    <th><?php echo TEXT_TYPE ?></th>        
    <th width="100%"><?php echo TEXT_TITLE ?></th>
    <th><?php echo TEXT_LOCATION ?></th>
    <th><?php echo TEXT_DATE_FROM ?></th>
    <th><?php echo TEXT_DATE_TO ?></th>       
    <th><?php echo TEXT_ASSIGNED_TO ?></th>
    <th><?php echo TEXT_IS_ACTIVE ?></th>        
    <th><?php echo TEXT_CREATED_BY ?></th>
  </tr>
</thead>
<tbody>
<?php
	$access_groups_cache = access_groups::get_cache();
	
  $alets_query = db_query("select * from app_users_alerts order by id desc");
  while($alets = db_fetch_array($alets_query)):  
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('users_alerts/delete','id=' . $alets['id'])) . ' ' . button_icon_edit(url_for('users_alerts/form','id=' . $alets['id'])); ?></td>        
    <td><?php echo '<span class="label label-' . $alets['type'] . '">' . users_alerts::get_type_by_name($alets['type']) . '</span>' ?></td>
    <td><?php echo $alets['title'] ?></td>
    <td><?php echo ($alets['location']=='all' ? TEXT_LOCATION_ON_ALL_PAGES : TEXT_LOCATION_ON_DASHBOARD) ?></td>
    <td><?php echo ($alets['start_date'] ? format_date($alets['start_date']) : '') ?></td>
    <td><?php echo ($alets['end_date'] ? format_date($alets['end_date']) : '') ?></td>        
    <td>
<?php
  if(strlen($alets['users_groups'])>0)
  {
    $users_groups = array();
    foreach(explode(',',$alets['users_groups']) as $id)
    {
      $users_groups[] = $access_groups_cache[$id];
    }
    
    if(count($users_groups)>0)
    {        
      echo '<span style="display:block" data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(implode(', ',$users_groups)). '">' . TEXT_USERS_GROUPS . ' (' . count($users_groups) . ')</span>';
    }
  }
  
  if($alets['assigned_to']>0)
  {
    $assigned_to = array();
    foreach(explode(',',$alets['assigned_to']) as $id)
    {
      $assigned_to[] = $app_users_cache[$id]['name'];
    }
    
    if(count($assigned_to)>0)
    {        
      echo '<span data-html="true" data-toggle="tooltip" data-placement="left" title="' . addslashes(implode(', ',$assigned_to)). '">' . TEXT_USERS_LIST . ' (' . count($assigned_to) . ')</span>';
    }
  }   
?>    
    </td>
    <td><?php echo render_bool_value($alets['is_active']) ?></td>    
    <td><?php echo users::get_name_by_id($alets['created_by']) ?></td>
  </tr>
<?php endwhile?>
<?php if(db_num_rows($alets_query)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND . '</td></tr>';?>  
</tbody>
</table>
</div>