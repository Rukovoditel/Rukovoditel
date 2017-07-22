
<?php echo ajax_modal_template_header(TEXT_HEADING_REPORTS_IFNO) ?>

<?php echo form_tag('users_groups_form', url_for('reports/reports','action=save' . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
  <div class="form-body ajax-modal-width-790">
  
<ul class="nav nav-tabs" id="form_tabs">  
  <li class="active" ><a data-toggle="tab" href="#form_tab_general"><?php echo TEXT_GENERAL_INFO ?></a></li>
    
<?php if(CFG_NOTIFICATIONS_SCHEDULE==1): ?>  
  <li><a data-toggle="tab" href="#form_tab_notification"><?php echo TEXT_NOTIFICATION ?></a></li>
<?php endif ?>
  
</ul> 

 
<div class="tab-content">
  <div class="tab-pane active" id="form_tab_general">
    
  <div class="form-group">
  	<label class="col-md-4 control-label" for="entities_id"><?php echo TEXT_REPORT_ENTITY ?></label>
    <div class="col-md-8">	
  	  <?php 
        
        $choices = entities::get_choices();
        
        if($app_user['group_id']>0)
        {
          $choices_new = array();
          
          foreach($choices as $k=>$v)
          {
            $acccess_query = db_query("select * from app_entities_access where access_groups_id='" . db_input($app_user['group_id']) . "' and entities_id='" . db_input($k) . "' and find_in_set('reports',access_schema)");
            if($acccess = db_fetch_array($acccess_query))
            {
              $choices_new[$k] = $v;
            }
          }
          
          $choices = $choices_new;
        }
        
        echo select_tag('entities_id',$choices,$obj['entities_id'],array('class'=>'form-control input-large required')) 
      
      ?>
    </div>			
  </div>  
    
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_NAME ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="cfg_menu_title"><?php echo TEXT_MENU_ICON_TITLE; ?></label>
    <div class="col-md-8">	
  	  <?php echo input_tag('menu_icon', $obj['menu_icon'],array('class'=>'form-control input-large')); ?> 
      <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="in_menu"><?php echo TEXT_IN_MENU ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_menu','1',array('checked'=>$obj['in_menu'])) ?></label></div>
    </div>			
  </div>  
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="in_dashboard"><?php echo TEXT_IN_DASHBOARD ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_dashboard','1',array('checked'=>$obj['in_dashboard'])) ?></label></div>
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-4 control-label" for="in_dashboard_counter"><?php echo TEXT_DISPLAY_COUNTER_ON_DASHBOARD ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_dashboard_counter','1',array('checked'=>$obj['in_dashboard_counter'])) ?></label></div>
    </div>			
  </div> 
  
  <div class="form-group">    
  	<label class="col-md-4 control-label" for="in_header"><?php echo tooltip_icon(TEXT_DISPLAY_IN_HEADER_TOOLTIP) . TEXT_DISPLAY_IN_HEADER ?></label>
    <div class="col-md-8">	
  	  <div class="checkbox-list"><label class="checkbox-inline"><?php echo input_checkbox_tag('in_header','1',array('checked'=>$obj['in_header'])) ?></label></div>
    </div>			
  </div> 
  
  
	</div>
	<div class="tab-pane" id="form_tab_notification">
	
		<div class="form-group">
	  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_DAY ?></label>
	    <div class="col-md-8">	
	  	  <?php echo select_checkboxes_tag('notification_days',app_get_days_choices(),$obj['notification_days']) ?>
	    </div>			
	  </div>
	  
	  <div class="form-group">
	  	<label class="col-md-4 control-label" for="name"><?php echo TEXT_TIME ?></label>
	    <div class="col-md-8">	
	  	  <?php echo select_tag('notification_time[]',app_get_hours_choices(),$obj['notification_time'],array('multiple'=>'multiple','class'=>'form-control chosen-select')) ?>
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
    $('#users_groups_form').validate();                                                                  
  });
  
</script>   
    
 
