<?php

$html = '';

$comments_query_sql = "select * from app_comments where entities_id='" . $current_entity_id . "' and items_id='" . $current_item_id . "'  order by date_added desc limit 1";
$items_query = db_query($comments_query_sql);
if($item = db_fetch_array($items_query))
{
  $html = '<stong>' . format_date_time($item['date_added']) . '</strong>';  
}

echo $html;

exit();