<?php require(component_path('entities/navigation')) ?>


<h3 class="page-title"><?php echo TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG ?></h3>

<div><?php echo TEXT_USER_PUBLIC_PROFILE_CFG_INFO ?></div>

<table style="width: 100%; max-width: 960px;">
  <tr>
    <td valign="top" width="50%">
      <fieldset>
        <legend><?php echo TEXT_FIELDS_IN_USER_PUBLIC_PROFILE ?></legend>
<div class="cfg_listing">        
  <ul id="fields_in_profile" class="sortable">
  <?php
  $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list() . "," . fields_types::get_users_types_list(). ") and f.id in (" . $public_user_profile_fields . ") and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id order by  field(f.id," . $public_user_profile_fields . ")");
  while($v = db_fetch_array($fields_query))
  {
    echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']) . '</div></li>';
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    
    </td>
    <td style="padding-left: 25px;" valign="top">
    
      <fieldset>
        <legend><?php echo TEXT_FIELDS_EXCLUDED_FROM_USER_PUBLIC_PROFILE ?></legend>
<div class="cfg_listing">        
<ul id="fields_excluded_from_profile" class="sortable">
<?php
$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list()  . "," . fields_types::get_users_types_list(). ") and f.id not in (" . $public_user_profile_fields . ") and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{
  echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']). '</div></li>';
}
?> 
</ul>
</div>                     
      </fieldset>
      
      
    </td>
  </tr>
</table>



<script>
  $(function() {         
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",
    		update: function(event,ui){  
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("entities/user_public_profile","action=sort_fields&entities_id=" . $_GET["entities_id"])?>',data: data});
        }
    	});
      
  });  
</script>



