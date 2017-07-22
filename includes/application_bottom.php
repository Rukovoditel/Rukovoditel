<?php
      
  if(DEV_MODE)
  {                
    $db_log = '';
    $count = 1;
    foreach($app_db_query_log as $v)
    {
      $db_log .= $count . '. ' . $v . "\n";
      $count++; 
    }
    
    $post_log = '';
    foreach($_POST as $k=>$v)
    {
      $post_log .= $k .'=' . $v . '; ';
    }
    
    $content = $_SERVER['REQUEST_URI'] . "\n"  . (strlen($post_log)>0 ? '$_POST' . "\t" . $post_log . "\n":''). $db_log;
    $errfile=fopen("log/db_log.txt","a"); 
    fputs($errfile, $content. "\n\n"); 
    fclose($errfile);
        
  }   
  
