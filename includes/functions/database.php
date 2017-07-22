<?php

  function db_connect($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE, $link = 'db_link') {
    global $$link;
      
    $$link = mysqli_init();
    
    if (!$$link) {
        die('mysqli_init failed');
    }
        
    if (!mysqli_options($$link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
        die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
    }
    
    if (!mysqli_options($$link, MYSQLI_INIT_COMMAND, 'SET NAMES utf8')) {
        die('Setting MYSQLI_INIT_COMMAND failed');
    }
    
    if (!@mysqli_real_connect($$link, $server, $username, $password, $database)) {
        die('Error: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error() . '<br><br>Please check database settings in "config/database.php" file.');
    }
    
    //reset sql mode     
    db_query("SET sql_mode = ''");

    return $$link;
  }

  function db_close($link = 'db_link') {
    global $$link;

    return mysqli_close($$link);
  }

  function db_error($query, $errno, $error) {
    $html = '
      <app_db_error>
      <div style="color: #b94a48; background: #f2dede; border: 1px solid #eed3d7; padding: 5px; margin: 5px; font-family: verdana; font-size: 12px; line-height: 1.5;">
        <div><strong>Database Error:</strong> ' . $errno . ' - ' . $error . '</div>
        <div><strong>Query:</strong> ' . $query . '</div>
        <div><strong>Page: </strong> ' . $_SERVER['REQUEST_URI'] . '</div>
      </div>
    '; 
    die($html);
  }

  function db_query($query, $debug = false,$link = 'db_link') {
    global $$link, $app_db_query_log;
    
    if(DEV_MODE)
    {
      $starttime = microtime(true);
    }
    
    if($debug)
    {
      echo $query;
    }

    $result = mysqli_query($$link, $query ) or db_error($query, mysqli_errno($$link), mysqli_error($$link));
            
    if(DEV_MODE)
    {      
      $app_db_query_log[] = $query . ' [' . number_format((microtime(true) - $starttime), 3) . ']';            
    }          
  
    return $result;
  }
  
  function db_batch_insert($table, $data)
  {
  	reset($data);
  	
  	if(count($data)==0) return false;
  	
  	$query = 'insert into ' . $table . ' (';
  	
  	while (list($columns, ) = each($data[0])) 
  	{
  		$query .= $columns . ', ';
  	}
  	
  	$query = substr($query, 0, -2) . ') values ';
  	
  	
  	reset($data);
  	
  	foreach($data as $d)
  	{
  		$query .= '(';
  		
	  	while (list(, $value) = each($d)) 
	  	{
	  		switch ((string)$value) 
	  		{
	  			case 'now()':
	  				$query .= 'now(), ';
	  				break;
	  			case 'null':
	  				$query .= 'null, ';
	  				break;
	  			default:
	  				$query .= '\'' . db_input($value) . '\', ';
	  				break;
	  		}
	  	}
	  	
	  	$query = substr($query, 0, -2) . '), ';
  	}
  	
  	$query = substr($query, 0, -2);
  	
  	return db_query($query);
  	
  }

  function db_perform($table, $data, $action = 'insert', $parameters = '') {
    reset($data);
    if ($action == 'insert') {
      $query = 'insert into ' . $table . ' (';
      while (list($columns, ) = each($data)) {
        $query .= $columns . ', ';
      }
      $query = substr($query, 0, -2) . ') values (';
      reset($data);
      while (list(, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= 'now(), ';
            break;
          case 'null':
            $query .= 'null, ';
            break;
          default:
            $query .= '\'' . db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ')';
    } elseif ($action == 'update') {
      $query = 'update ' . $table . ' set ';
      while (list($columns, $value) = each($data)) {
        switch ((string)$value) {
          case 'now()':
            $query .= $columns . ' = now(), ';
            break;
          case 'null':
            $query .= $columns .= ' = null, ';
            break;
          default:
            $query .= $columns . ' = \'' . db_input($value) . '\', ';
            break;
        }
      }
      $query = substr($query, 0, -2) . ' where ' . $parameters;
    }

    return db_query($query);
  }

  function db_fetch_array($result) 
  {
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
  }
  
  function db_fetch_all($table,$where = '',$order_by = '')
  {
    return db_query("select * from " . $table .  (strlen($where)>0 ? ' where ' . $where : '') .  (strlen($order_by)>0 ? ' order by ' . $order_by : ''));
  }
  
  function db_find($table, $value, $column='id')
  { 
    $info_query = db_query("select * from " . $table . " where " . $column . "='" . db_input($value)  . "'");
    if($info = db_fetch_array($info_query))
    {
      return $info;
    }
    else
    {
      $info = array();
      $columns_query = db_query("SHOW COLUMNS FROM " . $table );
      while($columns = db_fetch_array($columns_query))
      {
        $info[$columns['Field']] = '';
      }
      
      return $info;
    }
  }
  
  function db_count($table, $value='', $column='id')
  {
    $info_query = db_query("select count(*) as total from " . $table . (strlen($value)>0 ? " where " . $column . "='" . db_input($value)  . "'":""));
    $info = db_fetch_array($info_query);
    
    return $info['total'];
  }
  
  function db_show_columns($table)
  {
    $info = array();
    $columns_query = db_query("SHOW COLUMNS FROM " . $table );
    while($columns = db_fetch_array($columns_query))
    {
      $info[$columns['Field']] = '';
    }
    
    return $info;
  }
  
  function db_delete_row($table,$value,$column='id')
  {
    db_query("delete from " . $table . " where " . $column . "='" . db_input($value) . "'");
  }

  function db_num_rows($result) {
    return mysqli_num_rows($result);
  }
  
  function db_insert_id($link = 'db_link') {
    global $$link;

    return mysqli_insert_id($$link);
  }

  function db_output($string) {
    return htmlspecialchars($string);
  }
         
  
  
  function db_input($string, $link = 'db_link') {
    global $$link;
    
    //remove slashes added by magic_quotes
    if (get_magic_quotes_gpc()) 
    {
    	$string = stripslashes($string);
    }
                 
    if (function_exists('mysqli_real_escape_string')) 
    {    	 
    	return mysqli_real_escape_string($$link,$string);
    } 
    elseif (function_exists('mysqli_escape_string')) 
    {
    	return mysqli_escape_string($$link,$string);
    }
                                                            
    return addslashes($string);
  }
  
  function db_prepare_input($string) {
    if (is_string($string)) 
    {
      return trim(app_sanitize_string($string));
    } 
    elseif (is_array($string)) 
    {
      reset($string);
      while (list($key, $value) = each($string)) {
        $string[$key] = db_prepare_input($value);
      }
      return $string;
    } 
    else 
    {
      return $string;
    }
  }
  
  function db_prepare_html_input($html)
  { 
  	$config = HTMLPurifier_Config::createDefault();
  	$config->set('Attr.AllowedFrameTargets', array('_blank'));
  	$purifier = new HTMLPurifier($config);
  	return $purifier->purify($html);  	 	
  }

