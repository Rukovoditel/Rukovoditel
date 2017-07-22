<?php

class fieldtype_mapbbcode
{
	public $options;

	function __construct()
	{
		$this->options = array('title' => TEXT_FIELDTYPE_MAPBBCODE_TITLE);
	}

	function get_configuration()
	{
		$cfg = array();		
		$cfg[] = array('title'=>TEXT_DEFAULT_POSITION, 'name'=>'default_position','type'=>'input','tooltip'=>TEXT_DEFAULT_POSITION_TIP,'params'=>array('class'=>'form-control input-medium'));
		$cfg[] = array('title'=>TEXT_DEFAULT_ZOOM, 'name'=>'default_zoom','type'=>'input','tooltip'=>TEXT_DEFAULT_ZOOM_TIP,'params'=>array('class'=>'form-control input-xsmall'));
				
		return $cfg;
	}

	function render($field,$obj,$params = array())
	{
		$cfg =  new fields_types_cfg($field['configuration']);
		
		$attributes = array('rows'=>'3',
				'class'=>'form-control input-xlarge ' .  ($field['is_heading']==1 ? ' autofocus':'') . ' fieldtype_mapbbcode field_' . $field['id'] . ($field['is_required']==1 ? ' required':''));

		$html = textarea_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],$attributes);
						
		$map_id = $field['id'];
		
		$map_params = array();
		
		if(strlen($cfg->get('default_position'))>0)
		{
			$map_params[] = 'defaultPosition: [' . $cfg->get('default_position') . ']'; 
		}
		
		if(strlen($cfg->get('default_zoom'))>0)
		{
			$map_params[] = 'defaultZoom: ' . $cfg->get('default_zoom') ;
		}
		
		$html .= '
				<a href="javascript: mapbb' . $map_id . '_edit();"><i class="fa fa-map-marker" aria-hidden="true"></i> ' . TEXT_OPEN_MAP_EDITOR . '</a>
				<div id="mapbb' . $map_id . '_edit"></div>						
				<script>
					var mapBB' . $map_id . ' = "";	
					$(function(){		
						 mapBB' . $map_id . ' = new MapBBCode({' . implode(',' , $map_params). '});						   						
					})			
							
				  function mapbb' . $map_id . '_edit() 
					{								
					    mapBB' . $map_id . '.editor(\'mapbb' . $map_id . '_edit\', document.getElementById(\'fields_' . $map_id . '\'), function(res) {
					        if( res !== null )
					    		{					    		  	
					            $(\'#fields_' . $map_id . '\').val(res)
					        }
					    });
					}			
				</script>
		';
		
		return $html;
	}

	function process($options)
	{
		return db_prepare_input($options['value']);
	}

	function output($options)
	{		
		$html = '';
		
		if(isset($options['is_export']))
		{
			return  $options['value'];
		}
		else
		{	
			if(strlen($options['value']))
			{	
				$map_id = $options['field']['id'];
				
				$html = '
						<div id="mapbb' . $map_id . '"></div>
						<script>
							$(function(){	
								var mapBB = new MapBBCode({fullFromStart:is_mobile});
								mapBB.show(\'mapbb' . $map_id. '\', \'' . $options['value'] . '\');
							})
						</script>		
				';
			}
		}
		return $html;
	}
}