<?php

$hasher = new PasswordHash(11, false);

if(strlen(trim($_POST['password']))>0)
{
  $password = trim($_POST['password']);
}
else
{
  $password = users::get_random_password();
}

$sql_data['password']=$hasher->HashPassword($password);

$to_name = (CFG_APP_DISPLAY_USER_NAME_ORDER=='firstname_lastname' ? $_POST['fields'][7] . ' ' . $_POST['fields'][8] : $_POST['fields'][8] . ' ' . $_POST['fields'][7]);

$options = array('to' => $_POST['fields'][9],
                 'to_name' => $to_name,
                 'subject'=>(strlen(CFG_REGISTRATION_EMAIL_SUBJECT)>0 ? CFG_REGISTRATION_EMAIL_SUBJECT :TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT),
                 'body'=>CFG_REGISTRATION_EMAIL_BODY . '<p><b>' . TEXT_LOGIN_DETAILS . '</b></p><p>' . TEXT_USERNAME .': ' . $_POST['fields'][12] . '<br>' . TEXT_PASSWORD . ': ' . $password . '</p><p><a href="' . url_for('users/login','',true) . '">' . url_for('users/login','',true). '</a></p>',
                 'from'=> CFG_EMAIL_ADDRESS_FROM,
                 'from_name'=> CFG_EMAIL_NAME_FROM );
                 
users::send_email($options);