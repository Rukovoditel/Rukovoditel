<?php

class entities_flowchart
{
	public $nodes;
	
	public $edges;
	
	public $tips;
	
	public $fields_step;
	
	public $height;
	
	public $entities_fields_count;
	
	public $entities_coords;
	
	public $shcema;
			
	function __construct()
	{
		$this->fields_step = 8;
		
		$this->height = 0;
		
		$this->entities_fields_count = array();
		$this->entities_coords = array();
			
		$this->nodes = array();
		$this->edges = array();
		$this->tips = array();	
		
		$data = $this->get_shcema();
		$this->shcema = $data['tree'];				
	}	
	
  function get_shcema($parent_id=0,$tree=array(),$level=0, $x=0, $y=0)
  {  	  	
  	$entities_query = db_query("select * from app_entities where parent_id='" . $parent_id . "' order by sort_order, name");  	
  
  	while($entities = db_fetch_array($entities_query))
  	{
  		$tree['tree'][] = array(
  				'id'=>$entities['id'],
  				'parent_id'=>$entities['parent_id'],
  				'name'=>$entities['name'],
  				'notes'=>$entities['notes'],
  				'sort_order'=>$entities['sort_order'],
  				'level'=>$level,  				
  				'x' => $x,
  				'y' => $y,
  		);
  		
  		$tree['y'] = $y;
  		  		  
  		$tree = $this->get_shcema($entities['id'],$tree,$level+1,$x+130,$y);
  		
  		$y = $tree['y'];
  		
  		$count_fields = 0;
  		$check_fields_query = db_query("select * from app_fields where entities_id = '" . $entities['id'] . "' and type in ('fieldtype_users','fieldtype_grouped_users','fieldtype_entity','fieldtype_related_records','fieldtype_formula')");
  		while($check_fields = db_fetch_array($check_fields_query))
  		{
  			if($check_fields['type']=='fieldtype_formula')
  			{
  				$cfg = new fields_types_cfg($check_fields['configuration']);
  				
  				if(strstr($cfg->get('formula'),'{'))
  				{
  					$count_fields++;
  				}
  			}	
  			else
  			{
  				$count_fields++;
  			}
  			
  		}
  		
  		$y+= 30+($count_fields*11);
  	}
  
  	return $tree;
  } 
  
  function prepare_data()
  {
  	$this->build_entities_nodes();
  	$this->build_users_fields_nodes();
  	$this->build_entity_fields_nodes();
  	$this->build_related_records_nodes();
  	$this->build_functions_nodes();
  	$this->build_entities_edges();
  	$this->build_tips();
  }
  
  function build_entities_nodes()
  {
  	foreach($this->shcema as $entities)
  	{
  		//users entity have own color
  		$faveColor = ($entities['id']==1 ? '#5bc0de':'#e2e3e5');
  	
  		//entity node
  		$this->nodes[] = "{ data: { id: 'entity_" . $entities['id'] . "',name: '" . addslashes($entities['name']). "',faveShape:'rectangle',borderWidth:1, nodeSize: 15, faveColor:'{$faveColor}', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . $entities['x'] . ", y: " . $entities['y'] . " }}";
  	
  		//extra parent node to display arrows edge
  		$this->nodes[] = "{ data: { id: 'field_0_" . $entities['id'] . "',name: '',parent:'entity_" . $entities['id'] . "',faveShape:'rectangle',borderWidth:1, nodeSize: 0, faveColor:'{$faveColor}', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . $entities['x'] . ", y: " . $entities['y'] . " }}";
  	
  		//hold coordintates for each entity
  		$this->entities_coords[$entities['id']] = array($entities['x'],$entities['y']);
  	
  		//get max height
  		$this->height = ($entities['y']>$this->height ? $entities['y']:$this->height);

  		//reset fields count
  		$this->entities_fields_count[$entities['id']] = 0;
  	}  	  	
  }
  
  function build_users_fields_nodes()
  {
  	$fields_query = db_query("select * from app_fields where type in ('fieldtype_users','fieldtype_grouped_users')");
  	while($fields = db_fetch_array($fields_query))
  	{  		  	
  		//set coordinates for field entity
  		$x = $this->entities_coords[$fields['entities_id']][0];
  		$y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']]*$this->fields_step);
  	
  		$this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes($fields['name']). "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#5bc0de', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  	
  		$this->entities_fields_count[$fields['entities_id']]++;
  	}  
  }
  
  function build_entity_fields_nodes()
  {
  	$fields_query = db_query("select * from app_fields where type in ('fieldtype_entity')");
  	while($fields = db_fetch_array($fields_query))
  	{  		  	
  		//set coordinates for field entity
  		$x = $this->entities_coords[$fields['entities_id']][0];
  		$y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']]*$this->fields_step);
  	
  		$this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes($fields['name']). "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#ffc107', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  	
  		$this->entities_fields_count[$fields['entities_id']]++;
  	
  		$cfg = new fields_types_cfg($fields['configuration']);
  			
  		$this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'entity_{$cfg->get('entity_id')}', target: 'field_{$fields['id']}',lineColor: '#ffc107',arrowShape:'triangle',sourceShape:'none',width:1},classes:'relation' }";
  	}  
  }
  
  function build_related_records_nodes()
  {
  	$skip_related_records_fields = array();
  	$fields_query = db_query("select * from app_fields where type in ('fieldtype_related_records')");
  	while($fields = db_fetch_array($fields_query))
  	{  	  		  
  		//set coordinates for field entity
  		$x = $this->entities_coords[$fields['entities_id']][0];
  		$y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']]*$this->fields_step);
  	
  		$this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes($fields['name']). "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#28a745', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  	
  		$this->entities_fields_count[$fields['entities_id']]++;
  	
  		$cfg = new fields_types_cfg($fields['configuration']);
  	
  		$source = 'entity_' .$cfg->get('entity_id');
  		$sourceShape = 'none';
  	
  		//check if exist related field
  		$check_query = db_query("select * from app_fields where type in ('fieldtype_related_records') and entities_id='" . $cfg->get('entity_id') . "'");
  		while($check = db_fetch_array($check_query))
  		{
  			$check_cfg = new fields_types_cfg($check['configuration']);
  			if($check_cfg->get('entity_id')==$fields['entities_id'])
  			{
  				$source = 'field_' . $check['id'];
  				$sourceShape = 'triangle';
  				$skip_related_records_fields[] = $check['id'];
  			}
  		}
  	
  		if(!in_array($fields['id'],$skip_related_records_fields))
  		{
  			$this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: '{$source}', target: 'field_{$fields['id']}',lineColor: '#28a745',arrowShape:'triangle',sourceShape:'{$sourceShape}',width:1},classes:'relation' }";
  		}
  	}  
  }
  
  function build_functions_nodes()
  {
  	global $app_functions_cache;
  	
  	$skip_functions = array();
  	$fields_query = db_query("select * from app_fields where type in ('fieldtype_formula')");
  	while($fields = db_fetch_array($fields_query))
  	{
  		$cfg = new fields_types_cfg($fields['configuration']);
  	
  		if(strstr($cfg->get('formula'),'{') and class_exists('functions'))
  		{
  			if(!isset($entities_fields_count[$fields['entities_id']])) $entities_fields_count[$fields['entities_id']] = 0;
  	
  			//set coordinates for field entity
  			$x = $this->entities_coords[$fields['entities_id']][0];
  			$y = $this->entities_coords[$fields['entities_id']][1] + ($this->entities_fields_count[$fields['entities_id']]*$this->fields_step);
  	
  			$this->nodes[] = "{ data: { id: 'field_" . $fields['id'] . "',name: '" . addslashes($fields['name']). "',parent: 'entity_{$fields['entities_id']}', faveShape:'rectangle',borderWidth:0, nodeSize: 4, faveColor:'#17a2b8', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  	
  			$this->entities_fields_count[$fields['entities_id']]++;
  	
  			$skip_funcitons_edges = array();
  	
  			foreach($app_functions_cache as $functions)
  			{
  				//simple formula string
  				if(strstr($cfg->get('formula'),'{' . $functions['id'] . '}'))
  				{
  					//set coordinates for field entity
  					$x = $this->entities_coords[$functions['entities_id']][0];
  					$y = $this->entities_coords[$functions['entities_id']][1] + ($this->entities_fields_count[$functions['entities_id']]*$this->fields_step);
  	
  					if(!in_array($functions['id'],$skip_functions))
  					{
  						$this->nodes[] = "{ data: { id: 'function_" . $functions['id'] . "',name: '" . addslashes($functions['name']). "',parent: 'entity_{$functions['entities_id']}', faveShape:'diamond',borderWidth:0, nodeSize: 5, faveColor:'#959393', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  							
  						$skip_functions[] = $functions['id'];
  							
  						$this->entities_fields_count[$functions['entities_id']]++;
  							
  						//function tip
  						$this->tips[] = array(
  								'id' => 'function_' . $functions['id'],
  								'content' => TEXT_EXT_FUNCTION . ': ' . $functions['functions_name'] . '<br>' . TEXT_FORMULA . ': ' . $functions['functions_formula'] . '<br>' . $functions['notes'],
  						);
  					}
  	
  					$this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'function_{$functions['id']}', target: 'field_{$fields['id']}',lineColor: '#17a2b8',arrowShape:'triangle',sourceShape:'none',width:1},classes:'function' }";
  				}
  	
  				//formula with related items
  				if(preg_match_all('/{(\d+):(\d+)}/',$cfg->get('formula'), $matches))
  				{
  					foreach($matches[1] as $matches_key=>$functions_id)
  					{
  						if(!isset($app_functions_cache[$functions_id])) continue;
  						
  						$function_info = $app_functions_cache[$functions_id];
  							
  						//set coordinates for field entity
  						$x = $this->entities_coords[$function_info['entities_id']][0];
  						$y = $this->entities_coords[$function_info['entities_id']][1] + ($this->entities_fields_count[$function_info['entities_id']]*$this->fields_step);
  							
  						if(!in_array($function_info['id'],$skip_functions))
  						{
  							$this->nodes[] = "{ data: { id: 'function_" . $function_info['id'] . "',name: '" . addslashes($function_info['name']). "',parent: 'entity_{$function_info['entities_id']}', faveShape:'diamond',borderWidth:0, nodeSize: 5, faveColor:'#959393', fontSize:'4px',textValign:'center',textHalign:'right'}, position: { x: " . $x . ", y: " . $y . " }}";
  	
  							$skip_functions[] = $function_info['id'];
  	
  							$this->entities_fields_count[$function_info['entities_id']]++;
  							
  							//function tip
  							$this->tips[] = array(
  									'id' => 'function_' . $function_info['id'],
  									'content' => TEXT_EXT_FUNCTION . ': ' . $function_info['functions_name'] . '<br>' . TEXT_FORMULA . ': ' . $function_info['functions_formula'] . '<br>' . $function_info['notes'],
  							);
  	
  						}
  							
  						if(!in_array($function_info['id'],$skip_funcitons_edges))
  						{
  							$this->edges[] = "{ data: { id: 'edge_field_{$fields['id']}', source: 'function_{$function_info['id']}', target: 'field_{$fields['id']}',lineColor: '#17a2b8',arrowShape:'triangle',sourceShape:'none',width:1},classes:'function' }";
  							$skip_funcitons_edges[] = $function_info['id'];
  						}
  					}
  				}
  			}
  		}
  	}  
  }
  
  function build_entities_edges()
  {
  	foreach($this->shcema as $entities)
  	{
  		//build entity node for parents entities tree
  		if(db_count('app_entities',$entities['id'],'parent_id')>0)
  		{
  			$y = $entities['y'];
  	
  			switch(true)
  			{
  				case $this->entities_fields_count[$entities['id']]>1:
  					$y = $entities['y']+(($this->entities_fields_count[$entities['id']]-1)*4);
  					break;
  			}
  	
  			$this->nodes[] = "{ data: { id: 'entity_node_" . $entities['id'] . "',name: '',faveShape:'rectangle',borderWidth:0, nodeSize: 2, faveColor:'#cccccc', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . ($entities['x']+100) . ", y: " . $y . " }}";
  			$this->edges[] = "{ data: { id: 'edge_node_{$entities['id']}_{$entities['id']}', source: 'entity_{$entities['id']}', target: 'entity_node_{$entities['id']}',lineColor: '#cccccc',arrowShape:'none',sourceShape:'none',width:2} }";
  		}
  	
  		if($entities['parent_id']>0)
  		{
  			$y = $entities['y'];
  	
  			switch(true)
  			{
  				case $this->entities_fields_count[$entities['id']]>1:
  					$y = $entities['y']+(($this->entities_fields_count[$entities['id']]-1)*4);
  					break;
  			}
  			//entity short nodes for tree
  			$this->nodes[] = "{ data: { id: 'entity_shortnode_" . $entities['id'] . "',name: '',faveShape:'rectangle',borderWidth:0, nodeSize: 2, faveColor:'#cccccc', fontSize:'7px',textValign:'top',textHalign:'center'}, position: { x: " . ($entities['x']-30) . ", y: " . $y . " }}";
  			$this->edges[] = "{ data: { id: 'edge_node_{$entities['id']}_{$entities['parent_id']}', source: 'entity_shortnode_{$entities['id']}', target: 'entity_{$entities['id']}',lineColor: '#cccccc',arrowShape:'triangle',sourceShape:'none',width:2} }";
  	
  			$this->edges[] = "{ data: { id: 'edge_{$entities['id']}_{$entities['parent_id']}', source: 'entity_node_{$entities['parent_id']}', target: 'entity_shortnode_{$entities['id']}',lineColor: '#cccccc',arrowShape:'none',sourceShape:'none',width:2} }";
  		}
  	}
  }
  
  function build_tips()
  {
  	$fields_query = db_query("select * from app_fields");
  	while($fields = db_fetch_array($fields_query))
  	{
  		$cfg = new fields_types_cfg($fields['configuration']);
  	
  		$content = '';
  	
  		switch($fields['type'])
  		{
  			case 'fieldtype_formula':
  				$content .= TEXT_FORMULA . ': ' . $cfg->get('formula') . '<br>';
  				break;
  			case 'fieldtype_entity':
  			case 'fieldtype_related_records':
  				$content .= TEXT_RELATIONSHIP_HEADING . ': ' . entities::get_name_by_id($cfg->get('entity_id')) . '<br>';
  				break;
  			case 'fieldtype_users':
  				$content .= TEXT_TYPE . ': ' . TEXT_FIELDTYPE_USERS_TITLE . '<br>';
  				break;
  			case 'fieldtype_grouped_users':
  				$content .= TEXT_TYPE . ': ' . TEXT_FIELDTYPE_GROUPEDUSERS_TITLE . '<br>';
  				break;
  		}
  	
  		$content .= $fields['notes'];
  	
  		if(strlen($content))
  		{
  			$this->tips[] = array(
  					'id' => 'field_' . $fields['id'],
  					'content' => $content,
  	
  			);
  		}
  	}
  }
  
}