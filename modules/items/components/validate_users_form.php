<?php

$msg = array();

if(strlen($_POST['fields'][9])==0)
{
  $msg[] = TEXT_ERROR_USEREMAL_EMPTY;
}

if(strlen($_POST['fields'][12])==0)
{
  $msg[] = TEXT_ERROR_USERNAME_EMPTY;
}

if(strlen($_POST['fields'][9])>0 and CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL==0)
{
  $check_query = db_query("select count(*) as total from app_entity_1 where field_9='" . db_input($_POST['fields'][9]) . "' " . (isset($_GET['id']) ? " and id!='" . db_input($_GET['id']) . "'":''));
  $check = db_fetch_array($check_query);
  if($check['total']>0)
  {
    $msg[] = TEXT_ERROR_USEREMAL_EXIST;
  }
}

if(strlen($_POST['fields'][12])>0)
{
  $check_query = db_query("select count(*) as total from app_entity_1 where field_12='" . db_input($_POST['fields'][12]) . "' " . (isset($_GET['id']) ? " and id!='" . db_input($_GET['id']) . "'":''));
  $check = db_fetch_array($check_query);
  if($check['total']>0)
  {
    $msg[] = TEXT_ERROR_USERNAME_EXIST;
  }
}

if(count($msg)>0)
{
  echo implode('<br>',$msg);
  exit(); 
}
