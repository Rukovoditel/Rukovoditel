
<?php echo ajax_modal_template_header(TEXT_SAVE_FILTERS) ?>

<?php echo form_tag('users_filters', url_for('reports/users_filters','action=save&reports_id=' . $_GET['reports_id'] ),array('class'=>'form-horizontal')) ?>

<?php echo input_hidden_tag('redirect_to',$app_redirect_to) ?>
<?php if(isset($_GET['path'])) echo input_hidden_tag('path',$_GET['path']) ?>
<?php
  $users_filters = new users_filters($_GET['reports_id']);
?>

<div class="modal-body">
  <div class="form-body">
  
  
<ul class="nav nav-tabs">
  <li class="active"><a href="#add_filter"  data-toggle="tab"><?php echo TEXT_ADD ?></a></li>
  <?php if($users_filters->count()>0): ?>
  <li><a href="#update_filter"  data-toggle="tab"><?php echo TEXT_UPDATE ?></a></li>
  <?php endif ?>    
</ul>

<div class="tab-content">
  <div class="tab-pane fade active in" id="add_filter">  
     
  <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo  input_tag('name','',array('class'=>'form-control required')) ?>
    </div>			
  </div> 
  
  </div>
  <div class="tab-pane fade" id="update_filter">
    <div class="form-group">
  	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-9">	
  	  <?php echo  select_tag('filters_id',$users_filters->get_choices(true),'',array('class'=>'form-control required')) ?>
    </div>			
  </div> 
  </div>  
</div>
            
   </div>
</div> 
 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#users_filters').validate();                                                                                 
  });     
</script>  

    
 
