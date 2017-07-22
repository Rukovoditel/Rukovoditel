<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_entities',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_entities');
  $obj['sort_order']=0;
}
