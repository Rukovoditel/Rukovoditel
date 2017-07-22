<?php

class app_recaptcha
{
	static function is_enabled()
	{
		if(strlen(CFG_RECAPTCHA_KEY) and strlen(CFG_RECAPTCHA_SECRET_KEY) and CFG_RECAPTCHA_ENABLE==true)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	static function render_js()
	{
		if(self::is_enabled())
		{
			return '<script src="https://www.google.com/recaptcha/api.js?hl='. APP_LANGUAGE_SHORT_CODE . '"></script>';
		}
		else
		{
			return '';
		}
	}
	
	static function render()
	{		
		return '<div class="g-recaptcha" data-sitekey="' . CFG_RECAPTCHA_KEY . '"></div>';		
	}
	
	static function verify()
	{
		require('includes/libs/ReCaptcha/ReCaptcha.php');
		require('includes/libs/ReCaptcha/RequestMethod.php');
		require('includes/libs/ReCaptcha/RequestParameters.php');
		require('includes/libs/ReCaptcha/Response.php');
		require('includes/libs/ReCaptcha/RequestMethod/Curl.php');
		require('includes/libs/ReCaptcha/RequestMethod/CurlPost.php');
		
		$recaptcha = new \ReCaptcha\ReCaptcha(CFG_RECAPTCHA_SECRET_KEY);
		$resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
		
		//print_r($resp->getErrorCodes());		
		//exit();
		
		return $resp->isSuccess();
	}
}