<?php

class users_notifications
{
	public $unread_items;
	
	function __construct($entities_id)
	{
		global $app_user;
		
		$this->unread_items = array();
		
		$items_query = db_query("select * from app_users_notifications where users_id='" . $app_user['id'] . "' and entities_id='" . $entities_id . "'");
		while($items = db_fetch_array($items_query))
		{
			$this->unread_items[] = $items['items_id'];
		}	
	}
	
	function has($items_id)
	{
		global $app_users_cfg;
		
		//don't highlight if configuratin disabled
		if($app_users_cfg->get('disable_highlight_unread')==1) return false;
		
		return in_array($items_id,$this->unread_items);				
	}
	
	static function add($name, $type, $users_id, $entities_id, $items_id)
	{		
		global $app_user;
		
		//skip user with disabled notification
		if(users_cfg::get_value_by_users_id($users_id, 'disable_internal_notification')==1 and users_cfg::get_value_by_users_id($users_id, 'disable_highlight_unread')==1) return false;
		
		//skip current user
		if($app_user['id']==$users_id) return false;
		
		$sql_data = array(
				'users_id' 		=> $users_id,
				'entities_id' => $entities_id,
				'items_id' 		=> $items_id,
				'name' 				=> $name,
				'type'        => $type,
				'date_added'  => time(),
				'created_by'  => $app_user['id'],
		);
		
		db_perform('app_users_notifications',$sql_data);		
	}
	
	static function reset($entities_id, $items_id)
	{
		global $app_user;
		
		db_query("delete from app_users_notifications where users_id='" . $app_user['id'] . "' and entities_id='" . $entities_id . "' and items_id='" . $items_id . "'");
	}
	
	static function render()
	{					
		global $app_users_cfg;
		
		//skip menu with disabled notificaiton
		if($app_users_cfg->get('disable_internal_notification')==1) return false;
		
		$html = '
        <li class="dropdown hot-reports" id="user_notifications_report">
          ' . '
        </li>
		
        <script>
          function user_notifications_report_render_dropdown()
          {
            $("#user_notifications_report").load("' . url_for("dashboard/","action=update_user_notifications_report") . '",function(){
                $(\'[data-hover="dropdown"]\').dropdownHover();
              })
          }
		
          $(function(){
             setInterval(function(){
              user_notifications_report_render_dropdown()
             },60000);
          });
		
          user_notifications_report_render_dropdown()
		
        </script>
      ';
		
		return $html;
	}
	
	static function render_dropdown()
	{
		global $app_user, $app_users_cache;
		
		$poup_items_limit = 25;
		
		$items_html = '';
		
		$itmes_query = db_query("select * from app_users_notifications where users_id='" . $app_user['id'] . "' order by id desc limit " . $poup_items_limit);
		while($itmes = db_fetch_array($itmes_query))
		{
			$path_info = items::get_path_info($itmes['entities_id'],$itmes['items_id']);
			
			$items_html .= '
          <li>
  					<a href="' . url_for('items/info','path=' . $path_info['full_path']) . '">' . self::render_icon_by_type($itmes['type']) . ' ' . $itmes['name'] . ' <span class="parent-name"><i class="fa fa-angle-left"></i>' . (isset($app_users_cache[$itmes['created_by']]) ? $app_users_cache[$itmes['created_by']]['name'] : '') . '</span></a>
  				</li>
        ';			
		}
				
		$itmes_count = db_count('app_users_notifications',$app_user['id'],'users_id');
		
		if($itmes_count==0)
		{
			$items_html .= '
          <li>
  					<a onClick="return false;">' . TEXT_NO_RECORDS_FOUND . '</a>
  				</li>
        ';
		}
		
		$dropdown_menu_height = ($itmes_count<11 ? ($itmes_count*42+42) : 420);
		
		$external_html = '';
		if($itmes_count>$poup_items_limit)
		{
			$external_html = '
          <li class="external">
						<a href="' . url_for('users/notifications') . '">' . sprintf(TEXT_DISPLAY_NUMBER_OF_ITEMS,1, $poup_items_limit,$itmes_count) . '</a>
					</li>
        ';
		}
		
		$badge_html = ($itmes_count>0 ? '<span class="badge badge-warning">' . $itmes_count . '</span>' : '');
		
		$html = '
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
				  <i class="fa fa-bell-o"></i>
				  ' . $badge_html . '
				</a>
				<ul class="dropdown-menu extended tasks">
					<li style="cursor:pointer" onClick="location.href=\'' . url_for('users/notifications') . '\'">
						<p>' . TEXT_USERS_NOTIFICATIONS . '</p>
					</li>
					<li>
						<ul class="dropdown-menu-list scroller" style="height: ' . $dropdown_menu_height . 'px;">
							' . $items_html . '
              ' . $external_html . '  
						</ul>
					</li>
          
				</ul>            
      ';
		
		return $html;
	}
	
	static function render_icon_by_type($type)
	{
		$html = '';
		
		switch($type)
		{
			case 'new_item':
				$html = '<i class="fa fa-bell-o" aria-hidden="true"></i>';
				break;
			case 'new_comment':
				$html = '<i class="fa fa-comment-o" aria-hidden="true"></i>';
				break;
			case 'updated_item':
				$html = '<i class="fa fa-refresh" aria-hidden="true"></i>';
				break;
		}
		
		return $html;
	}
}