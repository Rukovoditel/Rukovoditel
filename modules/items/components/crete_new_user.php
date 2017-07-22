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

$options = array('to' => $_POST['fields'][9],
                 'to_name' => $_POST['fields'][7] . ' ' . $_POST['fields'][8],
                 'subject'=>(strlen(CFG_REGISTRATION_EMAIL_SUBJECT)>0 ? CFG_REGISTRATION_EMAIL_SUBJECT :TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT),
                 'body'=>CFG_REGISTRATION_EMAIL_BODY . '<p><b>' . TEXT_LOGIN_DETAILS . '</b></p><p>' . TEXT_USERNAME .': ' . $_POST['fields'][12] . '<br>' . TEXT_PASSWORD . ': ' . $password . '</p><p><a href="' . url_for('users/login','',true) . '">' . url_for('users/login','',true). '</a></p>',
                 'from'=> $app_user['email'],
                 'from_name'=>'noreply' );
                 
users::send_email($options);