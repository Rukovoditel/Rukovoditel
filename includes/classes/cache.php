<?php

class cache
{
  
  public static function create($filename,$content,$folder='/')
  {
    if ($fp = @fopen('cache' . $folder . $filename, 'w')) {
      fputs($fp, $content);
      fclose($fp);
    }
  }
}