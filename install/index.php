<?php
  error_reporting(E_ALL & ~E_NOTICE);
  
  define('PROJECT_VERSION','1.9');
  
// set default timezone if none exists (PHP 5.3 throws an E_WARNING)
	define('CFG_TIME_ZONE','Europe/Moscow');
	
  if (PHP_VERSION >= '5.2') {
  	date_default_timezone_set(defined('CFG_TIME_ZONE') ? CFG_TIME_ZONE : date_default_timezone_get());
  }
      
  include('lib/database.php');
  
  include('lib/html.php');
  
  if(isset($_GET['step']) and !isset($_GET['lng']))
  {
    header('Location: index.php');
    exit();
  }
  
  if(isset($_GET['lng']))
  {
    include('languages/' . $_GET['lng'] . '.php');
    
    $app_title = sprintf(TEXT_INSTALLATION_HEADING,PROJECT_VERSION);
  }
  else
  {
    $app_title = sprintf('Rukovoditel %s Installation',PROJECT_VERSION);
  }
  
  
       
  if($_GET['step']=='rukovoditel_config') include('actions/check_db_settings.php');
  
  if($_GET['action']=='install_rukovoditel') include('actions/install_rukovoditel.php');
  
?>


<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $app_title ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta name="MobileOptimized" content="320">

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="../template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../template/plugins/select2/select2_conquer.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="../template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
<link href="../template/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../template/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../css/skins/default/default.css" rel="stylesheet" type="text/css" />

<style>
.login .content{
  width: auto;
  max-width: 750px;
}
</style>

<script src="../template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="../js/validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="../js/validation/additional-methods.min.js"></script>
 
<script type="text/javascript" src="../js/main.js"></script>

<link rel="stylesheet" type="text/css" href="../css/default.css"/>

<script type="text/javascript">
  $.extend($.validator.messages, { 
    required: '<?php echo TEXT_FIELD_IS_REQURED ?>',
    email: '<?php echo TEXT_FIELD_IS_REQURED_EMAIL ?>'    
  });
</script> 


<!-- END THEME STYLES -->
<link rel="shortcut icon" href="../favicon.ico"/>
</head>
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="login-page-logo"><?php echo $app_title ?></div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">

<?php if(!$_GET['step']) include('modules/language.php')?>
<?php if($_GET['step']=='checking_environment') include('modules/checking_environment.php')?>
<?php if($_GET['step']=='database_config') include('modules/database_config.php')?>
<?php if($_GET['step']=='rukovoditel_config') include('modules/rukovoditel_config.php')?>
<?php if($_GET['step']=='success') include('modules/success.php')?>

</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 <a href="http://rukovoditel.net">Rukovoditel <?php echo PROJECT_VERSION ?></a><br>
    Copyright &copy; <?php echo date('Y')?> <a href="http://rukovoditel.net">www.rukovoditel.net</a>
</div>
<!-- END COPYRIGHT -->
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="../template/plugins/respond.min.js"></script>
<script src="../template/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="../template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<script src="../template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../template/plugins/bootstrap-hover-dropdown/twitter-bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="../template/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="../template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="../template/plugins/select2/select2.min.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="../template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
jQuery(document).ready(function() {     
  App.init();
});
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
