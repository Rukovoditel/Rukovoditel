<?php 

	require('config/server.php');
	require('config/security.php');
	require('config/database.php');

// set the level of error reporting
	if(DEV_MODE)
	{
		error_reporting(E_ALL);
	}
	else
	{
		error_reporting(E_ALL & ~E_NOTICE);
	}
	
	$app_db_query_log = array();

//include classes
	require('includes/classes/backup.php');
	require('includes/classes/alerts.php');
	require('includes/classes/attachments.php');
	require('includes/classes/cache.php');
	require('includes/classes/fields_types.php');
	require('includes/classes/fields_types_cfg.php');
	require('includes/classes/related_records.php');
	require('includes/classes/items.php');
	require('includes/classes/items_search.php');
	require('includes/classes/items_listing.php');
	require('includes/classes/ldap_login.php');
	require('includes/classes/split_page.php');
	require('includes/classes/users/users.php');
	require('includes/classes/users/users_cfg.php');
	require('includes/classes/users/users_notifications.php');
	require('includes/classes/plugins.php');	
	require('includes/classes/session.php');
	require('includes/classes/entities_cfg.php');
	require('includes/classes/listing_search.php');
	require('includes/classes/maintenance_mode.php');
	require('includes/classes/app_recaptcha.php');
	require('includes/classes/app_restricted_countries.php');
	require('includes/classes/app_restricted_ip.php');
	require('includes/classes/_get.php');
	require('includes/classes/_post.php');

//include field types
	require('includes/classes/fieldstypes/fieldtype_action.php');
	require('includes/classes/fieldstypes/fieldtype_attachments.php');
	require('includes/classes/fieldstypes/fieldtype_checkboxes.php');
	require('includes/classes/fieldstypes/fieldtype_created_by.php');
	require('includes/classes/fieldstypes/fieldtype_date_added.php');
	require('includes/classes/fieldstypes/fieldtype_dropdown.php');
	require('includes/classes/fieldstypes/fieldtype_dropdown_multiple.php');
	require('includes/classes/fieldstypes/fieldtype_progress.php');
	require('includes/classes/fieldstypes/fieldtype_entity.php');
	require('includes/classes/fieldstypes/fieldtype_formula.php');
	require('includes/classes/fieldstypes/fieldtype_grouped_users.php');
	require('includes/classes/fieldstypes/fieldtype_id.php');
	require('includes/classes/fieldstypes/fieldtype_parent_item_id.php');
	require('includes/classes/fieldstypes/fieldtype_input.php');
	require('includes/classes/fieldstypes/fieldtype_input_date.php');
	require('includes/classes/fieldstypes/fieldtype_input_datetime.php');
	require('includes/classes/fieldstypes/fieldtype_input_file.php');
	require('includes/classes/fieldstypes/fieldtype_input_numeric.php');
	require('includes/classes/fieldstypes/fieldtype_input_numeric_comments.php');
	require('includes/classes/fieldstypes/fieldtype_input_url.php');
	require('includes/classes/fieldstypes/fieldtype_radioboxes.php');
	require('includes/classes/fieldstypes/fieldtype_textarea.php');
	require('includes/classes/fieldstypes/fieldtype_textarea_wysiwyg.php');
	require('includes/classes/fieldstypes/fieldtype_users.php');
	require('includes/classes/fieldstypes/fieldtype_user_accessgroups.php');
	require('includes/classes/fieldstypes/fieldtype_user_email.php');
	require('includes/classes/fieldstypes/fieldtype_user_firstname.php');
	require('includes/classes/fieldstypes/fieldtype_user_language.php');
	require('includes/classes/fieldstypes/fieldtype_user_lastname.php');
	require('includes/classes/fieldstypes/fieldtype_user_photo.php');
	require('includes/classes/fieldstypes/fieldtype_user_skin.php');
	require('includes/classes/fieldstypes/fieldtype_user_status.php');
	require('includes/classes/fieldstypes/fieldtype_user_username.php');
	require('includes/classes/fieldstypes/fieldtype_related_records.php');
	require('includes/classes/fieldstypes/fieldtype_input_masked.php');
	require('includes/classes/fieldstypes/fieldtype_image.php');
	require('includes/classes/fieldstypes/fieldtype_boolean.php');
	require('includes/classes/fieldstypes/fieldtype_text_pattern.php');
	require('includes/classes/fieldstypes/fieldtype_input_vpic.php');
	require('includes/classes/fieldstypes/fieldtype_mapbbcode.php');
	require('includes/classes/fieldstypes/fieldtype_barcode.php');
	require('includes/classes/fieldstypes/fieldtype_qrcode.php');

//include models
	require('includes/classes/model/access_groups.php');
	require('includes/classes/model/comments.php');
	require('includes/classes/model/entities.php');
	require('includes/classes/model/fields.php');
	require('includes/classes/model/fields_choices.php');
	require('includes/classes/model/forms_tabs.php');
	require('includes/classes/model/comments_forms_tabs.php');
	require('includes/classes/model/choices_values.php');
	require('includes/classes/model/global_lists.php');

	require('includes/classes/reports/reports.php');
	require('includes/classes/reports/hot_reports.php');
	require('includes/classes/reports/filters_preview.php');
	require('includes/classes/reports/users_filters.php');
	require('includes/classes/reports/reports_counter.php');
	require('includes/classes/reports/reports_notification.php');

//include functions
	require('includes/functions/app.php');
	require('includes/functions/database.php');
	require('includes/functions/html.php');
	require('includes/functions/menu.php');
	require('includes/functions/sessions.php');
	require('includes/functions/urls.php');
	require('includes/functions/validations.php');

//include libs
	require('includes/libs/PasswordHash.php');
	require('includes/libs/PHPMailer/PHPMailerAutoload.php');
	require('includes/libs/PHPMailer/extras/Html2Text.php');
	require('includes/libs/htmlpurifier-4.8.0/library/HTMLPurifier.auto.php');
	require('includes/libs/php-barcode-generator-master/src/BarcodeGenerator.php');
	require('includes/libs/php-barcode-generator-master/src/BarcodeGeneratorPNG.php');
	require('includes/libs/phpqrcode-master/qrlib.php');	

//set custom error handler
	if(DEV_MODE)
	{
		set_error_handler('app_error_handler');
	}

// make a connection to the database...
	db_connect();

// set the application parameters
	$cfg_query = db_fetch_all('app_configuration');
	while ($v = db_fetch_array($cfg_query))
	{
		define($v['configuration_name'], $v['configuration_value']);
	}

//configuration added in next versions
	if(!defined('CFG_APP_FIRST_DAY_OF_WEEK')) define('CFG_APP_FIRST_DAY_OF_WEEK',0);
	if(!defined('CFG_APP_LOGIN_PAGE_BACKGROUND')) define('CFG_APP_LOGIN_PAGE_BACKGROUND','');
	if(!defined('CFG_APP_DISPLAY_USER_NAME_ORDER')) define('CFG_APP_DISPLAY_USER_NAME_ORDER','firstname_lastname');
	if(!defined('CFG_APP_COPYRIGHT_NAME')) define('CFG_APP_COPYRIGHT_NAME','');
	if(!defined('CFG_APP_NUMBER_FORMAT')) define('CFG_APP_NUMBER_FORMAT','2/./*');
	if(!defined('CFG_APP_LOGO_URL')) define('CFG_APP_LOGO_URL','');
	if(!defined('CFG_ALLOW_CHANGE_USERNAME')) define('CFG_ALLOW_CHANGE_USERNAME',0);
	if(!defined('CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL')) define('CFG_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL',0);
	if(!defined('CFG_MAINTENANCE_MODE')) define('CFG_MAINTENANCE_MODE',0);
	if(!defined('CFG_MAINTENANCE_MESSAGE_HEADING')) define('CFG_MAINTENANCE_MESSAGE_HEADING','');
	if(!defined('CFG_MAINTENANCE_MESSAGE_CONTENT')) define('CFG_MAINTENANCE_MESSAGE_CONTENT','');
	if(!defined('CFG_APP_LOGIN_MAINTENANCE_BACKGROUND')) define('CFG_APP_LOGIN_MAINTENANCE_BACKGROUND','');
	if(!defined('CFG_RESIZE_IMAGES')) define('CFG_RESIZE_IMAGES',0);
	if(!defined('CFG_MAX_IMAGE_WIDTH')) define('CFG_MAX_IMAGE_WIDTH',1600);
	if(!defined('CFG_MAX_IMAGE_HEIGTH')) define('CFG_MAX_IMAGE_HEIGTH',900);	
	if(!defined('CFG_RESIZE_IMAGES_TYPES')) define('CFG_RESIZE_IMAGES_TYPES','2');
	if(!defined('CFG_SKIP_IMAGE_RESIZE')) define('CFG_SKIP_IMAGE_RESIZE','5000');
	if(!defined('CFG_NOTIFICATIONS_SCHEDULE')) define('CFG_NOTIFICATIONS_SCHEDULE',0);
	if(!defined('CFG_SEND_EMAILS_ON_SCHEDULE')) define('CFG_SEND_EMAILS_ON_SCHEDULE',0);
	if(!defined('CFG_MAXIMUM_NUMBER_EMAILS')) define('CFG_MAXIMUM_NUMBER_EMAILS',3);
	if(!defined('CFG_USE_PUBLIC_REGISTRATION')) define('CFG_USE_PUBLIC_REGISTRATION',0);
	if(!defined('CFG_PUBLIC_REGISTRATION_USER_GROUP')) define('CFG_PUBLIC_REGISTRATION_USER_GROUP','');
	if(!defined('CFG_PUBLIC_REGISTRATION_PAGE_HEADING')) define('CFG_PUBLIC_REGISTRATION_PAGE_HEADING','');
	if(!defined('CFG_PUBLIC_REGISTRATION_PAGE_CONTENT')) define('CFG_PUBLIC_REGISTRATION_PAGE_CONTENT','');
	if(!defined('CFG_REGISTRATION_BUTTON_TITLE')) define('CFG_REGISTRATION_BUTTON_TITLE','');
	if(!defined('CFG_APP_DISABLE_CHANGE_PWD')) define('CFG_APP_DISABLE_CHANGE_PWD','');
	if(!defined('CFG_LOGIN_PAGE_HIDE_REMEMBER_ME')) define('CFG_LOGIN_PAGE_HIDE_REMEMBER_ME',0);
	
	
								
//get max upload file size
	define('CFG_SERVER_UPLOAD_MAX_FILESIZE',((int)ini_get("post_max_size")<(int)ini_get("upload_max_filesize") ? (int)ini_get("post_max_size") : (int)ini_get("upload_max_filesize")));

	date_default_timezone_set(CFG_APP_TIMEZONE);
	
//cache vars
	$app_choices_cache = fields_choices::get_cache();
	$app_global_choices_cache = global_lists::get_cache();
	$app_users_cache  = users::get_cache();	