<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="<?php echo APP_LANGUAGE_SHORT_CODE ?>" dir="<?php echo APP_LANGUAGE_TEXT_DIRECTION ?>" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $app_title ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="www.rukovoditel.net" name="author"/>
<meta name="MobileOptimized" content="320">


<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
<link href="template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link href="template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.css" rel="stylesheet"/>
<link rel="stylesheet" type="text/css" href="template/plugins/select2/select2_conquer.css"/>
<link href="template/plugins/bootstrap-modal/css/bootstrap-modal-bs3patch.css" rel="stylesheet" type="text/css"/>
<link href="template/plugins/bootstrap-modal/css/bootstrap-modal.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="template/plugins/bootstrap-datepicker/css/datepicker.css"/>
<link rel="stylesheet" type="text/css" href="template/plugins/bootstrap-datetimepicker/css/datetimepicker.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
<link href="template/css/style.css?v=2" rel="stylesheet" type="text/css"/>
<link href="template/css/style-responsive.css?v=2" rel="stylesheet" type="text/css"/>
<link href="template/css/plugins.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="template/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
<link rel="stylesheet" type="text/css" href="js/simple-color-picker/colorPicker.css"/>
<link href="js/uploadifive/uploadifive.css" rel="stylesheet" media="screen">
<link href="js/chosen/chosen.css" rel="stylesheet" media="screen">
<link rel="stylesheet" type="text/css" href="template/plugins/jquery-nestable/jquery.nestable.css"/>

<?php require('js/mapbbcode-master/includes.css.php'); ?>

<link href="css/skins/<?php echo $app_skin ?>" rel="stylesheet" type="text/css" />

<script src="template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>   

<script src="js/jquery.ui.touch-punch/jquery.ui.touch-punch.min.js" type="text/javascript"></script>

<script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/validation/additional-methods.min.js"></script>
<?php require('js/validation/validator_messages.php'); ?> 

<!-- Add fancyBox -->
<link rel="stylesheet" href="js/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
<script type="text/javascript" src="js/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

<script type="text/javascript" src="js/main.js?v=<?php echo PROJECT_VERSION ?>"></script>

<script type="text/javascript">      
  var CKEDITOR = false;
  var CKEDITOR_holders = new Array();
      
  var app_cfg_first_day_of_week = <?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>;
  var app_language_short_code = '<?php echo APP_LANGUAGE_SHORT_CODE ?>';
  var app_cfg_ckeditor_images = '<?php echo url_for("dashboard/ckeditor_image")?>';
  var app_language_text_direction = '<?php echo APP_LANGUAGE_TEXT_DIRECTION ?>'    
      
</script>

<link rel="stylesheet" type="text/css" href="css/default.css?v=<?php echo PROJECT_VERSION ?>"/>

<?php echo render_login_page_background() ?>
<?php echo app_recaptcha::render_js() ?>

<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
</head>
<!-- BEGIN BODY -->
<body class="login">

<div class="login-fade-in"></div>

<!-- BEGIN LOGO -->
<div class="login-page-logo">

<?php
  if(is_file(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
  {
    if(is_image(DIR_FS_UPLOADS  . '/' . CFG_APP_LOGO))
    {
      $html = '<img src="uploads/' . CFG_APP_LOGO .  '" border="0" title="' . CFG_APP_NAME . '">';
      
      if(strlen(CFG_APP_LOGO_URL)>0)
      {
        $html = '<a href="' . CFG_APP_LOGO_URL . '" target="_new">' . $html . '</a>';
      }
      
      echo $html;
    }
  }
  else
  {
    echo CFG_APP_NAME;
  }
?>
	
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content <?php echo 'content-' . $app_action ?>" >

<?php 
  //output alerts if they exists.
  echo $alerts->output(); 
        
//include module views    
  if(is_file($path = 'modules/' . $app_module . '/views/' . $app_action . '.php'))
  {    
    require($path);
  }   
?>

</div>
<!-- END LOGIN -->


<!-- BEGIN COPYRIGHT -->
<div class="copyright">
	 <?php echo (strlen(CFG_APP_COPYRIGHT_NAME)>0 ? '&copy; ' . CFG_APP_COPYRIGHT_NAME . ' ' . date('Y') . '<br>': '') ?>
    <small><?php echo TEXT_POWERED_BY ?>&nbsp;<a target="_blank" href="https://www.rukovoditel.net" title="<?php echo TEXT_POWERED_BY_TITLE ?>">Rukovoditel</a></small>
</div>
<!-- END COPYRIGHT -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="template/plugins/respond.min.js"></script>
<script src="template/plugins/excanvas.min.js"></script> 
<![endif]-->
<script src="template/plugins/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="template/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js" type="text/javascript"></script>
<script src="template/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="template/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.js?v=2.2.1" type="text/javascript"></script>
<script src="template/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="template/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="template/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="template/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="template/plugins/ckeditor/ckeditor.js?v=4.5.7"></script>
<script type="text/javascript" src="template/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="template/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="template/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="template/plugins/bootstrap-modal/js/bootstrap-modalmanager.js" type="text/javascript"></script>
<script type="text/javascript" src="template/plugins/bootstrap-modal/js/bootstrap-modal.js" type="text/javascript"></script>
<script type="text/javascript" src="template/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="template/plugins/jquery-nestable/jquery.nestable.js"></script>
<script type="text/javascript" src="js/simple-color-picker/jquery.colorPicker.js"></script>
<script type="text/javascript" src="js/uploadifive/jquery.uploadifive.min.js"></script>
<script type="text/javascript" src="js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="js/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="js/maskedinput/jquery.maskedinput.js"></script>
<!-- END PAGE LEVEL PLUGINS -->

<?php require('js/mapbbcode-master/includes.js.php'); ?>

<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<script>
jQuery(document).ready(function() {     

	$.fn.datepicker.dates['en'] = {
	    days: [<?php echo TEXT_DATEPICKER_DAYS ?>],
	    daysShort: [<?php echo TEXT_DATEPICKER_DAYSSHORT ?>],
	    daysMin: [<?php echo TEXT_DATEPICKER_DAYSMIN ?>],
	    months: [<?php echo TEXT_DATEPICKER_MONTHS ?>],
	    monthsShort: [<?php echo TEXT_DATEPICKER_MONTHSSHORT ?>],
	    today: "<?php echo TEXT_DATEPICKER_TODAY ?>"    
	};  
	
	$.fn.datetimepicker.dates['en'] = {
	    days: [<?php echo TEXT_DATEPICKER_DAYS ?>],
	    daysShort: [<?php echo TEXT_DATEPICKER_DAYSSHORT ?>],
	    daysMin: [<?php echo TEXT_DATEPICKER_DAYSMIN ?>],
	    months: [<?php echo TEXT_DATEPICKER_MONTHS ?>],
	    monthsShort: [<?php echo TEXT_DATEPICKER_MONTHSSHORT ?>],
	    meridiem: ["am", "pm"],
			suffix: ["st", "nd", "rd", "th"],
	    today: "<?php echo TEXT_DATEPICKER_TODAY ?>"    
	};

  App.init();
  
  rukovoditel_app_init();

	appHandleUniform()
     
});
</script>

<?php echo i18n_js() ?>

</body>
<!-- END BODY -->
</html>