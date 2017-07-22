

<form name="configuration" id="configuration" action="index.php?step=rukovoditel_config&action=install_rukovoditel&lng=<?php echo $_GET['lng']?>" method="post"  class="form-horizontal">

<h3 class="form-section"><?php echo TEXT_GENERAL_CONFIGURATION?></h3>

  <div class="form-group">
  	<label class="col-md-3 control-label" for="app_name"><?php echo TEXT_APP_NAME ?></label>
    <div class="col-md-9">	
  	  <input type="text" name="app_name" id="app_name" value="" class="form-control input-medium required">      
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="app_short_name"><?php echo TEXT_APP_SHORT_NAME ?></label>
    <div class="col-md-9">	
  	  <input type="text" name="app_short_name" id="app_short_name" value="" class="form-control input-medium required">      
    </div>			
  </div>
  
   
<?php
  $timezone_list = array();
  $timezone_identifiers = DateTimeZone::listIdentifiers();
  for ($i=0; $i < sizeof($timezone_identifiers); $i++) 
  {    
      $timezone_list[$timezone_identifiers[$i]] = $timezone_identifiers[$i];
  }  
  
  if($_GET['lng']=='russian')
  {
    $time_zone = 'Europe/Moscow';
  }
  else
  {
    $time_zone = 'America/New_York';
  }
?> 

  <div class="form-group">
  	<label class="col-md-3 control-label" for="app_time_zone"><?php echo TEXT_TIME_ZONE ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('app_time_zone',$timezone_list,  $time_zone,array('class'=>'form-control input-large')); ?>
    </div>			
  </div>  
  
  

<h3 class="form-section"><?php echo TEXT_ADMINISTRATOR ?></h3>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="fields_12"><?php echo TEXT_USERNAME ?></label>
    <div class="col-md-9">	
  	  <input name="fields[12]" id="fields_12" value="" type="text" class="form-control input-medium required">              
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="user_password"><?php echo TEXT_PASSWORD ?></label>
    <div class="col-md-9">	
  	  <input name="user_password" id="user_password" value="" type="password" class="form-control input-medium required">              
    </div>			
  </div>   
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="fields_7"><?php echo TEXT_FIRST_NAME ?></label>
    <div class="col-md-9">	
  	  <input name="fields[7]" id="fields_7" value="" type="text" class="form-control input-medium required">              
    </div>			
  </div>
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="fields_8"><?php echo TEXT_LAST_NAME ?></label>
    <div class="col-md-9">	
  	  <input name="fields[8]" id="fields_8" value="" type="text" class="form-control input-medium required">              
    </div>			
  </div> 
  
  <div class="form-group">
  	<label class="col-md-3 control-label" for="fields_9"><?php echo TEXT_USER_EMAIL ?></label>
    <div class="col-md-9">	
  	  <input name="fields[9]" id="fields_9" value="" type="text" class="form-control input-medium required email">              
    </div>			
  </div>  
  
<h3 class="form-section"><?php echo TEXT_TECHNICAL_SUPPORT ?></h3>
  
<p><?php echo TEXT_TECHNICAL_SUPPORT_INFO ?></p>

	<div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_NAME_FROM"><?php echo TEXT_EMAIL_NAME_FROM ?></label>
    <div class="col-md-9">	  	  
  	  <input name="email_name_from" id="email_name_from" value="noreply" type="text" class="form-control input-medium required">
    </div>			
  </div>
       
  <div class="form-group">
  	<label class="col-md-3 control-label" for="CFG_EMAIL_ADDRESS_FROM"><?php echo TEXT_EMAIL_ADDRESS_FROM ?></label>
    <div class="col-md-9">	  	  
  	  <input name="email_address_from" id="email_address_from" value="noreply@noreply.com" type="text" class="form-control input-medium required email">
    </div>			
  </div>    


<div><input type="submit" value="<?php echo TEXT_BUTTON_INSTALL ?>" class="btn btn-primary"></div>

<input type="hidden" name="db_host"  value="<?php echo trim(addslashes($_POST['db_host']))?>">
<input type="hidden" name="db_port" value="<?php echo trim(addslashes($_POST['db_port']))?>">
<input type="hidden" name="db_name" value="<?php echo trim(addslashes($_POST['db_name']))?>">
<input type="hidden" name="db_username" value="<?php echo trim(addslashes($_POST['db_username']))?>">
<input type="hidden" name="db_password" value="<?php echo trim(addslashes($_POST['db_password']))?>">

</form>

<script>
  $('#configuration').validate();
</script>
 
