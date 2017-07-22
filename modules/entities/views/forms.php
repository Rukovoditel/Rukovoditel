<?php require(component_path('entities/navigation')) ?>


<h3 class="page-title"><?php echo  TEXT_NAV_FORM_CONFIG ?></h3>

<?php echo button_tag(TEXT_BUTTON_ADD_FORM_TAB,url_for('entities/forms_tabs_form','entities_id=' . $_GET['entities_id']))?>

<div class="forms_tabs" style="max-width: 960px;">
<ol id="forms_tabs_ol" class="sortable_tabs sortable">
  <?php 
  $count_tabs = db_count('app_forms_tabs',$_GET['entities_id'],"entities_id");

  $tabs_query = db_fetch_all('app_forms_tabs',"entities_id='" . db_input($_GET['entities_id']) . "' order by  sort_order, name");
  while($tabs = db_fetch_array($tabs_query)):
  
  $tab_is_reserved = forms_tabs::is_reserved($tabs['id']);
  
  ?>
  <li id="forms_tabs_<?php echo $tabs['id'] ?>" > <div>
  <div class="cfg_form_tab">
    
      <?php if($count_tabs>1): ?>
      <div class="cfg_form_tab_heading">
        <table width="100%">
          <tr>
            <td>
              <b><?php echo $tabs['name'] ?></b>
              <?php 
                if($tab_is_reserved)
                { 
                  echo tooltip_text(TEXT_RESERVED_FORM_TAB);
                } 
              ?>                              
            </td>
            <td class="align-right">
              <?php 
                echo  button_icon_edit(url_for('entities/forms_tabs_form','id=' . $tabs['id']. '&entities_id=' . $_GET['entities_id'])); 
                
                if(!$tab_is_reserved)
                { 
                  echo ' ' . button_icon_delete(url_for('entities/forms_tabs_delete','id=' . $tabs['id'] . '&entities_id=' . $_GET['entities_id']));
                } 
              ?>
            </td>
          </tr>
        </table>
      </div>
      <?php endif ?>
      
      <div class="cfg_forms_fields">
<?php  
echo '
  <ul id="forms_tabs_' . $tabs['id'] . '" class="sortable">
';
$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id='" . db_input($_GET['entities_id']) . "' and f.forms_tabs_id=t.id and f.forms_tabs_id='" . db_input($tabs['id']) . "' order by t.sort_order, t.name, f.sort_order, f.name");
while($v = db_fetch_array($fields_query))
{
  echo '
    <li id="form_fields_' . $v['id'] . '">
      <div>
        <table width="100%">
          <tr>
            <td>' . fields_types::get_option($v['type'],'name',$v['name']) . '</td>
            <td class="align-right">' . (!in_array($v['type'],fields_types::get_users_types()) ? button_icon_edit(url_for('entities/fields_form','id=' . $v['id']. '&entities_id=' . $_GET['entities_id']. '&redirect_to=forms')) . ' ' . button_icon_delete(url_for('entities/fields_delete','id=' . $v['id'] . '&entities_id=' . $_GET['entities_id']. '&redirect_to=forms')):'') . '</td>
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
      <div ><?php echo button_tag(TEXT_BUTTON_ADD_NEW_FIELD,url_for('entities/fields_form','entities_id=' . $_GET['entities_id'] . '&forms_tabs_id=' . $tabs['id'] . '&redirect_to=forms'),true,array('class'=>'btn btn-primary')) ?></div>          
  </div>
   </div></li>       
  <?php endwhile ?>
  </ol>
</div>



<script>
  $(function() {         
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul",
    		update: function(event,ui){  
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("entities/forms","action=sort_fields&entities_id=" . $_GET["entities_id"])?>',data: data});
        }
    	});
      
      $( "ol.sortable_tabs" ).sortable({
        handle: '.cfg_form_tab_heading',  		
    		update: function(event,ui){ 
        
          data = '';  
          $( "ol.sortable_tabs" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("entities/forms","action=sort_tabs")?>',data: data});
        }
    	});
  });  
</script> 

