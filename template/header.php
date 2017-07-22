<div class="header navbar navbar-inverse navbar-fixed-top noprint">
	<!-- BEGIN TOP NAVIGATION BAR -->
	<div class="header-inner">
		
		<!-- BEGIN LOGO -->
		<a class="navbar-brand" href="<?php echo url_for('dashboard/')?>">    
			<?php echo CFG_APP_NAME  ?>     
			<?php echo maintenance_mode::header_message() ?>    		
		</a>
		<!-- END LOGO -->
		
		<!-- BEGIN RESPONSIVE MENU TOGGLER -->
		<a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<img src="template/img/menu-toggler.png" alt=""/>
		</a>
		<!-- END RESPONSIVE MENU TOGGLER -->
		<!-- BEGIN TOP NAVIGATION MENU -->		
    <ul class="nav navbar-nav pull-right">
        
<?php
  if(app_session_is_registered('app_current_version')) 
  if(strlen($app_current_version)>0 and $app_current_version>PROJECT_VERSION and $app_user['group_id']==0):
?>			
			<li class="dropdown" id="header_new_release_bar">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				<i class="fa fa-warning"></i>
				<span class="badge badge-warning">1</span>
				</a>
				<ul class="dropdown-menu extended tasks">
					<li>
						<p>
							 <?php echo TEXT_NEW_PROJECT_VERSION ?>
						</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: 80px;">

							<li>
								<a href="http://rukovoditel.net/new_release.php" target="_new"><?php echo sprintf(TEXT_NEW_PROJECT_VERSION_INFO,$app_current_version) ?></a>
							</li>
						</ul>
					</li>
				</ul>
			</li>
<?php endif ?>    


<?php plugins::include_part('header_dropdown_menu') ?>      
        
<?php 
  $hot_reports = new hot_reports();
  echo $hot_reports->render();    
  
  echo users_notifications::render();
?>    
    
			<!-- BEGIN USER LOGIN DROPDOWN -->      
			<li class="dropdown user">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown"  data-hover="dropdown" data-close-others="true">
				                
        <?php echo (is_file(DIR_FS_USERS . $app_user['photo']) ? image_tag(DIR_WS_USERS . $app_user['photo'],array('class'=>'user-photo-header')) : image_tag('images/' . 'no_photo.png',array('class'=>'user-photo-header')) )?>
				<span class="username">
					 <?php echo $app_user['name'] ?>
				</span>
              
				<i class="fa fa-angle-down"></i>
				</a>
                
        <?php echo renderDropDownMenu(build_user_menu()) ?>
                               
  		</li>
  		<!-- END USER LOGIN DROPDOWN -->
  	</ul>  	
	  <!-- END TOP NAVIGATION MENU -->
</div>
<!-- END TOP NAVIGATION BAR -->
</div>
 