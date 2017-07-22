<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_NAV_FIELDS_ACCESS ?></h3>

<?php echo form_tag('cfg', url_for('entities/fields_access','action=set_access&entities_id=' . $_GET['entities_id'])) ?>
<?php echo input_hidden_tag('ui_accordion_active',0) ?>
<?php
  $fields_list = array();
  $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list(). ") and f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
  while($v = db_fetch_array($fields_query))
  {
    $fields_list[$v['id']] = fields_types::get_option($v['type'],'name',$v['name']) ;
  }
?>


<div id="accordion">  
  <h3><?php echo TEXT_ADMINISTRATOR ?></h3>
  <div>
    <?php echo TEXT_ADMINISTRATOR_FULL_ACCESS ?>
  </div>
<?php
  $access_choices = array('yes'=>TEXT_YES,'view'=>TEXT_VIEW_ONLY,'hide'=>TEXT_HIDE);
  
  $count = 0;
  $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
  while($groups = db_fetch_array($groups_query))
  {     
    $entities_access_schema = users::get_entities_access_schema($_GET['entities_id'],$groups['id']);
    
    if(!in_array('view',$entities_access_schema) and  !in_array('view_assigned',$entities_access_schema)) continue;
                  
    $count++;
  
    $html = '
      <div class="table-scrollable">
      <table class="table table-striped table-bordered table-hover">
        <tr>
          <th>' . TEXT_FIELDS . '</th>
          <th>' . TEXT_ACCESS . ': ' . select_tag('access_' . $groups['id'],array_merge(array(''=>''),$access_choices),'',array('class'=>'form-control input-medium ','onChange'=>'set_access_to_all_fields(this.value,' . $groups['id'] . ')')) . '</th>
        </tr>
      ';
      
    $access_schema = users::get_fields_access_schema($_GET['entities_id'],$groups['id']);
    
      
    foreach($fields_list as $id=>$name)
    {
      $value = (isset($access_schema[$id]) ? $access_schema[$id] : 'yes');
      
      $html .= '
        <tr>
          <td>' . $name . '</td>
          <td>' . select_tag('access[' . $groups['id']. '][' . $id . ']',$access_choices, $value,array('class'=>'form-control input-medium access_group_' . $groups['id'])). '</td>
        </tr>
      ';
    }
    $html .= '</table></div>';
    
    echo '
      <h3>' . $groups['name'] . '</h3>
      <div>
        ' . $html . '
      </div>
    ';
  } 
?>

</div>
<br>
<?php if($count>0) echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>

<script>
  $(function() {
    $( "#accordion" ).accordion({heightStyle:'content', active: <?php echo (isset($_GET["ui_accordion_active"]) ? $_GET["ui_accordion_active"]:"0") ?>,
        activate: function( event, ui ) {          
          active = $('#accordion').accordion('option', 'active');
          $('#ui_accordion_active').val(active)
        
        }
    });
  });
  </script>



