<?php
$data = array(
//zero value
		'nul' => 'zero',
		//form 1-9
		'ten' =>
		array(
				array('','one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'),
				array('','one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'),
		),
		//from 10-19
		'a20' =>
		array('ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'),
		//from 20-90
		'tens' =>
		array(2=>'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'),
		//from 100-900
		'hundred' =>
		array('','one hundred', 'two hundred', 'three hundred', 'four hundred', 'five hundred', 'six hundred', 'seven hundred', 'eight hundred', 'nine hundred'),
		//units
		'unit' =>
		array( // Units
				array('penny', 'penny', 'penny',	 1),
				array('dollar'   ,'dollars'   ,'dollars'    ,0),
				array('thousand', 'thousands', 'thousands'     ,1),
				array('million' ,'million','million' ,0),
				array('billion', 'billion', 'billions',0),
		)
);