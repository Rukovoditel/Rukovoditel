


<div class="row">
	<div class="col-md-12">
    
  <ul class="page-breadcrumb breadcrumb noprint">
    <?php echo items::render_breadcrumb($app_breadcrumb) ?>
  </ul>
  
  
<?php if(count($navbar = items::build_menu())>1): ?>
  <div class="navbar navbar-default navbar-items" role="navigation">
  	<!-- Brand and toggle get grouped for better mobile display -->
  	<div class="navbar-header">
  		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
    		<span class="sr-only"></span>
    		<span class="fa fa-bar "></span>
    		<span class="fa fa-bar fa-align-justify"></span>
    		<span class="fa fa-bar"></span>
  		</button>
  		<a class="navbar-brand <?php echo ($navbar[0]['selected_id'] == $current_entity_id ? 'selected':'')?>" href="<?php echo $navbar[0]['url'] ?>"><?php echo $navbar[0]['title'] ?></a>
  	</div>
  	<!-- Collect the nav links, forms, and other content for toggling -->
  	<div class="collapse navbar-collapse navbar-ex1-collapse">
    
       <?php
        unset($navbar[0]);
         
        echo renderNavbarMenu($navbar,'',0,$current_entity_id); 
       ?>
       
  	</div>
  	<!-- /.navbar-collapse -->
  </div>
<?php endif ?>  
  
  
  <?php if($current_item_id==0): ?>
    <h3 class="page-title"><?php echo $app_breadcrumb[count($app_breadcrumb)-1]['title'] ?></h3>
  <?php endif ?>    

  </div>
</div>


