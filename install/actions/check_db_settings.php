<?php
  $server = $_POST['db_host'];
  $port = $_POST['db_port'];
  $username = $_POST['db_username'];
  $password = $_POST['db_password'];
  $database = $_POST['db_name'];
  
  $params = array('db_host'=>$_POST['db_host'],'db_port'=>$_POST['db_port'],'db_username'=>$_POST['db_username'],'db_password'=>$_POST['db_password'],'db_name'=>$_POST['db_name'],'lng'=>$_GET['lng']);
    
  db_connect($server . (strlen($port)>0 ? ':' . $port : ''),$username, $password,$database,'db_link',$params);
      
//check user privileges    
  $user_privileges_list = array();
  $user_privileges_query = db_query("SHOW PRIVILEGES");
  while($user_privileges = db_fetch_array($user_privileges_query))
  {  
    $user_privileges_list[] = $user_privileges['Privilege'];    
  }
              
  $required_privileges = array('Select','Insert','Update','Delete','Create','Drop','Alter');
  
  $missed_privileges = array();
  foreach($required_privileges as $v)
  {
    if(!in_array($v,$user_privileges_list))
    {
      $missed_privileges[] = $v;
    }
  }  
  
  if(count($missed_privileges)>0)
  {
    $error = 'Next privileges: "' . implode(',',$missed_privileges) . '" are required for mysql user.';
    header('Location: index.php?step=database_config&db_error=' . urlencode($error) . '&lng=' .(isset($params['lng']) ? $params['lng']:''). '&params=' . base64_encode(json_encode($params)));
    exit();
  }

