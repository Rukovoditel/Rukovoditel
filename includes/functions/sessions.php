<?php


  if (STORE_SESSIONS == 'mysql') {
    if (!$SESS_LIFE = get_cfg_var('session.gc_maxlifetime')) {
      $SESS_LIFE = 1440;
    }
    
    register_shutdown_function('session_write_close');    

    function _sess_open($save_path, $session_name) {
      return true;
    }

    function _sess_close() {
      return true;
    }

    function _sess_read($key) {
      $value_query = db_query("select value from app_sessions where sesskey = '" . db_input($key) . "' and expiry > '" . time() . "'");
      $value = db_fetch_array($value_query);

      if (isset($value['value'])) {
        return $value['value'];
      }

      return '';
    }

    function _sess_write($key, $val) {
      global $SESS_LIFE;

      $expiry = time() + $SESS_LIFE;
      $value = $val;

      $check_query = db_query("select count(*) as total from app_sessions where sesskey = '" . db_input($key) . "'");
      $check = db_fetch_array($check_query);

      if ($check['total'] > 0) {
        return db_query("update app_sessions set expiry = '" . db_input($expiry) . "', value = '" . db_input($value) . "' where sesskey = '" . db_input($key) . "'");
      } else {
        return db_query("insert into app_sessions values ('" . db_input($key) . "', '" . db_input($expiry) . "', '" . db_input($value) . "')");
      }
    }

    function _sess_destroy($key) {
      return db_query("delete from app_sessions where sesskey = '" . db_input($key) . "'");
    }

    function _sess_gc($maxlifetime) {
      db_query("delete from app_sessions where expiry < '" . time() . "'");

      return true;
    }

    session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
  }

  function app_session_start() {
    global $_GET, $_POST, $_COOKIE;

    $sane_session_id = true;

    if (isset($_GET[app_session_name()])) {
      if (preg_match('/^[a-zA-Z0-9]+$/', $_GET[app_session_name()]) == false) {
        unset($_GET[app_session_name()]);

        $sane_session_id = false;
      }
    } elseif (isset($_POST[app_session_name()])) {
      if (preg_match('/^[a-zA-Z0-9]+$/', $_POST[app_session_name()]) == false) {
        unset($_POST[app_session_name()]);

        $sane_session_id = false;
      }
    } elseif (isset($_COOKIE[app_session_name()])) {
      if (preg_match('/^[a-zA-Z0-9]+$/', $_COOKIE[app_session_name()]) == false) {
        $session_data = session_get_cookie_params();

        setcookie(app_session_name(), '', time()-42000, $session_data['path'], $session_data['domain']);

        $sane_session_id = false;
      }
    }

    if ($sane_session_id == false) 
    {
      //put redirect here
    }

    return session_start();
  }

  function app_session_register($variable,$value = null) {
    global $session_started;

    if ($session_started == true) 
    {      
      if (isset($GLOBALS[$variable])) {
        $_SESSION[$variable] =& $GLOBALS[$variable];
      } else {
        $_SESSION[$variable] = $value;
      }      
    }

    return false;
  }

  function app_session_is_registered($variable) 
  {    
    return isset($_SESSION) && array_key_exists($variable, $_SESSION);    
  }

  function app_session_unregister($variable) 
  {    
    unset($_SESSION[$variable]);    
  }

  function app_session_id($sessid = '') {
    if (!empty($sessid)) {
      return session_id($sessid);
    } else {
      return session_id();
    }
  }

  function app_session_name($name = '') {
    if (!empty($name)) {
      return session_name($name);
    } else {
      return session_name();
    }
  }

  function app_session_close() {
    if (PHP_VERSION >= '4.0.4') {
      return session_write_close();
    } elseif (function_exists('session_close')) {
      return session_close();
    }
  }

  function app_session_destroy() {
    return session_destroy();
  }

  function app_session_save_path($path = '') {
    if (!empty($path)) {
      return session_save_path($path);
    } else {
      return session_save_path();
    }
  }

  function app_session_recreate() {
    if (PHP_VERSION >= 4.1) {
      $session_backup = $_SESSION;

      unset($_COOKIE[app_session_name()]);

      app_session_destroy();

      if (STORE_SESSIONS == 'mysql') {
        session_set_save_handler('_sess_open', '_sess_close', '_sess_read', '_sess_write', '_sess_destroy', '_sess_gc');
      }

      app_session_start();

      $_SESSION = $session_backup;
      unset($session_backup);
    }
  }
?>
