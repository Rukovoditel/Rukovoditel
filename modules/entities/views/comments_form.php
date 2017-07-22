<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_NAV_COMMENTS_FORM_CONFIG ?></h3>

<p><?php echo TEXT_COMMENTS_FORM_CFG_INFO ?></p>

<table style="width: 100%; max-width: 960px;">
  <tr>
    <td valign="top" width="50%">      
      
      <fieldset>
        <legend><?php echo TEXT_FIELDS_IN_COMMENTS_FORM ?></legend>
<div class="cfg_listing">        
<ul id="fields_in_comments" class="sortable">
<?php
$fields_query = db_query("select f.* from app_fields f where  comments_status = 1 and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.comments_forms_tabs_id=0 order by f.comments_sort_order");
while($v = db_fetch_array($fields_query))
{
  echo '<li id="fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']). '</div></li>';
}
?> 
</ul>
</div>                     
      </fieldset>  
      
      
<?php echo button_tag(TEXT_BUTTON_ADD_FORM_TAB,url_for('entities/comments_forms_tabs_form','entities_id=' . $_GET['entities_id']))?>

<div class="forms_tabs" style="max-width: 960px;">
<ol id="forms_tabs_ol" class="sortable_tabs sortable">
<?php   
  $tabs_query = db_fetch_all('app_comments_forms_tabs',"entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name");
  while($tabs = db_fetch_array($tabs_query)):      
?>
  <li id="forms_tabs_<?php echo $tabs['id'] ?>" > <div>
  <div class="cfg_form_tab">
         
      <div class="cfg_form_tab_heading">
        <table width="100%">
          <tr>
            <td>
              <b><?php echo $tabs['name'] ?></b>                             
            </td>
            <td class="align-right">
              <?php echo  button_icon_edit(url_for('entities/comments_forms_tabs_form','id=' . $tabs['id']. '&entities_id=' . $_GET['entities_id'])) . ' ' . button_icon_delete(url_for('entities/comments_forms_tabs_delete','id=' . $tabs['id'] . '&entities_id=' . $_GET['entities_id'])); ?>
            </td>
          </tr>
        </table>
      </div>
            
      <div class="cfg_forms_fields">
<?php  
echo '
  <ul id="forms_tabs_' . $tabs['id'] . '" class="sortable">
';
$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_comments_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.comments_forms_tabs_id=t.id and f.comments_forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{
  echo '
    <li id="fields_' . $v['id'] . '">
      <div>
        <table width="100%">
          <tr>
            <td>' . fields_types::get_option($v['type'],'name',$v['name']) . '</td>            
          </tr>
        </table>
      </div>
    </li>';
}
echo '
  </ul>
';  
?>            
      </div>                      
  </div>
   </div></li>       
  <?php endwhile ?>
  </ol>
</div>          
      
    </td>
    <td style="padding-left: 25px;" valign="top">

      <fieldset>
        <legend><?php echo TEXT_AVAILABLE_FIELS ?></legend>
<div class="cfg_listing">        
  <ul id="available_fields" class="sortable">
  <?php
  $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type in ('" . implode("','",comments::get_available_filedtypes_in_comments()) . "') and  comments_status = 0 and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");  
  while($v = db_fetch_array($fields_query))
  {
    echo '<li id="fields_' . $v['id'] . '"><div>' . fields_types::get_option($v['type'],'name',$v['name']) . '</div></li>';
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
          $.ajax({type: "POST",url: '<?php echo url_for("entities/comments_form","action=set_fields&entities_id=" . $_GET["entities_id"])?>',data: data});
        }
    	});


    	$( "ol.sortable_tabs" ).sortable({
        handle: '.cfg_form_tab_heading',  		
    		update: function(event,ui){ 
        
          data = '';  
          $( "ol.sortable_tabs" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("entities/comments_form","action=sort_tabs")?>',data: data});
        }
    	});
      
  });  
</script>



