<?php $lists_info = db_find('app_global_lists',$_GET['lists_id']) ?>

<h3 class="page-title"><?php echo   $lists_info['name'] . ': '.  TEXT_NAV_FIELDS_CHOICES_CONFIG ?></h3>

<ul class="page-breadcrumb breadcrumb">
  <li><?php echo link_to(TEXT_HEADING_GLOBAL_LISTS,url_for('global_lists/lists'))?><i class="fa fa-angle-right"></i></li>  
  <li><?php echo $lists_info['name'] ?></li>
</ul>

<?php 
	echo button_tag(TEXT_BUTTON_ADD_NEW_VALUE,url_for('global_lists/choices_form','lists_id=' . $_GET['lists_id']),true,array('class'=>'btn btn-primary')) . ' ' . 
			 button_tag(TEXT_BUTTON_SORT,url_for('global_lists/choices_sort','lists_id=' . $_GET['lists_id']),true,array('class'=>'btn btn-default')) . ' ' . 
			 button_tag(TEXT_BUTTON_IMPORT,url_for('global_lists/choices_import','lists_id=' . $_GET['lists_id']),true,array('class'=>'btn btn-default')); 
?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th>Action</th>
    <th>#</th>    
    <th width="100%"><?php echo TEXT_NAME ?></th>            
    <th><?php echo TEXT_IS_DEFAULT ?></th>
    <th><?php echo TEXT_BACKGROUND_COLOR ?></th>        
    <th><?php echo TEXT_SORT_ORDER ?></th>
  </tr>
</thead>
<tbody>
<?php

$tree = global_lists::get_choices_tree($_GET['lists_id']);

if(count($tree)==0) echo '<tr><td colspan="9">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; 

foreach($tree as $v):
?>
<tr>
  <td style="white-space: nowrap;"><?php 
      echo button_icon_delete(url_for('global_lists/choices_delete','id=' . $v['id'] . '&lists_id=' . $_GET['lists_id'])); 
      echo ' ' . button_icon_edit(url_for('global_lists/choices_form','id=' . $v['id'] . '&lists_id=' . $_GET['lists_id']));
      echo ' ' . button_icon(TEXT_BUTTON_CREATE_SUB_VALUE,'fa fa-plus',url_for('global_lists/choices_form','parent_id=' . $v['id'] . '&lists_id=' . $_GET['lists_id'])); 
  ?></td>
  <td><?php echo $v['id'] ?></td>  
  <td><?php echo str_repeat('&nbsp;-&nbsp;',$v['level']) . $v['name']  ?></td>
  <td><?php echo render_bool_value($v['is_default']) ?></td>
  <td><?php echo render_bg_color_block($v['bg_color']) ?></td>
  <td><?php echo $v['sort_order'] ?></td>      
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>






