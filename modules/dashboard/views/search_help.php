<?php echo ajax_modal_template_header(TEXT_SEARCH_HELP) ?>

<div class="modal-body ajax-modal-width-790">

<div class="panel-group accordion" id="accordion1">

  <div class="panel panel-default">
		<div class="panel-heading">			
			<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_0"><?php echo TEXT_SEARCH_HELP_INFO_FIELDS ?></a></h4>			
		</div>
		<div id="collapse_0" class="panel-collapse in">
			<div class="panel-body"><p><?php echo TEXT_SEARCH_HELP_INFO_FIELDS_EXAMPLE ?></p>
			</div>
		</div>
	</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">			
			<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_4"><?php echo TEXT_SEARCH_HELP_INFO_CONFIGURATION ?></a></h4>			
		</div>
		<div id="collapse_4" class="panel-collapse collapse">
			<div class="panel-body"><p><?php echo TEXT_SEARCH_HELP_INFO_CONFIGURATION_DESCRIPTION ?></p>
			</div>
		</div>
	</div>

  <div class="panel panel-default">
		<div class="panel-heading">			
			<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_1"><?php echo TEXT_SEARCH_HELP_INFO_ANDOR ?></a></h4>			
		</div>
		<div id="collapse_1" class="panel-collapse collapse">
			<div class="panel-body"><p><?php echo TEXT_SEARCH_HELP_INFO_ANDOR_EXAMPLE ?></p>
			</div>
		</div>
	</div>
  
  <div class="panel panel-default">
		<div class="panel-heading">			
			<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_2"><?php echo TEXT_SEARCH_HELP_INFO_QUOTES ?></a></h4>			
		</div>
		<div id="collapse_2" class="panel-collapse collapse">
			<div class="panel-body"><p><?php echo TEXT_SEARCH_HELP_INFO_QUOTES_EXAMPLE ?></p>
			</div>
		</div>
	</div>
  
  <div class="panel panel-default">
		<div class="panel-heading">			
			<h4 class="panel-title"><a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_3"><?php echo TEXT_SEARCH_HELP_INFO_BRACKETS ?></a></h4>			
		</div>
		<div id="collapse_3" class="panel-collapse collapse">
			<div class="panel-body"><p><?php echo TEXT_SEARCH_HELP_INFO_BRACKETS_EXAMPLE ?></p>
			</div>
		</div>
	</div>
    
</div>  
  
</div>

<?php echo ajax_modal_template_footer('hide-save-button') ?>