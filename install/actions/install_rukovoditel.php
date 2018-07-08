<?php
    
  require('../includes/libs/PasswordHash.php');
  
  $hasher = new PasswordHash(11, false);
  
  $sql_file = 'sql/' . $_GET['lng'] . '.sql';
  
  $server = $_POST['db_host'];
  $port = $_POST['db_port'];
  $username = $_POST['db_username'];
  $password = $_POST['db_password'];
  $database = $_POST['db_name'];
       
  db_connect($server. (strlen($port)>0 ? ':' . $port : ''),$username, $password,$database);
             
  if (file_exists($sql_file)) {    
    $fd = fopen($sql_file, 'rb');
    $install_query = fread($fd, filesize($sql_file));
    fclose($fd);    
  } else {
    echo 'SQL file does not exist: ' . $sql_file;
    exit();
  }
  
  
$insert_query = "
INSERT INTO app_configuration VALUES
('11','CFG_APP_LOGO',''),
('10','CFG_APP_SHORT_NAME','" . db_input($_POST['app_short_name']) . "'),
('9','CFG_APP_NAME','" . db_input($_POST['app_name']) . "'),
('12','CFG_EMAIL_USE_NOTIFICATION','1'),
('13','CFG_EMAIL_SUBJECT_LABEL',NULL),
('14','CFG_EMAIL_AMOUNT_PREVIOUS_COMMENTS','2'),
('15','CFG_EMAIL_COPY_SENDER','0'),
('16','CFG_EMAIL_SEND_FROM_SINGLE','0'),
('17','CFG_EMAIL_ADDRESS_FROM','" . db_input($_POST['email_address_from']) . "'),
('18','CFG_EMAIL_NAME_FROM','" . db_input($_POST['email_name_from']) . "'),
('19','CFG_EMAIL_USE_SMTP','0'),
('20','CFG_EMAIL_SMTP_SERVER',NULL),
('21','CFG_EMAIL_SMTP_PORT',NULL),
('22','CFG_EMAIL_SMTP_ENCRYPTION',NULL),
('23','CFG_EMAIL_SMTP_LOGIN',NULL),
('24','CFG_EMAIL_SMTP_PASSWORD',NULL),
('25','CFG_LDAP_USE','0'),
('26','CFG_LDAP_SERVER_NAME',NULL),
('27','CFG_LDAP_SERVER_PORT',NULL),
('28','CFG_LDAP_BASE_DN',NULL),
('29','CFG_LDAP_UID',NULL),
('30','CFG_LDAP_USER',NULL),
('31','CFG_LDAP_EMAIL_ATTRIBUTE',NULL),
('32','CFG_LDAP_USER_DN',NULL),
('33','CFG_LDAP_PASSWORD',NULL),
('34','CFG_LOGIN_PAGE_HEADING',''),
('35','CFG_LOGIN_PAGE_CONTENT',''),
('36','CFG_APP_TIMEZONE','" . db_input($_POST['app_time_zone']) . "'),
('37','CFG_APP_DATE_FORMAT','m/d/Y'),
('38','CFG_APP_DATETIME_FORMAT','m/d/Y H:i'),
('39','CFG_APP_ROWS_PER_PAGE','10'),
('40','CFG_REGISTRATION_EMAIL_SUBJECT',NULL),
('41','CFG_REGISTRATION_EMAIL_BODY',NULL),
('42','CFG_PASSWORD_MIN_LENGTH','5'),
('43','CFG_APP_LANGUAGE','" . $_GET['lng'] . ".php'),
('44','CFG_APP_SKIN',NULL),
('45','CFG_PUBLIC_USER_PROFILE_FIELDS','');

INSERT INTO app_entity_1 VALUES
('1','0','0','0','" . time() . "',NULL,'0','" . $hasher->HashPassword($_POST['user_password']) . "','1','0','" . db_input($_POST['fields'][7]) . "','" . db_input($_POST['fields'][8]) . "','" . db_input($_POST['fields'][9]) . "','','" . db_input($_POST['fields'][12]) . "','" . $_GET['lng'] . ".php','blue');
";  
     
  $install_query .= $insert_query;
  
  $install_query_array = explode(';',$install_query);
  
  //echo '<pre>';
  //print_r($install_query_array);
  //exit();
  
  foreach($install_query_array as $query)
  {
    if(strlen(trim($query))>0)db_query(trim($query));
  }
  

  $db_config = "<?php

// define database connection
  define('DB_SERVER', '" . $server . (strlen($port)>0 ? ':' . $port :'') . "'); // eg, localhost - should not be empty for productive servers
  define('DB_SERVER_USERNAME', '" . $username . "');
  define('DB_SERVER_PASSWORD', '" . $password . "');
  define('DB_DATABASE', '" . $database. "');
  	  
  ";
    
  if(is_file('../config/database.php'))
  {
    @unlink('../config/database.php');
  }
  
  file_put_contents('../config/database.php',$db_config,FILE_TEXT|FILE_APPEND|LOCK_EX);
      
  header('Location: index.php?step=success&lng=' . $_GET['lng']);
  
  exit();
