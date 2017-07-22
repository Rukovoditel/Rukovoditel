<!-- BEGIN STYLE CUSTOMIZER -->

<div class="dashboard-reports-config hidden-xs hidden-sm">
	<div class="toggler" title="<?php echo TEXT_CONFIGURE_DASHBOARD ?>" onClick="open_dialog('<?php echo url_for('dashboard/configure') ?>')">
		<i class="fa fa-bars"></i>
	</div>
</div>  

<div class="theme-panel hidden-xs hidden-sm">
	<div class="toggler" title="<?php echo TEXT_CONFIGURE_THEME ?>">
		<i class="fa fa-gear"></i>
	</div>
	<div class="theme-options">
		<div class="theme-option theme-colors clearfix">
			<span>
				 <?php echo TEXT_CONFIGURE_THEME ?>
			</span>
		</div>
		<div class="theme-option">
			<span>
				 <?php echo TEXT_SIDEBAR ?>
			</span>
      <?php echo  select_tag('sidebar-option',array('default'=>TEXT_DEFAULT,'fixed'=>TEXT_SIDEBAR_FIXED),($app_users_cfg->get('sidebar-option')=='page-sidebar-fixed' ? 'fixed':'default'),array('class'=>'sidebar-option form-control input-medium','onChange'=>"set_user_cfg('sidebar-option',this.value)")) ?>

		</div>
    
<?php if(APP_LANGUAGE_TEXT_DIRECTION=='ltr'): ?>    
		<div class="theme-option">
			<span>
				 <?php echo TEXT_SIDEBAR_POSITION ?>
			</span>
      <?php echo select_tag('sidebar-pos-option',array('left'=>TEXT_SIDEBAR_POS_LEFT,'right'=>TEXT_SIDEBAR_POS_RIGHT),($app_users_cfg->get('sidebar-pos-option')=='page-sidebar-reversed' ? 'right':'left'),array('class'=>'sidebar-pos-option form-control input-medium','onChange'=>"set_user_cfg('sidebar-pos-option',this.value)")) ?>			
		</div>
<?php endif ?>
    
		<div class="theme-option">
			<span>
				 <?php echo TEXT_SCALE ?>
			</span>
      <?php echo select_tag('page-scale-option',array('default'=>TEXT_DEFAULT,'reduced'=>TEXT_SCALE_REDUCED),($app_users_cfg->get('page-scale-option')=='page-scale-reduced' ? 'reduced':'default'),array('class'=>'scale-option  form-control input-medium','onChange'=>"set_user_cfg('page-scale-option',this.value)")) ?>
		</div>
	</div>
</div>
<!-- END BEGIN STYLE CUSTOMIZER -->

<script>
  function set_user_cfg(key,value)
  {
    switch(key)
    {
      case 'sidebar-option':
          if(value=='fixed') value = 'page-sidebar-fixed'; else value = '';
        break;
      case 'sidebar-pos-option':
          if(value=='right') value = 'page-sidebar-reversed'; else value = '';
        break;
      case 'page-scale-option':
          if(value=='reduced') value = 'page-scale-reduced'; else value = '';
        break;
    }
          
    $.ajax({
      method: "POST",
      url: "<?php echo url_for('users/account','action=set_cfg')?>",
      data: { key: key, value: value }
    })
  }

  $('.sidebar-toggler').click(function(){
    if($('body').hasClass('page-sidebar-closed'))
    {
    	set_user_cfg('sidebar-status',''); 
    } 
    else
    {
    	set_user_cfg('sidebar-status','page-sidebar-closed')
    }   
  })
</script>