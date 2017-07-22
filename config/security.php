<?php

/**
 * Google reCAPTCHA
 * If keys entered then reCAPTCHA will be display on login screen
 * You can get keys here https://www.google.com/recaptcha/admin 
 */
define('CFG_RECAPTCHA_ENABLE',false);
define('CFG_RECAPTCHA_KEY','');
define('CFG_RECAPTCHA_SECRET_KEY','');

/**
 * Restricted countries
 * Enter allowed countries list by comma, for example: RU,US
 */
define('CFG_RESTRICTED_COUNTRIES_ENABLE',false);
define('CFG_ALLOWED_COUNTRIES_LIST','');

/**
 * Restriction by IP
 * Enter allowed IP list by comma, for example: 192.168.2.1,192.168.2.2
 */
define('CFG_RESTRICTED_BY_IP_ENABLE',false);
define('CFG_ALLOWED_IP_LIST','');