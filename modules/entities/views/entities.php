<h3 class="page-title"><?php echo TEXT_ENTITIES_HEADING ?></h3>

<?php echo button_tag(TEXT_ADD_NEW_ENTITY,url_for('entities/entities_form')) ?>

<div class="table-scrollable" style="overflow-x:visible;overflow-y:visible; ">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_ACTION ?></th>
    <th>#</th>
    <th width="100%"><?php echo TEXT_NAME ?></th>
    <th><?php echo TEXT_NOTE ?></th>
    <th><?php echo TEXT_SORT_ORDER ?></th>    
  </tr>
</thead>
<tbody>
<?php if(count($entities_list)==0) echo '<tr><td colspan="4">' . TEXT_NO_RECORDS_FOUND. '</td></tr>'; ?>
<?php foreach($entities_list as $v): ?>
<tr>
  <td style="white-space: nowrap;"><?php echo button_icon_delete(url_for('entities/entities_delete','id=' . $v['id'])) . ' ' . button_icon_edit(url_for('entities/entities_form','id=' . $v['id'])) . ' ' . button_icon(TEXT_CREATE_SUB_ENTITY,'fa fa-plus',url_for('entities/entities_form','parent_id=' . $v['id'])) ?></td>
  <td><?php echo $v['id']?></td>
  <td style="white-space: nowrap">
  	<?php echo  str_repeat('&nbsp;<i class="fa fa-minus" aria-hidden="true"></i>&nbsp;', $v['level']) ?>
  	
  	<div class="btn-group">
			<button type="button" type="button" class="btn btn-default" onClick="location.href='<?php echo url_for('entities/entities_configuration','entities_id=' . $v['id']) ?>'"><?php echo $v['name'] ?></button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"><i class="fa fa-angle-down"></i></button>						
			<ul class="dropdown-menu" role="menu">
				<li><?php echo link_to(TEXT_NAV_GENERAL_CONFIG,url_for('entities/entities_configuration&entities_id=' . $v['id'])) ?></li>
				<li><?php echo link_to(TEXT_NAV_FIELDS_CONFIG,url_for('entities/fields&entities_id=' . $v['id'])) ?></li>
	      <li class="dropdown-submenu">
					<a href="#"><?php echo TEXT_NAV_VIEW_CONFIG ?></a>
					<ul class="dropdown-menu">
						<li><?php echo link_to(TEXT_NAV_FORM_CONFIG,url_for('entities/forms','entities_id=' . $v['id'])) ?></li>
						<li><?php echo link_to(TEXT_NAV_LISTING_CONFIG,url_for('entities/listing','entities_id=' . $v['id'])) ?></li>
						<li><?php echo link_to(TEXT_NAV_LISTING_FILTERS_CONFIG,url_for('entities/listing_filters','entities_id=' . $v['id'])) ?></li>
	          <?php if($v['id']==1): ?>
	          	<li><?php echo link_to(TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG,url_for('entities/user_public_profile','entities_id=' . $v['id'])) ?></li>
	          <?php endif ?>
					</ul>
				</li>
	      <li class="dropdown-submenu">
					<a href="#"><?php echo TEXT_NAV_ACCESS_CONFIG ?></a>
					<ul class="dropdown-menu">
						<li><?php echo link_to(TEXT_NAV_ENTITY_ACCESS,url_for('entities/access','entities_id=' . $v['id'])) ?></li>
						<li><?php echo link_to(TEXT_NAV_FIELDS_ACCESS,url_for('entities/fields_access','entities_id=' . $v['id'])) ?></li>
					</ul>
				</li>
	      <li class="dropdown-submenu">
					<a href="#"><?php echo TEXT_NAV_COMMENTS_CONFIG ?></a>
					<ul class="dropdown-menu">
						<li><?php echo link_to(TEXT_NAV_COMMENTS_ACCESS,url_for('entities/comments_access','entities_id=' . $v['id'])) ?></li>
						<li><?php echo link_to(TEXT_NAV_COMMENTS_FIELDS,url_for('entities/comments_form','entities_id=' . $v['id'])) ?></li>
					</ul>
				</li>
			</ul>						
		</div>
		
  </td>
  <td><?php echo tooltip_icon($v['notes'],'left') ?></td>
  <td><?php echo $v['sort_order']?></td>
</tr>  
<?php endforeach ?>
</tbody>
</table>
</div>



