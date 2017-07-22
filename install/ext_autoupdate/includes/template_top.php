<?php

  error_reporting(E_ALL & ~E_NOTICE);
          
  require('../../config/database.php');
          
  include('includes/database.php');
      
  db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
  
  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    
  switch($lang)
  {
    case 'ru':
        define('TEXT_APP_TITLE','Руководитель Дополнение | Автоматическое обновление базы данных  с ' . TEXT_UPDATE_VERSION_FROM .' до ' . TEXT_UPDATE_VERSION_TO);
        define('TEXT_PROCESSING','Обработка...');      
        define('TEXT_UPDATE_COMPLATED','Обновление завершено');
        define('TEXT_UPDATE_ALREADY_RUN','Вы уже выполнили это обновление');
      break;
    default:
        define('TEXT_APP_TITLE','Rukovoditel Extension | Database Auto Update from ' . TEXT_UPDATE_VERSION_FROM . ' to' . TEXT_UPDATE_VERSION_TO);
        define('TEXT_PROCESSING','Processing...');
        define('TEXT_UPDATE_COMPLATED','Update completed');
        define('TEXT_UPDATE_ALREADY_RUN','You have already run this update');
      break;
  }
  
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
<title><?php echo  TEXT_APP_TITLE ?></title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<meta content="" name="description"/>
<meta content="" name="author"/>
<meta name="MobileOptimized" content="320">

<!-- BEGIN GLOBAL MANDATORY STYLES -->
<link href="../../template/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<link href="../../template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
<link href="../../template/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
<!-- END GLOBAL MANDATORY STYLES -->
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" type="text/css" href="../../template/plugins/select2/select2_conquer.css"/>
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME STYLES -->
<link href="../../template/css/style-conquer.css" rel="stylesheet" type="text/css"/>
<link href="../../template/css/style.css" rel="stylesheet" type="text/css"/>
<link href="../../template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
<link href="../../template/css/plugins.css" rel="stylesheet" type="text/css"/>
<link href="../../css/skins/default/default.css" rel="stylesheet" type="text/css" />

<style>
.login .content{
  width: auto;
  max-width: 750px;
}
</style>

<script src="../../template/plugins/jquery-1.10.2.min.js" type="text/javascript"></script>

<script type="text/javascript" src="../../js/validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../js/validation/additional-methods.min.js"></script>
 
<script type="text/javascript" src="../../js/main.js"></script>

<link rel="stylesheet" type="text/css" href="../../css/default.css"/>


<!-- END THEME STYLES -->
<link rel="shortcut icon" href="../../favicon.ico"/>
</head>
<!-- BEGIN BODY -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="login-page-logo"><?php echo TEXT_APP_TITLE ?></div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">  

