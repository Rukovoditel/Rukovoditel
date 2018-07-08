<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_NAV_ENTITY_ACCESS ?></h3>

<p><?php echo TEXT_ENTITY_ACCESS_INFO . TEXT_ENTITY_ACCESS_INFO_EXTRA ?></p>

<?php echo form_tag('cfg', url_for('entities/access','action=set_access&entities_id=' . $_GET['entities_id'])) ?>

<table class="table table-striped table-bordered table-hover">
  <tr>
    <th><?php echo TEXT_USERS_GROUPS ?></th>
    <th><?php echo TEXT_VIEW_ACCESS ?></th>
    <th><?php echo TEXT_ACCESS ?></th>        
  </tr>
  <tr>
    <td><?php echo TEXT_ADMINISTRATOR ?></td>
    <td><?php echo TEXT_FULL_ACCESS ?></td>
    <td><?php echo TEXT_FULL_ACCESS ?></td>    
  </tr>
    
<?php
      
  $count = 0;
  $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
  while($v = db_fetch_array($groups_query))
  {        
    $count++; 
    
    $access_schema = array();
    
    $acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $v['id']. "'");
    if($acess_info = db_fetch_array($acess_info_query))
    {
      $access_schema = explode(',',$acess_info['access_schema']);                          
    }
                       
    echo '
      <tr>
        <td>' . $v['name']. '</td>
        <td>' . select_tag('access[' . $v['id']. '][]',access_groups::get_access_view_choices(),access_groups::get_access_view_value($access_schema),array('id'=>'access_' . $v['id'],'class'=>'form-control input-large','onChange'=>'check_access_schema(this.value,' . $v['id'] . ')')) . '</td>
  			<td>' . select_tag('access[' . $v['id']. '][]',access_groups::get_access_choices(),$access_schema,array('id'=>'access_shcema_' . $v['id'],'class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')). '</td>	        
      </tr>    
    ';    
  }
  
?>
</table>

<br>
<?php if($count>0) echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form>

<script>
function check_access_schema(access,group_id)
{
	if(access=='')
	{
		$('#access_shcema_'+group_id).val('');
		$('#access_shcema_'+group_id).trigger("chosen:updated");
		
	}
}
</script> 





