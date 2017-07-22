<?php

class session
{
  public static function get($key, $default='')
  {
    if(isset($_SESSION[$key]))
    {
      return $_SESSION[$key]; 
    }
    else
    {
      return $default;
    }
  }
}