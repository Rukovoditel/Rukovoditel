<h3 class="page-title"><?php echo TEXT_MENU_CONFIGURATION_MENU ?></h3>

<p><?php echo TEXT_CONFIGURATION_MENU_EXPLAIN ?></p>

<?php echo button_tag(TEXT_ADD_NEW_MENU_ITEM,url_for('entities/menu_form')) ?>
<?php echo ' ' . button_tag(TEXT_SORT,url_for('entities/menu_sort')) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>    
    <th width="100%"><?php echo TEXT_NAME ?></th>    
    <th><?php echo TEXT_SORT_ORDER ?></th>    
  </tr>
</thead>
<tbody>
<?php if(db_count('app_entities_menu')==0) echo '<tr><td colspan="5">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; ?>
<?php
  $menu_query = db_fetch_all('app_entities_menu','','sort_order, name');
  while($v = db_fetch_array($menu_query)):
?>
  <tr>
    <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('entities/menu_delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('entities/menu_form','id=' . $v['id'])) . ' ' . button_icon(TEXT_SORT,'fa fa-sort-alpha-asc',url_for('entities/menu_items_sort','id=' . $v['id'])) ?></td>    
    <td><?php 
    	echo '<i class="fa ' . (strlen($v['icon'])>0 ? $v['icon']:'fa-list-alt'). '" aria-hidden="true"></i> <b>' . $v['name'] . '</b>';
			
			if(strlen($v['entities_list'])>0)
			{
				$entities_query = db_query("select * from app_entities where id in (" . $v['entities_list'] . ") order by field(id," . $v['entities_list'] . ")");
				while($entities = db_fetch_array($entities_query))
				{
					echo '<div style="padding-left: 19px;">- ' . $entities['name'] . '</div>';
				}
			}
		
    ?></td>    
    <td><?php echo $v['sort_order'] ?></td>
  </tr>
<?php endwhile?>  
</tbody>
</table>
</div>