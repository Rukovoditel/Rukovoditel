<?php echo ajax_modal_template_header(TEXT_CONFIGURE_DASHBOARD) ?>

<?php echo form_tag('dashboard', url_for('dashboard/reports','action=save&id=' . _get::int('id'))) ?>

<?php 
	$reports_groups_info = db_find('app_reports_groups',_get::int('id'));
	$reports_list = (strlen($reports_groups_info['reports_list']) ? $reports_groups_info['reports_list'] : 0);
	$counters_list = (strlen($reports_groups_info['counters_list']) ? $reports_groups_info['counters_list'] : 0);
?>

<div class="modal-body ajax-modal-width-790">

<ul class="nav nav-tabs" id="form_tabs">
  <li class="active" ><a data-toggle="tab" href="#form_tab_standard_reports"><?php echo TEXT_STANDARD_REPORTS ?></a></li>
  <li><a data-toggle="tab" href="#form_tab_reports_sections"><?php echo TEXT_SECTIONS ?></a></li>
  <li><a data-toggle="tab" href="#form_tab_reports_counter"><?php echo TEXT_COUNTERS ?></a></li>  
</ul>

<div class="tab-content">
  <div class="tab-pane active" id="form_tab_standard_reports">
  
<div><?php echo TEXT_CONFIGURE_DASHBOARD_INFO ?></div><br>

<table style="width: 100%; max-width: 960px;">
  <tr>
    <td valign="top" width="50%">
      <fieldset>
        <legend><?php echo TEXT_REPORTS_ON_DASHBOARD ?></legend>
<div class="cfg_listing">        
  <ul id="reports_on_dashboard" class="sortable sortable-reports">
  <?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and r.id in (" . $reports_list . ") order by field(r.id,{$reports_list})");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    
    </td>
    <td style="padding-left: 25px;" valign="top">
    
      <fieldset>
        <legend><?php echo TEXT_MY_REPORTS ?></legend>
<div class="cfg_listing">        
<ul id="reports_excluded_from_dashboard" class="sortable sortable-reports">
<?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard'and r.id not in (" . $reports_list . ") order by e.name, r.name");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
?> 
</ul>
</div>                     
      </fieldset>
      
      
    </td>
  </tr>
</table>

  </div>
  
  <div class="tab-pane" id="form_tab_reports_sections">
  	<div><?php echo TEXT_CONFIGURE_DASHBOARD_SECTION_INFO ?></div><br>
  	<div style="margin-bottom: 15px;"><bubtton type="button" class="btn btn-primary btn-add-reports-section"><?php echo TEXT_ADD_SECTION ?></bubtton></div>
  	<div id="reports_sections_list"></div>
  </div>
  
  <div class="tab-pane" id="form_tab_reports_counter">
  
<div><?php echo TEXT_CONFIGURE_DASHBOARD_INFO ?></div><br>

<table style="width: 100%; max-width: 960px;">
  <tr>
    <td valign="top" width="50%">
      <fieldset>
        <legend><?php echo TEXT_REPORTS_ON_DASHBOARD ?></legend>
<div class="cfg_listing">        
  <ul id="reports_counter_on_dashboard" class="sortable sortable-reports-counter">
  <?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and r.id in (" . $counters_list . ") order by field(r.id,{$counters_list})");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
  ?> 
  </ul>         
</div>
              
      </fieldset>
    
    </td>
    <td style="padding-left: 25px;" valign="top">
    
      <fieldset>
        <legend><?php echo TEXT_MY_REPORTS ?></legend>
<div class="cfg_listing">        
<ul id="reports_counter_excluded_from_dashboard" class="sortable sortable-reports-counter">
<?php
  $reports_query = db_query("select r.*,e.name as entities_name from app_reports r, app_entities e where e.id=r.entities_id and r.created_by='" . db_input($app_logged_users_id) . "' and r.reports_type='standard' and r.id not in (" . $counters_list . ") order by e.name, r.name");
  while($v = db_fetch_array($reports_query))
  {
    echo '<li id="report_' . $v['id'] . '"><div>' . $v['name'] . '</div></li>';
  }
?> 
</ul>
</div>                     
      </fieldset>
      
      
    </td>
  </tr>
</table>

  </div>
  

  </div>
</div>

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() {      
       
    	$( "ul.sortable-reports" ).sortable({
    		connectWith: "ul",
    		update: function(event,ui){  
          data = '';  
          $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
          data = data.slice(1)                      
          $.ajax({type: "POST",url: '<?php echo url_for("dashboard/reports","action=sort_reports&id=" . _get::int('id'))?>',data: data});
        }
    	});
             
	  	$( "ul.sortable-reports-counter" ).sortable({
	  		connectWith: "ul",
	  		update: function(event,ui){  
	        data = '';  
	        $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
	        data = data.slice(1)                      
	        $.ajax({type: "POST",url: '<?php echo url_for("dashboard/reports","action=sort_reports_counter&id=" . _get::int('id'))?>',data: data});
	      }
	  	});  


	  	//handle sections
	  	$('#reports_sections_list').load("<?php echo url_for('dashboard/reports','action=get_sections&id=' . _get::int('id')) ?>")
	  	
	  	$('.btn-add-reports-section').click(function(){
				$('#reports_sections_list').load("<?php echo url_for('dashboard/reports','action=add_section&id=' . _get::int('id')) ?>")
		  })		  
	  	 
	});  

  function reports_section_delete(section_id)
  {
  	$('#section_panel_'+section_id).fadeOut();
  	$.ajax({type: "POST",url: '<?php echo url_for("dashboard/reports","action=delete_section&id=" . _get::int('id'))?>',data: {section_id:section_id}});
  }

  function reports_section_edit(section_id,type,value)
  {
  	$.ajax({type: "POST",url: '<?php echo url_for("dashboard/reports","action=edit_section&id=" . _get::int('id'))?>',data: {section_id:section_id,type:type,value:value}}).done(function(msg){
				if(msg.length>0)
				{
					alert(msg)
					$('#'+type+'_section'+section_id).val('');
				}
    	});
  }
	  
</script>