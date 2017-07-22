<?php echo ajax_modal_template_header(TEXT_ADD) ?>

<?php echo form_tag('prepare_add_item_form', url_for('reports/view','reports_id=' . $_GET['reports_id'] ),array('class'=>'form-horizontal')) ?>

<?php
  $report_info_query = db_query("select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and (r.created_by='" . db_input($app_logged_users_id) . "' or (reports_type = 'common' and find_in_set(" . $app_user['group_id'] . ",users_groups))) and r.id='" . db_input($_GET['reports_id']) . "' order by e.name, r.name");
  $report_info = db_fetch_array($report_info_query);
      
  $entity_info = db_find('app_entities',$report_info['entities_id']);
  $entity_cfg = entities::get_cfg($report_info['entities_id']);
  
  $button_title = (strlen($entity_cfg['insert_button'])>0 ? $entity_cfg['insert_button'] : TEXT_ADD);
  
  $parent_item_id = '';
  
  //prepare default value for dropdown
  if(isset($_GET["related"]))
  {
  	$related = explode('-',$_GET["related"]);
  	$path_info = items::get_path_info($related[0],$related[1]);
  	$parent_item_id = $path_info['full_path'] . '/' . $entity_info['id'];  	  	
  }
?>

<div class="modal-body">
  <div class="form-body">
  
  <div class="ajax-modal-width-790"></div>
  
  <div class="form-group" >
  	<label class="col-md-3 control-label" for="entities_id"><?php echo TEXT_ADD_IN ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('parent_item_id',items::get_choices_by_entity($entity_info['id'],$entity_info['parent_id']),$parent_item_id,array('class'=>'form-control chosen-select required')) ?>
    </div>			
  </div> 
         
   </div>
</div> 
 
<?php echo ajax_modal_template_footer($button_title) ?>

</form>

<script>
  $(function() { 
                  
    $('#prepare_add_item_form').validate({ignore:'',      
      submitHandler: function(form)
      {                              
        path = $('#parent_item_id').val();
        url = '<?php echo url_for("items/form","redirect_to=report_" . $report_info["id"] . (isset($_GET["related"]) ? "&related=" . $_GET["related"]:"")) ?>'+'&path='+path;
            
        open_dialog(url)
        
        return false;                
      }
      });
                                                                        
  });
</script>
