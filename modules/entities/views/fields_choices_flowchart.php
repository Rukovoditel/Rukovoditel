<?php require(component_path('entities/navigation')) ?>
<?php 

	$flowchart = new fields_choices_flowchart;
	$flowchart->prepare_data(_get::int('fields_id'));
	
	$field_info = db_find('app_fields',_get::int('fields_id'));
?>

<h3 class="page-title"><?php echo   $field_info['name'] . ': '.  TEXT_FLOWCHART ?></h3>

<div class="row">
	<div class="col-md-3">
		<?php echo '<a class="btn btn-default" href="' . url_for('entities/fields_choices','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']) . '">' . TEXT_BUTTON_BACK. '</a>' ?>		
	</div>
	<div class="col-md-8" style="text-align: right;">		 		 
		 <span class="label" style="background-color: #eaac44"><?php echo TEXT_FILTERS ?></span>		 
		 <span class="label" style="background-color: #68b857;"><?php echo TEXT_FIELDS ?></span>
	</div>
</div>
<br>
<div id="flowchart" class="flowchart" style="height: <?php echo $flowchart->height ?>px;"></div>

<script>
$(function(){

	var cy = window.cy = cytoscape({
	  container: document.getElementById('flowchart'),

	  boxSelectionEnabled: false,
	  autounselectify: true,
	  wheelSensitivity: 0.1,	  

	  style: [
	    {
	      selector: 'node.choice_filter',
	      css: {
	      	'shape': 'diamond',		      
	        'content': 'data(name)',
	        'text-valign': 'top',
	        'text-halign': 'left',
	        'background-color': '#f0ad4e',
	        'font-size':'5px',
	        'text-wrap': 'wrap',
	        'height': '30',
	        'width': '30',
		        	        	  		          
	      }
	    },
	    {
	      selector: 'node.choice',
	      css: {
	      	'shape': 'rectangle',		      
	        'content': 'data(name)',
	        'text-valign': 'center',
	        'text-halign': 'right',
	        'background-color': '#5cb85c',
	        'font-size':'7px',
	        'text-wrap': 'wrap',
	        'height': '15',
	        'width': '15',
		        	        	  		          
	      }
	    },
	    {
				selector: 'node',
				css: {					
					"overlay-padding": "3px"					      
				}
			},
	    {
	      selector: 'edge',
	      css: {
	        'target-arrow-shape': 'triangle',
	        'width': 1,
	        'curve-style': 'bezier',
	        'content': 'data(label)',
	        'font-size': '5',
	        'line-color': '#c3c3c3',
	        'target-arrow-color': '#c3c3c3',
	        'arrow-scale': 0.7,
	        "overlay-padding": "3px"	        
	          
	      }
	    },
	  ],

	  elements: {
	    nodes: [
	      <?php echo str_replace('<br>','\n',implode(",\n",$flowchart->nodes)) ?>
	    ],
	    edges: [
				<?php echo implode(",\n",$flowchart->edges) ?>
	    ]
	  },

	  layout: {
	    name: 'preset',
	    padding: 25
	  }
	});

	cy.$('node').on('click', function(e){
	  var node = e.target;
	  if(node.id().indexOf('choice_filter_')!=-1)
	  {
	  	window.open('<?php echo url_for('entities/fields_choices_filters','entities_id=' . _get::int('entities_id') . '&fields_id=' . _get::int('fields_id')) ?>&choices_id='+node.id().replace('choice_filter_',''), '_blank');
	  	return true;
	  }  
	});
})

</script>

<script src="js/cytoscape.js-master/dist/cytoscape.min.js"></script>