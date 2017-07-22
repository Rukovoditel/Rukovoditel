<?php

$s = plugins::include_menu('extension');
      
if(count($s)>0)
{
  redirect_to('ext/ext/');
}