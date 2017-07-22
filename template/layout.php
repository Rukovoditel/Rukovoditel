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

<link rel="stylesheet" type="text/css" href="js/DataTables-1.10.15/media/css/dataTables.bootstrap.css" />



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
  
  var app_key_ctrl_pressed = false;
  $(window).keydown(function(evt) {if (evt.which == 17) { app_key_ctrl_pressed = true; }}).keyup(function(evt) {if (evt.which == 17) { app_key_ctrl_pressed = false; }});
  
  function keep_session()
  {
    $.ajax({url: '<?php echo url_for("dashboard/","action=keep_session") ?>'});
  }
  
  $(function(){
     setInterval("keep_session()",600000);                                                                   
  }); 
    
  var app_cfg_first_day_of_week = <?php echo CFG_APP_FIRST_DAY_OF_WEEK ?>;
  var app_language_short_code = '<?php echo APP_LANGUAGE_SHORT_CODE ?>';
  var app_cfg_ckeditor_images = '<?php echo url_for("dashboard/ckeditor_image")?>';
  var app_language_text_direction = '<?php echo APP_LANGUAGE_TEXT_DIRECTION ?>'    
      
</script>

<?php plugins::include_part('layout_head') ?>

<link rel="stylesheet" type="text/css" href="css/default.css?v=<?php echo PROJECT_VERSION ?>"/>

<?php 
  $sidebar_pos_option = $app_users_cfg->get('sidebar-pos-option','');
  
  if(APP_LANGUAGE_TEXT_DIRECTION=='rtl')
  {
    require(component_path('dashboard/direction_rtl'));
  } 
?>
  
<!-- END THEME STYLES -->
<link rel="shortcut icon" href="favicon.ico"/>
<link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png">
<link rel="apple-touch-icon" sizes="72x72" href="images/icons/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="114x114" href="images/icons/apple-touch-icon-114x114.png"> 

</head>
<!-- BEGIN BODY -->
<body class="page-header-fixed <?php echo $app_users_cfg->get('sidebar-option','') . ' ' . $sidebar_pos_option . ' ' . $app_users_cfg->get('page-scale-option','') . ' ' . $app_users_cfg->get('sidebar-status') ?>">

<!-- BEGIN HEADER -->
<?php require('template/header.php'); ?>
<!-- END HEADER -->

<div class="clearfix"></div>

<!-- BEGIN CONTAINER -->
<div class="page-container">

<!-- BEGIN SIDEBAR -->
<?php require('template/sidebar.php'); ?>
<!-- END SIDEBAR -->

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
	<div class="page-content-wrapper">
		<div class="page-content">			
			<div id="ajax-modal" class="modal fade" tabindex="-1" data-replace="true" data-keyboard="false" data-backdrop="static" data-focus-on=".autofocus"></div>			
			<!-- BEGIN PAGE CONTENT-->
			<div class="row">
				<div class="col-md-12">                
<?php 
//check install dir
  if(is_dir('install'))
  {
    $alerts->add(TEXT_REMOVE_INSTALL_FOLDER,'warning');
  }

//output alerts if they exists.
  echo $alerts->output(); 
        
//include module views    
  if(is_file($path = $app_plugin_path . 'modules/' . $app_module . '/views/' . $app_action . '.php'))
  {    
    require($path);
  }      
?>					                       
				</div>
			</div>
			<!-- END PAGE CONTENT-->
		</div>
	</div>
</div>
<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->

<!-- BEGIN FOOTER -->
<?php require('template/footer.php'); ?>
<!-- END FOOTER -->

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
<script type="text/javascript" src="js/totop/jquery.ui.totop.js" ></script>
<!-- END PAGE LEVEL PLUGINS -->

<?php if($app_module_path=='items/info') require(component_path('dashboard/data_tables')); ?>

<?php require('js/mapbbcode-master/includes.js.php'); ?>


<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="template/scripts/app.js" type="text/javascript"></script>
<!-- END PAGE LEVEL SCRIPTS -->

<?php plugins::include_part('layout_bottom') ?>

<script>
jQuery(document).ready(function() {     
  App.init();
  
  rukovoditel_app_init();
  
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
  
  
  <?php if(strlen($app_current_version)==0) echo "$.ajax({url: '" . url_for("dashboard/check_project_version") ."'});" ?>
});
</script>

<?php echo i18n_js() ?>

<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>
      