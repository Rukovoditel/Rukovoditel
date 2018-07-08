<?php
class processes_flowchart
{
	public $nodes;

	public $edges;

	public $height;

	public $height_step;

	public $y;

	public $y_step;
	
	public $x;
	
	public $x_step;
	
	public $process_has_filter;
	
	public $action_has_filter;
		
	function __construct()
	{
		$this->y = 0;
		$this->y_step = 30;
		$this->y_field_step = 20;
		
		$this->x = 0;
		$this->x_step = 80;

		$this->height = 0;
		$this->height_step = 90;
		
		$process_has_filter = array();
		$action_has_filter = array();

		$this->nodes = array();
		$this->edges = array();
	}

	function prepare_data()
	{
		global $processes_filter;
		
		$where_sql = '';
		
		if(isset($_GET['process_id']))
		{
			$where_sql .= " and p.id='" . db_input(_get::int('process_id')) . "'";
		}			
		elseif($processes_filter>0)
		{
			$where_sql .= " and p.entities_id='" . db_input($processes_filter) . "'";
		}
		
		$processes_query = db_query("select p.*, e.name as entities_name from app_ext_processes p, app_entities e where e.id=p.entities_id {$where_sql} order by p.sort_order, e.name, p.name");
		while($process = db_fetch_array($processes_query))
		{
			//reset x
			$this->x = 0;
			
			//process node
			$this->nodes[] = "{ data: { id: 'process_{$process['id']}',name: '" . addslashes($process['entities_name'] . ': ' . $process['name']). "'}, classes:'process', position: { x: {$this->x}, y: {$this->y} }}";
			
			//filter node			
			$this->prepare_process_filter_node($process);
			
			//actions node
			$this->prepare_process_actions_node($process);
			
			
			$this->y += $this->y_step;
		}
						
		$this->height = ($this->y/$this->y_step)*$this->height_step;
		
		$this->height = ($this->height<200 ? 200 : $this->height);
	}
	
	function prepare_process_filter_node($process)
	{
		$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($process['entities_id']). "' and reports_type='process" . $process['id'] . "'");
		if($reports_info = db_fetch_array($reports_info_query))
		{
			$filters_title = '';
			$filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($reports_info['id']) . "' order by rf.id");
			while($filters = db_fetch_array($filters_query))
			{
				$filters_title .= fields_types::get_option($filters['type'],'name',$filters['name']) . ": " . reports::get_condition_name_by_key($filters['filters_condition'])  . ' ' . reports::render_filters_values($filters['fields_id'],$filters['filters_values'],', ',$filters['filters_condition']) . '<br>';
			}
		
			if(strlen($filters_title))
			{
				$this->x+=$this->x_step;
				
				$this->nodes[] = "{ data: { id: 'process_filter_{$process['id']}',name: '" . addslashes($filters_title). "'}, classes:'process_filter', position: { x: {$this->x}, y: {$this->y} }}";
				
				$this->process_has_filter[$process['id']] = true;
			}
		}
	}
	
	function prepare_process_actions_node($process)
	{
		$this->x+=$this->x_step;
		
		$actions_x = $this->x;
		
		$actions_types = processes::get_actions_types_choices($process['entities_id']);
		
		$previos_action_id = false;
		$actions_query = db_query("select pa.*, p.name as process_name from app_ext_processes_actions pa, app_ext_processes p where pa.process_id='" . $process['id'] . "' and  p.id=pa.process_id order by pa.sort_order");				
		while($actions = db_fetch_array($actions_query))
		{
			$this->x = $actions_x;
			
			$this->nodes[] = "{ data: { id: 'action_{$actions['id']}',process_id:{$actions['process_id']}, name: '" . addslashes($actions_types[$actions['type']]). "'}, classes:'actions', position: { x: {$this->x}, y: {$this->y} }}";
			
			$this->prepare_process_actions_edge($process,$actions,$previos_action_id);
			
			$this->prepare_process_actions_filter_node($actions);
			$this->prepare_process_actions_fields_node($actions);
			
			$this->y += $this->y_step;
			
			$previos_action_id = $actions['id'];;
		}
	}
	
	function prepare_process_actions_edge($process,$actions,$previos_action_id)
	{
		if(!$previos_action_id)
		{	
			if(isset($this->process_has_filter[$process['id']]))
			{
				$this->edges[] = "{ data: { id: 'process_action_{$actions['id']}', source: 'process_{$process['id']}', target: 'process_filter_{$process['id']}',label: ''} }";
				$this->edges[] = "{ data: { id: 'process_action_yes_{$actions['id']}', source: 'process_filter_{$process['id']}', target: 'action_{$actions['id']}',label: '" . addslashes(TEXT_YES). "'} }";
			}
			else
			{
				$this->edges[] = "{ data: { id: 'process_action_{$actions['id']}', source: 'process_{$process['id']}', target: 'action_{$actions['id']}',label: ''} }";
			}
		}
		else
		{
			$this->edges[] = "{ data: { id: 'process_action_{$actions['id']}', source: 'action_{$previos_action_id}', target: 'action_{$actions['id']}',label: ''} }";
		}
	}
	
	function prepare_process_actions_filter_node($actions)
	{
		$action_entity_id = processes::get_entity_id_from_action_type($actions['type']);
		
		$reports_info_query = db_query("select * from app_reports where entities_id='" . db_input($action_entity_id). "' and reports_type='process_action" . $actions['id'] . "'");
		if($reports_info = db_fetch_array($reports_info_query))
		{
			$filters_title = '';
			$filters_query = db_query("select rf.*, f.name, f.type from app_reports_filters rf left join app_fields f on rf.fields_id=f.id where rf.reports_id='" . db_input($reports_info['id']) . "' order by rf.id");
			while($filters = db_fetch_array($filters_query))
			{
				$filters_title .= fields_types::get_option($filters['type'],'name',$filters['name']) . ": " . reports::get_condition_name_by_key($filters['filters_condition'])  . ' ' . reports::render_filters_values($filters['fields_id'],$filters['filters_values'],', ',$filters['filters_condition']) . '<br>';
			}
		
			if(strlen($filters_title))
			{
				$this->x+=$this->x_step;
		
				$this->nodes[] = "{ data: { id: 'action_filter_{$actions['id']}', process_id:{$actions['process_id']}, name: '" . addslashes($filters_title). "'}, classes:'process_filter', position: { x: {$this->x}, y: {$this->y} }}";
				
				$this->action_has_filter[$actions['id']] = true;
			}
		}
	}
	
	function prepare_process_actions_fields_node($actions)
	{
		$this->x+=$this->x_step;
		
		$count = 0;
		$previos_field_id = false;
		$actions_fields_query = db_query("select af.id, af.fields_id, af.value, af.enter_manually, f.name, f.type as field_type from app_ext_processes_actions_fields af, app_fields f left join app_forms_tabs t on f.forms_tabs_id=t.id  where f.id=af.fields_id and af.actions_id='" . db_input($actions['id']) ."' order by t.sort_order, t.name, f.sort_order, f.name");						
		while($actions_fields = db_fetch_array($actions_fields_query))
		{
			if($count>0)
			{
				$this->y += $this->y_field_step;				
			}
			
			$title = $actions_fields['name'] . ': ' . strip_tags(processes::output_action_field_value($actions_fields));
			
			$this->nodes[] = "{ data: { id: 'field_{$actions_fields['id']}',name: '" . addslashes($title). "'}, classes:'field', position: { x: {$this->x}, y: {$this->y} }}";
			
			$this->prepare_process_actions_fields_edge($actions,$actions_fields,$previos_field_id);
			
			$count++;
			
			$previos_field_id = $actions_fields['id']; 
		}	
	}
	
	function prepare_process_actions_fields_edge($actions,$actions_fields,$previos_field_id)
	{
		if(!$previos_field_id)
		{
			if(isset($this->action_has_filter[$actions['id']]))
			{
				$this->edges[] = "{ data: { id: 'action_field_{$actions_fields['id']}', source: 'action_{$actions['id']}', target: 'action_filter_{$actions['id']}',label: ''} }";
				$this->edges[] = "{ data: { id: 'action_filed_yes_{$actions_fields['id']}', source: 'action_filter_{$actions['id']}', target: 'field_{$actions_fields['id']}',label: '" . addslashes(TEXT_YES). "'} }";
			}
			else
			{
				$this->edges[] = "{ data: { id: 'action_field_{$actions_fields['id']}', source: 'action_{$actions['id']}', target: 'field_{$actions_fields['id']}',label: ''} }";
			}
		}
		else
		{
			$this->edges[] = "{ data: { id: 'action_field_{$actions_fields['id']}', source: 'field_{$previos_field_id}', target: 'field_{$actions_fields['id']}',label: ''} }";
		}
	}
}