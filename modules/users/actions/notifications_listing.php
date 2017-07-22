<?php

$html = '
	<div class="table-scrollable">
		<table class="table table-striped table-bordered table-hover">
		<thead>  
		  <tr>	
				<th>' . input_checkbox_tag('select_all_items','',array('class'=>'select_all_items')) . '</th>
		    <th width="100%">' . TEXT_DESCRIPTION . '</th>
		    <th>' . TEXT_CREATED_BY . '</th>
		    <th>' . TEXT_DATE_ADDED . '</th>
		  </tr>
		</thead>
		<tbody> 		
';


$listing_sql = "select * from app_users_notifications where users_id='" . $app_user['id'] . "' order by id desc";
$listing_split = new split_page($listing_sql,'users_notifications_listing');
$items_query = db_query($listing_split->sql_query);
while($item = db_fetch_array($items_query))
{
	$path_info = items::get_path_info($item['entities_id'],$item['items_id']);
	
	$html .= '
			<tr>
				<td>' . input_checkbox_tag('items_' . $item['id'],$item['id'],array('class'=>'items_checkbox','checked'=>in_array($item['id'],$app_selected_notification_items))) . '</td>
				<td style="white-space: normal;"><a href="' . url_for('items/info','path=' . $path_info['full_path']) . '">' . users_notifications::render_icon_by_type($item['type']) . ' ' . $item['name'] . '</a></td>
				<td>' . (isset($app_users_cache[$item['created_by']]) ? $app_users_cache[$item['created_by']]['name'] : '') . '</td>		
				<td>' . format_date_time($item['date_added']) . '</td>
			</tr>
	';
}


if($listing_split->number_of_rows==0)
{
	$html .= '
    <tr>
      <td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  ';
}

$html .= '
  </tbody>
</table>
</div>
';

//add pager
$html .= '
  <table width="100%">
    <tr>
      <td>' . $listing_split->display_count() . '</td>
      <td align="right">' . $listing_split->display_links(). '</td>
    </tr>
  </table>
';

echo $html;

exit();
