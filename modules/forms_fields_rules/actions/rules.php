<?php


switch($app_module_action)
{
	case 'save':
		
		$visible_fields = (isset($_POST['visible_fields']) ? $_POST['visible_fields'] : array());
		
		$hidden_fields = (isset($_POST['hidden_fields']) ? $_POST['hidden_fields'] : array());
		
		//check aready set fields
		if(count($visible_fields) and count($hidden_fields))
		{					
			foreach($hidden_fields as $k=>$v)
			{
				if(in_array($v,$visible_fields))
				{
					unset($hidden_fields[$k]);
				}
			}			
		}
		
		$sql_data = array(
			'entities_id'=>$_GET['entities_id'],
			'fields_id'=>$_POST['fields_id'],
			'choices' => (isset($_POST['choices']) ? implode(',',$_POST['choices']) : ''),
			'visible_fields' => implode(',',$visible_fields),
			'hidden_fields' => implode(',',$hidden_fields),
		);
	
		if(isset($_GET['id']))
		{
			db_perform('app_forms_fields_rules',$sql_data,'update',"id='" . db_input($_GET['id']) . "'");
		}
		else
		{
			db_perform('app_forms_fields_rules',$sql_data);			
		}
	
		redirect_to('forms_fields_rules/rules','entities_id=' . $_GET['entities_id']);
		break;
	
	case 'delete':
		
		if(isset($_GET['id']))
		{
			db_delete_row('app_forms_fields_rules',$_GET['id']);
		
			$alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS,$name),'success');
		}
		
		redirect_to('forms_fields_rules/rules','entities_id=' . $_GET['entities_id']);
		break;
		
	case 'get_fields_choices':
		
		if(isset($_GET['id']))
		{
			$obj = db_find('app_forms_fields_rules',$_GET['id']);
		}
		else
		{
			$obj = db_show_columns('app_forms_fields_rules');
		}
		
		$fields_id = _get::int('fields_id');
		
		$field_info = db_find('app_fields', $fields_id);
		
		$exclude_choices = array();		
		$rules_query = db_query("select * from app_forms_fields_rules where fields_id='" . $fields_id . "'" . (isset($_GET['id']) ? " and id!='" . $_GET['id']. "'":''));
		while($rules = db_fetch_array($rules_query))
		{
			if(strlen($rules['choices']))
			{
				$exclude_choices = array_merge($exclude_choices,explode(',',$rules['choices']));
			}
		}
		
		$cfg = new fields_types_cfg($field_info['configuration']);
						
		$choices = array();	
		
		//handle users groups
		if($field_info['type']=='fieldtype_user_accessgroups')
		{
			foreach(access_groups::get_choices() as $id=>$name)
			{
				if(!in_array($id,$exclude_choices))
				{
					$choices[$id] = $name;
				}
			}
		}	
		//hanlde default choices
		else 
		{
			$tree = ($cfg->get('use_global_list')>0 ? global_lists::get_choices_tree($cfg->get('use_global_list')) : fields_choices::get_tree($fields_id));
			foreach($tree as $v)
			{
				if(!in_array($v['id'],$exclude_choices))
				{
					$choices[$v['id']] = $v['name'];
				}
			}
		}
		
		$html = '
				<div class="form-group">
					<label class="col-md-3 control-label" for="name">' . TEXT_SELECT_FIELD_VALUES  . '</label>
					    <div class="col-md-9">	
					  	  ' . select_tag('choices[]',$choices,$obj['choices'],array('class'=>'form-control input-xlarge chosen-select required','multiple'=>'multiple')) . '
					    </div>			
				</div>';
		
		
		$choices = array();
		$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.id!='" . $fields_id . "' and f.type not in (" . fields_types::get_reserverd_types_list() . ',' . fields_types::get_users_types_list(). ") and f.entities_id='" . _get::int('entities_id') . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
		while($v = db_fetch_array($fields_query))
		{
			$choices[$v['id']] = $v['name'];			
		}		
		
		$html .= '
		  <div class="form-group">
		  	<label class="col-md-3 control-label" for="name">' . TEXT_DISPLAY_FIELDS . '</label>
		    <div class="col-md-9">	
		  	  ' . select_tag('visible_fields[]',$choices,$obj['visible_fields'],array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')) . '
		    </div>			
		  </div>'; 
		  
		
		$choices = array();
		$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.id!='" . $fields_id . "' and f.type not in (" . fields_types::get_reserverd_types_list() . ',' . fields_types::get_users_types_list(). ") and f.entities_id='" . _get::int('entities_id') . "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
		while($v = db_fetch_array($fields_query))
		{			
			$choices[$v['id']] = $v['name'];			
		}	
		
		$html .= '
		  <div class="form-group">
		  	<label class="col-md-3 control-label" for="name">' . TEXT_HIDE_FIELDS . '</label>
		    <div class="col-md-9">	
		  	  ' . select_tag('hidden_fields[]',$choices,$obj['hidden_fields'],array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')) . '
		    </div>			
		  </div>';   		
		
		
		echo $html;
		
		exit();
		
		break;
}