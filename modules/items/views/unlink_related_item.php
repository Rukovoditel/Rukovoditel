<?php echo ajax_modal_template_header(TEXT_UNLINK) ?>

<?php echo form_tag('remove_related_items', url_for('items/related_item','action=remove_related_items&path=' . $_GET['path'])) ?>

<div class="modal-body">
  <p><?php echo TEXT_PLEASE_SELECT_ITEMS ?></p>   
<?php
  $related_records = new related_records($current_entity_id,$current_item_id);
  $related_records->set_related_field($_GET['fields_id']);
  $related_items = $related_records->get_related_items();
  
  $related_entities_id = (int)$_GET['related_entities_id'];
  
  $listing_sql_query = '';
  $listing_sql_query_join = '';
  
  //check view assigned only access
  $listing_sql_query = items::add_access_query($related_entities_id,$listing_sql_query);

  //include access to parent records
  $listing_sql_query .= items::add_access_query_for_parent_entities($related_entities_id);
  
  $listing_sql_query .= " and e.id in (" . implode(',',$related_items) . ")";
  
  $listing_sql_query .= items::add_listing_order_query_by_entity_id($related_entities_id);
  
  $items_sql_query = "select * from app_entity_" . $related_entities_id . " e " . $listing_sql_query_join . " where id>0 " . $listing_sql_query; 
  $items_query = db_query($items_sql_query);
  
  if(db_num_rows($items_query)>0)
  {
    echo '<div><label>' . input_checkbox_tag('select_all_related_items','1') . ' ' . TEXT_SELECT_ALL . '</label></div>';
  }
  
  while($items = db_fetch_array($items_query))     
  {
    $path_info = items::get_path_info($related_entities_id,$items['id']);
    
    $related_id = array_search($items['id'],$related_items);
    
    echo '<div>' . input_checkbox_tag('items[]',$related_id,array('class'=>'remove_related_item')) . ' <a href="' . url_for('items/info', 'path=' . $path_info['full_path']) . '" target="_blnak">' . items::get_heading_field($related_entities_id,$items['id']) . '</a></div>';
  }
  
  echo input_hidden_tag('related_entities_id',$related_entities_id);
?>  
  
</div>
 
<?php echo ajax_modal_template_footer(TEXT_UNLINK) ?>

</form>

<script>
  $("#select_all_related_items").change(function(){
    select_all_by_classname("select_all_related_items","remove_related_item");
  });
</script>