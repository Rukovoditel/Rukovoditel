<?php echo ajax_modal_template_header(TEXT_NAV_LISTING_CONFIG) ?>

<?php
  if($app_redirect_to=='common_reports')
  {
    echo form_tag('sorting_form', url_for('ext/common_reports/reports'));
  }
  elseif(isset($_GET['path']))
  {
    echo form_tag('sorting_form', url_for('items/','path=' . $_GET['path']));
  }
  else
  { 
    echo form_tag('sorting_form', url_for('reports/view','reports_id=' . $reports_info['id']));
  } 
  
  $fields_access_schema = users::get_fields_access_schema($reports_info['entities_id'],$app_user['group_id']);
?>

<div class="modal-body">

    
<div><?php echo TEXT_LISTING_CFG_INFO ?></div>

<table width="100%">
  <tr>
    <td valign="top" width="45%">
      <fieldset>
        <legend><?php echo TEXT_FIELDS_IN_LISTING ?></legend>
<div class="cfg_listing">        
  <ul id="fields_for_listing" class="sortable">
  <?php  
  if(count($fields_in_listing)>0)
  {
    $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.id in (" . implode(',',$fields_in_listing). ") and  f.entities_id='" . db_input($reports_info['entities_id']) . "' and f.forms_tabs_id=t.id order by field(f.id," . implode(',',$fields_in_listing) . ")");
    while($v = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$v['id']]))
      {
        if($fields_access_schema[$v['id']]=='hide') continue;
      }
      
      //skip fieldtype_parent_item_id for deafult listing
      if($v['type']=='fieldtype_parent_item_id' and $reports_info['parent_id']==0)
      {
        continue;      
      }
            
      echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']) . '</div></li>';
    }
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    </td>
    <td style="padding-left: 25px;" valign="top">
      <fieldset>
        <legend><?php echo TEXT_FIELDS_EXCLUDED_FROM_LISTING ?></legend>
<div class="cfg_listing">        
<ul id="fields_excluded_from_listing" class="sortable">
<?php
$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where " . (count($fields_in_listing)>0 ? "f.id not in (" . implode(',',$fields_in_listing). ") and " : "") . "  f.entities_id='" . db_input($reports_info['entities_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{

  //check field access
  if(isset($fields_access_schema[$v['id']]))
  {
    if($fields_access_schema[$v['id']]=='hide') continue;
  }
  
  //skip fieldtype_parent_item_id for deafult listing
  if($v['type']=='fieldtype_parent_item_id' and $reports_info['parent_id']==0)
  {
    continue;      
  }
      
  echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']). '</div></li>';
}
?> 
</ul>
</div>                     
      </fieldset>
    </td>
  </tr>
</table>

<?php echo TEXT_SHOW . ' ' .  input_tag('rows_per_page',($reports_info['rows_per_page']>0 ? $reports_info['rows_per_page'] : CFG_APP_ROWS_PER_PAGE), array('class'=>'form-control form-control-inline input-xsmall')) . ' <span style="text-transform: lowercase;">' . TEXT_ROWS_PER_PAGE . '.</span>' ?>

</div>


<script>
         
  $(function() {                     
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",       
    		update: function(event,ui)
        {               
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("reports/configure","action=set_listing_fields&reports_id=" . $_GET["reports_id"])?>',data: data});
        }
    	});

    	$('#rows_per_page').keyup(function(){
    		$.ajax({type: "POST",url: '<?php echo url_for("reports/configure","action=set_rows_per_page&reports_id=" . $_GET["reports_id"])?>',data: {rows_per_page: $(this).val()}});
      })
      
  });  
</script>
 
<?php echo ajax_modal_template_footer() ?>

</form> 