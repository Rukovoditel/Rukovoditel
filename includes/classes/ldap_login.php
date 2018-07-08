<?php

class ldap_login
{
  public $config;
  
  function __construct()
  {
    $this->config = array();
    $this->config['ldap_server']      = CFG_LDAP_SERVER_NAME;
    $this->config['ldap_port']        = CFG_LDAP_SERVER_PORT;
    $this->config['ldap_base_dn']     = CFG_LDAP_BASE_DN;
    $this->config['ldap_uid']         = CFG_LDAP_UID;
    $this->config['ldap_user_filter'] = CFG_LDAP_USER;
    $this->config['ldap_email']       = CFG_LDAP_EMAIL_ATTRIBUTE;
    $this->config['ldap_firstname']   = CFG_LDAP_FIRSTNAME_ATTRIBUTE;
    $this->config['ldap_lastname']    = CFG_LDAP_LASTNAME_ATTRIBUTE;    
    $this->config['ldap_user']        = CFG_LDAP_USER_DN;
    $this->config['ldap_password']    = CFG_LDAP_PASSWORD;
  }
  
  function do_ldap_login($username, $password)
  {  	
  	if (!@extension_loaded('ldap'))
  	{
  		return array('status'=>false, 'msg'=>TEXT_LDAP_ERROR_NOT_AVAILABLE);
  	}
        	
  	if (strlen($this->config['ldap_port'])>0)
  	{
  		$ldap = ldap_connect($this->config['ldap_server'], $this->config['ldap_port']);
  		
  	}
  	else
  	{
  		$ldap = ldap_connect($this->config['ldap_server']);
  	}
        
  	if (!$ldap)  	
  	{
  	  return array('status'=>false, 'msg'=>TEXT_LDAP_ERROR_CONNECTION);  		
  	}
  	
  	  	  
  	@ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
  	@ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
  
  	if (strlen($this->config['ldap_user'])>0 || strlen($this->config['ldap_password'])>0)
  	{
  		if (!@ldap_bind($ldap, htmlspecialchars_decode($this->config['ldap_user']), htmlspecialchars_decode($this->config['ldap_password'])))
  		{  			  			
  			return array('status'=>false, 'msg'=>TEXT_LDAP_ERROR_BINDING);
  		}
  	}      
  
  	// ldap_connect only checks whether the specified server is valid, so the connection might still fail  	  	  	
  	$search_filters = array('cn',htmlspecialchars_decode($this->config['ldap_uid']));
  	
  	if(strlen($this->config['ldap_email']))
  		$search_filters[] = htmlspecialchars_decode($this->config['ldap_email']);
  			  			
  	if(strlen($this->config['ldap_firstname']))
  		$search_filters[] = htmlspecialchars_decode($this->config['ldap_firstname']);
  		
  	if(strlen($this->config['ldap_lastname']))
  		$search_filters[] = htmlspecialchars_decode($this->config['ldap_lastname']);
  	
  	  		  			  				  	  	  	    	
  	$search = @ldap_search(
  		$ldap,
  		htmlspecialchars_decode($this->config['ldap_base_dn']),
  		$this->ldap_user_filter($username),
  		$search_filters,
  		0,
  		1
  	);
  	  	    
  	$ldap_result = @ldap_get_entries($ldap, $search);
  	  
  	if (is_array($ldap_result) && sizeof($ldap_result) > 1)
  	{
  		if (@ldap_bind($ldap, $ldap_result[0]['dn'], htmlspecialchars_decode($password)))
  		{
  			  			  			  			  			
        $userName = '';
        $userEmail = '';
        $firstname = '';
        $lastname = '';
        $group = '';
        
        if(isset($ldap_result[0][htmlspecialchars_decode($this->config['ldap_email'])][0]))
        {
          $userEmail = $ldap_result[0][htmlspecialchars_decode($this->config['ldap_email'])][0];
        }
        
        if(isset($ldap_result[0]['cn'][0]))
        {
          $userName = $ldap_result[0]['cn'][0];
        }
        
        if(isset($ldap_result[0][htmlspecialchars_decode($this->config['ldap_firstname'])][0]))
        {
        	$firstname = $ldap_result[0][htmlspecialchars_decode($this->config['ldap_firstname'])][0];
        }
        
        if(isset($ldap_result[0][htmlspecialchars_decode($this->config['ldap_lastname'])][0]))
        {
        	$lastname = $ldap_result[0][htmlspecialchars_decode($this->config['ldap_lastname'])][0];
        }
        

  		  			
  			return array(
  					'status'=>true, 
  					'name'=>$userName,
  					'email'=>$userEmail,
  					'firstname'=>$firstname,
  					'lastname'=>$lastname,
  					'group'=>$this->ldap_get_user_group($ldap,$username));
  		}
  		else
  		{
  			unset($ldap_result);
  			@ldap_close($ldap);
  
  			// Give status about wrong password...
  			return array('status'=>false, 'msg'=>TEXT_LDAP_ERROR_INCORRECT_PASSWORD);
  		}
  	}
  
  	@ldap_close($ldap);
  
  	return array('status'=>false, 'msg'=>TEXT_LDAP_ERROR_INCORRECT_USERNAME);
  }
  
  
  
  /**
  * Generates a filter string for ldap_search to find a user
  *
  * @param	$username	string	Username identifying the searched user
  *
  * @return				string	A filter string for ldap_search
  */
  function ldap_user_filter($username)
  {  	
  	$filter = '(' . $this->config['ldap_uid'] . '=' . $this->ldap_escape(htmlspecialchars_decode($username)) . ')';
  	if ($this->config['ldap_user_filter'])
  	{
  		$_filter = ($this->config['ldap_user_filter'][0] == '(' && substr($this->config['ldap_user_filter'], -1) == ')') ? $this->config['ldap_user_filter'] : "({$this->config['ldap_user_filter']})";
  		$filter = "(&{$filter}{$_filter})";
  	}
  	return $filter;
  }
  
  /**
  * Escapes an LDAP AttributeValue
  */
  function ldap_escape($string)
  {
  	return str_replace(array('*', '\\', '(', ')'), array('\\*', '\\\\', '\\(', '\\)'), $string);
  }
  
  /**
   * check all user groups wehere ldap filter exists for user membership  
   */
  function ldap_get_user_group($ldap,$username)
  {  
  	$groups_query = db_query("select * from app_access_groups where length(ldap_filter)>0 ORDER BY sort_order");      	  	
  	while($groups = db_fetch_array($groups_query))
  	{    
  		$filter="(&(".htmlspecialchars_decode($this->config['ldap_uid'])."=".$username.")".$groups['ldap_filter'].")";
    
  		$search = @ldap_search(
  				$ldap,
  				htmlspecialchars_decode($this->config['ldap_base_dn']),
  				$filter
  				);
  		
  		$ldap_result = @ldap_get_entries($ldap, $search);
  
  		if (is_array($ldap_result) && sizeof($ldap_result) > 1)
  		{
  			return $groups['id'];  			
  		}  
  	}  
  
  	return false;  
  }

}


