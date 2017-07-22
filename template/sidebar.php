<div class="page-sidebar-wrapper noprint">
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<!-- BEGIN SIDEBAR MENU -->
			<ul class="page-sidebar-menu">
				<li class="sidebar-toggler-wrapper">
          
<?php
  if(is_file(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
  {
    if(is_image(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
    {
    
      $html = '<img src="uploads/' . CFG_APP_LOGO .  '" border="0" title="' . CFG_APP_NAME . '">';
      
      if(strlen(CFG_APP_LOGO_URL)>0)
      {
        $html = '<div class="logo"><a href="' . CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a></div>';
      }
      else
      {
        $html = '<div class="logo"><a href="' . url_for('dashboard/') . '">' . $html . '</a></div>';
      }
      
      echo $html;
    }
  }
?>          
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
					<div class="sidebar-toggler">
					</div>
					<div class="clearfix">
					</div>
					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->
				</li>
        <li>
        
        </li>
				
        <?php
          $sidebarMenu = build_main_menu(); 
          echo renderSidebarMenu($sidebarMenu) 
        ?>
                            
			</ul>
			<!-- END SIDEBAR MENU -->
		</div>
	</div>
</div>

