<?php require(component_path('entities/navigation')) ?>

<h3 class="page-title"><?php echo TEXT_NAV_ITEM_PAGE_CONFIG ?></h3>

<?php $default_selector = array('1'=>TEXT_YES,'0'=>TEXT_NO); ?>

<?php echo form_tag('cfg', url_for('entities/item_page_configuration','action=save&entities_id=' . $_GET['entities_id']),array('class'=>'form-horizontal')) ?>

<?php 
	
	$choices = array();
	$choices['3-9'] = '20% - 80%';
	$choices['4-8'] = '30% - 70%';
	$choices['5-7'] = '40% - 60%';
	$choices['6-6'] = '50% - 50%';
	$choices['7-5'] = '60% - 40%';
	$choices['8-4'] = '70% - 30%';
	$choices['9-3'] = '80% - 20%';
?>
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_COLUMNS_SIZE; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[item_page_columns_size]',$choices, $cfg->get('item_page_columns_size','8-4'),array('class'=>'form-control input-small')); ?> 
      <?php echo tooltip_text(TEXT_ITEM_PAGE_COLUMNS_SIZE) ?>
    </div>			
  </div>

<?php 
	$choices = array();
	$choices['1'] = TEXT_ONE_COLUMN;
	$choices['2'] = TEXT_TWO_COLUMNS;
?>
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_ITEM_DETAILS_POSITION; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[item_page_details_columns]',$choices, $cfg->get('item_page_details_columns','2'),array('class'=>'form-control input-medium')); ?> 
      <?php echo tooltip_text(TEXT_ITEM_DETAILS_POSITION_INFO) ?>
    </div>			
  </div>  
  
<?php 	
	$choices = array();
	$fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.is_heading!=1 and f.entities_id='" . db_input(_get::int('entities_id')). "' and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name");
	while($fields = db_fetch_array($fields_query))
	{
		$choices[$fields['tab_name']][$fields['id']] = $fields['name'];
	}
?>
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_HIDEN_FIELDS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[item_page_hidden_fields][]',$choices, $cfg->get('item_page_hidden_fields',''),array('class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple')); ?> 
      <?php echo tooltip_text(TEXT_ITEM_HIDDEN_PAGE_INFO) ?>
    </div>			
  </div>  
  
<?php 
	$choices = array();
	$choices['left_column'] = TEXT_LEFT_COLUMN;
	$choices['right_column'] = TEXT_RIGHT_COLUMN;
?>
  <div class="form-group">
  	<label class="col-md-3 control-label" for="cfg_menu_title"><?php echo TEXT_COMMENTS; ?></label>
    <div class="col-md-9">	
  	  <?php echo select_tag('cfg[item_page_comments_position]',$choices, $cfg->get('item_page_comments_position','left'),array('class'=>'form-control input-medium')); ?> 
    </div>			
  </div>   
  
<?php 
//configure subentites
	$html = '';
	$entities_query = db_query("select * from app_entities where parent_id = '" . db_input(_get::int('entities_id')) . "'");
	if(db_num_rows($entities_query))
	{	
		$html .= '
			<h3 class="form-section">' . TEXT_SUB_ENTITIES . '</h3>
			<p>' . TEXT_ITEM_DETAILS_SUM_ENTITIES . '</p>
		';
		
		$choices = array();
		$choices[''] = '';
		$choices['left_column'] = TEXT_LEFT_COLUMN;
		$choices['right_column'] = TEXT_RIGHT_COLUMN;
		
		$default_selector = array('0'=>TEXT_NO,'1'=>TEXT_YES);
		
		while($entities = db_fetch_array($entities_query))
		{
			$html .= '
			<div class="form-group" style="margin-bottom: 0px;">
		  	<label class="col-md-3 control-label" for="cfg_menu_title">' . $entities['name']. '</label>
		    <div class="col-md-9">
					<ul class="list-inline">  			
		  	  	<li>' . select_tag('cfg[item_page_subentity' . $entities['id'] . '_position]',$choices, $cfg->get('item_page_subentity' . $entities['id'] . '_position'),array('class'=>'form-control input-medium')) . '</li>
  	  			<li><a href="' . url_for('entities/parent_infopage_filters','entities_id=' .$entities['id']) . '" title="' . TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . TEXT_CONFIGURE_FILTERS . '</a></li>
					</ul>   				
		    </div>			
		  </div> 
      <div class="form-group">
		  	<label class="col-md-3 control-label" for="cfg_menu_title">' . tooltip_icon(TEXT_LISTING_HEADING_TOOLTIP) . TEXT_LISTING_HEADING . '</label>
		    <div class="col-md-9">
					' . input_tag('cfg[item_page_subentity' . $entities['id'] . '_heading]', $cfg->get('item_page_subentity' . $entities['id'] . '_heading'),array('class'=>'form-control input-medium')) . '
  				<div style="padding-top: 5px;"><ul class="list-inline"><li>' . TEXT_HIDE_IN_TOP_MENU . ' </li><li>' . select_tag('cfg[hide_subentity' . $entities['id'] . '_in_top_menu]',$default_selector,$cfg->get('hide_subentity' . $entities['id'] . '_in_top_menu'), array('class'=>'form-control input-msmall')) . '</li></ul></div>
		    </div>			
		  </div> 		
			';
		}
	}
	
	echo $html;
	
//configure entites related by field Entity
	$html = '';
	
	$choices = array();
	$choices[''] = '';
	$choices['left_column'] = TEXT_LEFT_COLUMN;
	$choices['right_column'] = TEXT_RIGHT_COLUMN;
	
	$fields_query = db_query("select id, name, configuration, entities_id from app_fields where entities_id!='" . db_input(_get::int('entities_id'))  . "' and type='fieldtype_entity'");
	while($fields = db_fetch_array($fields_query))
	{
		$field_cfg = new fields_types_cfg($fields['configuration']);
		
		if($field_cfg->get('entity_id')==_get::int('entities_id'))
		{
			$entities = $app_entities_cache[$fields['entities_id']];
			$html .= '
			<div class="form-group" style="margin-bottom: 0px;">
		  	<label class="col-md-3 control-label" for="cfg_menu_title">' . $fields['name'] . ' ('.  $entities['name']. ')</label>
		    <div class="col-md-9">
					<ul class="list-inline">
		  	  	<li>' . select_tag('cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position]',$choices, $cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_position'),array('class'=>'form-control input-medium')) . '</li>
  	  			<li><a href="' . url_for('entities/infopage_entityfield_filters','entities_id=' .$entities['id'] . '&related_entities_id=' . _get::int('entities_id') . '&fields_id=' . $fields['id']) . '" title="' . TEXT_CONFIGURE_FILTERS . '"><i class="fa fa-cogs" aria-hidden="true"></i> ' . TEXT_CONFIGURE_FILTERS . '</a></li>
					</ul>
		    </div>
		  </div>
      <div class="form-group">
		  	<label class="col-md-3 control-label" for="cfg_menu_title">' . tooltip_icon(TEXT_LISTING_HEADING_TOOLTIP) . TEXT_LISTING_HEADING . '</label>
		    <div class="col-md-9">
					' . input_tag('cfg[item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading]', $cfg->get('item_page_field' . $fields['id'] . '_entity' . $entities['id'] . '_heading'),array('class'=>'form-control input-medium')) . '  				
		    </div>
		  </div>
			';
		}
	}
	
	if(strlen($html))
	{
		$html = '
				<h3 class="form-section">' . TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY . '</h3>
				<p>' . TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY_INFO . '</p>
			' . $html;
	}
	
	echo $html;
	
		
?>  

<?php echo submit_tag(TEXT_BUTTON_SAVE) ?>

</form>


<script>
  $(function() {   
    $('.tooltips').tooltip();                                                                             
  });
</script>    



