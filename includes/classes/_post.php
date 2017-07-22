<?php

class _post
{
	static function int($v)
	{
		if(isset($_POST[$v]))
		{
			return (int)$_POST[$v];
		}
		else
		{
			die('Error: $_POST[' . $v . '] is not available!');
		}
	}
}