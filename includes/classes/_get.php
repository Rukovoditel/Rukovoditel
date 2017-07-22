<?php

class _get
{
	static function int($v)
	{
		if(isset($_GET[$v]))
		{
			return (int)$_GET[$v];
		}
		else
		{
			die('Error: $_GET[' . $v . '] is not available!');
		}
	}
}