<?php require(component_path('entities/navigation')) ?>


<h3 class="page-title"><?php echo TEXT_NAV_ENTITY_ACCESS ?></h3>

<p><?php echo TEXT_ENTITY_ACCESS_INFO ?></p>

<?php echo form_tag('cfg', url_for('entities/access','action=set_access&entities_id=' . $_GET['entities_id'])) ?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
  <tr>
    <th><?php echo TEXT_USERS_GROUPS ?></th>
    <th><?php echo TEXT_VIEW_ACCESS ?></th>
    <th><?php echo TEXT_CREATE_ACCESS ?></th>
    <th><?php echo TEXT_UPDATE_ACCESS ?></th>
    <th><?php echo TEXT_DELETE_ACCESS ?></th>
    <th><?php echo TEXT_REPORTS ?></th>
  </tr>
  <tr>
    <td><?php echo TEXT_ADMINISTRATOR ?></td>
    <td><?php echo TEXT_YES ?></td>
    <td><?php echo TEXT_YES ?></td>
    <td><?php echo TEXT_YES ?></td>
    <td><?php echo TEXT_YES ?></td>
    <td><?php echo TEXT_YES ?></td>
  </tr>
    
<?php
    
  $choices = array(''=>TEXT_NO,'view'=>TEXT_VIEW_ACCESS,'view_assigned'=>TEXT_VIEW_ASSIGNED_ACCESS);
  
  $count = 0;
  $groups_query = db_fetch_all('app_access_groups','','sort_order, name');
  while($v = db_fetch_array($groups_query))
  {        
    $count++; 
    
    $access_schema = array('view' => '','create'=>false,'update'=>false,'delete'=>false,'reports'=>false);
    
    $acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $v['id']. "'");
    if($acess_info = db_fetch_array($acess_info_query))
    {
      $schema = explode(',',$acess_info['access_schema']);
            
      if(in_array('view',$schema))
      {
        $access_schema['view'] = 'view'; 
      } 
      
      if(in_array('view_assigned',$schema))
      {
        $access_schema['view'] = 'view_assigned'; 
      }
      
      if(in_array('create',$schema))
      {
        $access_schema['create'] = true;
      } 
       
      if(in_array('update',$schema))
      {
        $access_schema['update'] = true;
      } 
       
      if(in_array('delete',$schema))
      {
        $access_schema['delete'] = true;
      }
      
      if(in_array('reports',$schema))
      {
        $access_schema['reports'] = true;
      }          
    }
               
    echo '
      <tr>
        <td>' . $v['name']. '</td>
        <td>' . select_tag('access[' . $v['id']. '][view]',$choices,$access_schema['view'],array('id'=>'access_' . $v['id'],'class'=>'form-control input-medium','onChange'=>'update_crud_checkboxes(this.value,' . $v['id'] . ')')) . '</td>
        <td>' . input_checkbox_tag('access[' . $v['id']. '][create]','1',array('checked'=>$access_schema['create'],'class'=>'crud_' . $v['id'])) . '</td>
        <td>' . input_checkbox_tag('access[' . $v['id']. '][update]','1',array('checked'=>$access_schema['update'],'class'=>'crud_' . $v['id'])) . '</td>
        <td>' . input_checkbox_tag('access[' . $v['id']. '][delete]','1',array('checked'=>$access_schema['delete'],'class'=>'crud_' . $v['id'])) . '</td>
        <td>' . input_checkbox_tag('access[' . $v['id']. '][reports]','1',array('checked'=>$access_schema['reports'],'class'=>'crud_' . $v['id'])) . '</td>
      </tr>    
    ';    
  }
  
?>
</table>
</div>
<br>
<?php if($count>0) echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form>

<script>
  $(function() {         
    $( ".access_choices" ).each(function() { 
      id = $(this).attr('id').replace('access_','');  
      
      if($(this).val()=='')
      {
        $('.crud_'+id).css('display','none')
      }
    });  
  });  
</script> 





