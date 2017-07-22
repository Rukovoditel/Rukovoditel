<?php

class fieldtype_barcode
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_BARCODE_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    
    $cfg[] = array('title'=>TEXT_WIDHT, 
                   'name'=>'width',
                   'type'=>'dropdown',
                   'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
                   'tooltip_icon'=>TEXT_ENTER_WIDTH,
                   'params'=>array('class'=>'form-control input-medium'));
                         
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
    
    $cfg[] = array('title'=>TEXT_IS_UNIQUE_FIELD_VALUE, 'name'=>'is_unique','type'=>'checkbox','tooltip_icon'=>TEXT_IS_UNIQUE_FIELD_VALUE_TIP);
        
    $cfg[] = array('title'=>TEXT_HEIGHT, 'name'=>'height','type'=>'input','tooltip_icon'=>TEXT_FIELDTYPE_BARCODE_HEIGHT_TIP,'params'=>array('class'=>'form-control input-small'));
    
    $barcode_types = '<small>C39, C39+, C39E, C39E+, C93, S25, S25+, I25, I25+, C128, C128A, C128B, C128C, EAN2, EAN5, EAN8, EAN13, UPCA, UPCE, MSI, MSI+, POSTNET, PLANET, RMS4CC, KIX, IMB, CODABAR, CODE11, PHARMA, PHARMA2T</small>';
    $cfg[] = array('title'=>TEXT_FIELDTYPE_BARCODE_TYPE, 'name'=>'barcode_type','type'=>'input','tooltip_icon'=>TEXT_FIELDTYPE_BARCODE_TYPE_TIP, 'tooltip'=>$barcode_types,'params'=>array('class'=>'form-control input-medium'));
    
    $cfg[] = array('title'=>TEXT_DISPLAY_FIELD_VALUE, 'name'=>'display_field_value','type'=>'checkbox','tooltip_icon'=>TEXT_FIELDTYPE_BARCODE_DSIPLAY_TIP);
    
    $cfg[] = array('title'=>TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING, 'name'=>'template','type'=>'input','tooltip_icon'=>TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP_ICON, 'tooltip'=>TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP,'params'=>array('class'=>'form-control input-large'));
    
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg =  new fields_types_cfg($field['configuration']);
    
    $attributes = array('class'=>'form-control ' . $cfg->get('width') . 
                                 ' fieldtype_input field_' . $field['id'] . 
    														 ($field['is_heading']==1 ? ' autofocus':'') .
                                 ($field['is_required']==1 ? ' required':'') .
                                 ($cfg->get('is_unique')==1 ? ' is-unique':'') .  
    		                         (strlen($cfg->get('template')) ? ' atuogenerate-value-by-template':'')
                                );
    
    if(strlen($cfg->get('template')))
    {
    	$attributes['data-template'] = $cfg->get('template');
    }	
    
    return input_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],$attributes);
  }
  
  function process($options)
  {
    return db_prepare_input($options['value']);
  }
  
  function output($options)
  {
  	
  	if(isset($options['is_export']) and strlen($options['value']))
  	{
  		
  		$cfg =  new fields_types_cfg($options['field']['configuration']);
  		
  		$height = (strlen($cfg->get('height')) ? $cfg->get('height') : 30);
  		
  		$type = (strlen($cfg->get('barcode_type')) ? $cfg->get('barcode_type') : 'C128');
  		  		  		    		
  		$generator = new Picqer\Barcode\BarcodeGeneratorPNG();
  		$generated = $generator->getBarcode($options['value'], $type, 1.5,$height);
  		
  		$html = '<img src="data:image/png;base64,' . base64_encode($generated) . '">';
  		
  		if($cfg->get('display_field_value')==1)
  		{
  			$html = '<table><tr><td>' . $html. '</td></tr><tr><td align="center">' . $options['value'] . '</td></tr></table>';
  		}
  		
    	return $html;
  	}
  	else
  	{  		  		
  		return $options['value'];
  	}
  }
}