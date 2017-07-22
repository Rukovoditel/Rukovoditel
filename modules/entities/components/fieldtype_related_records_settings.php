
<div class="row">
  <div class="col-md-6">
    <fieldset>
        <legend><?php echo TEXT_FIELDS_IN_LISTING ?></legend>
        <p><?php echo TEXT_FIELDS_IN_LISTING_RELATED_ITEMS ?></p>

<div class="checkbox-list">        
<?php
  $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id='" . db_input($cfg['entity_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {
    echo '<label>'  . input_checkbox_tag('fields_in_listing[]',$v['id'], array('checked'=>in_array($v['id'],explode(',',$cfg['fields_in_listing'])))). ' '. fields_types::get_option($v['type'],'name',$v['name']) . '</label>';
  }
?>
</div>
    </fieldset>
    
  </div>
  <div class="col-md-6">
  
    <fieldset>
        <legend><?php echo TEXT_FIELDS_IN_POPUP ?></legend>
        <p><?php echo TEXT_FIELDS_IN_POPUP_RELATED_ITEMS ?></p>
        
<div class="checkbox-list">        
<?php
  $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where is_heading = 0 and f.type not in ('fieldtype_action','fieldtype_parent_item_id') and  f.entities_id='" . db_input($cfg['entity_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {
    echo '<label>'  . input_checkbox_tag('fields_in_popup[]',$v['id'], array('checked'=>in_array($v['id'],explode(',',$cfg['fields_in_popup'])))). ' '. fields_types::get_option($v['type'],'name',$v['name']) . '</label>';
  }
?>
</div>
        
    </fieldset>
  
  </div>
</div>