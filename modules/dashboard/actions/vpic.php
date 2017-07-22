<?php

switch($app_module_action)
{
	case 'input_vin_decode':
		
		//get field info
		$field_info_query = db_query("select * from app_fields where id='" . db_input($_POST['field_id']). "'");
		if(!$field_info = db_fetch_array($field_info_query))
		{			
			exit();
		}
		
		$field_cfg = new fields_types_cfg($field_info['configuration']);
		
		$vin_number = db_prepare_input(trim($_POST['vin_number']));
		
		//check if exist VIN number
		if(!strlen($vin_number))
		{
			echo TEXT_ERROR_REQUIRED;
			exit();
		}
		
		//Decode Vin
		//Example from API https://vpic.nhtsa.dot.gov/api/Home/Index/LanguageExamples
		
		$opts = array('http' =>
				array(
						'method' => 'GET',
						'content' => ''
				)
		);

		//old
		//$apiURL = "https://vpiclist.cdan.dot.gov/vpiclistapi/vehicles/DecodeVin/" . $vin_number . "?format=json";
		
		$apiURL ="https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/" . $vin_number . "?format=json";
		
		$context = stream_context_create($opts);
		$fp = fopen($apiURL, 'rb', false, $context);
		
		if(!$fp)
		{
			echo "Error: can't create stream";
			exit();
		}
		
		$response = @stream_get_contents($fp);
		
		if($response == false)
		{
			echo "Error: no response";
			exit();
		}
		else 
		{
			$response_array = json_decode($response,true);
			//echo '<pre>';
			//print_r($response_array);
			
			$html = '';
			
			//set feilds which will be availabe in popup
			$use_varialbles = array(
					'Error Code',
					'Make',
					'Manufacturer Name',
					'Model',
					'Model Year',
					'Vehicle Type',
					'Body Class');
			
			//add extra details names form configuraiton
			if(strlen(trim($field_cfg->get('other_details')))>0)
			{
				$use_varialbles = array_merge($use_varialbles,explode(',',trim($field_cfg->get('other_details'))));
			}
			
			//build result if exist
			if(is_array($response_array['Results']))
			{
				$auto_fill_fields = array();
				
				foreach($response_array['Results'] as $val)
				{
					//skip details if not used
					if(!in_array($val['Variable'],$use_varialbles)) continue;
					
					//handle errors
					if($val['VariableId']==143)
					{
						if($val['ValueId']!='0')
						{
							$html .= '<tr><td colspan="2"><b>Warning: ' . $val['Value'] . '</b></td></tr>';
						}
					}
					else 
					{
						//build html table
						$html .= '
								<tr>
									<td>' . $val['Variable'] . ':&nbsp;</td>
									<td>' . $val['Value'] . '</td>
								</tr>';
					}
					
					//buld list filed to auto fill
					if($field_cfg->get('auto_fill_fields')==1)
					{
						$check_field_query = db_query("select * from app_fields where entities_id='" . db_input($field_info['entities_id']) . "' and name='" . db_input($val['Variable']) . "'");
						if($check_field = db_fetch_array($check_field_query))
						{
							$auto_fill_fields[$check_field['id']] = $val['Value'];
						}
					}
					
				}
				
				$html = '<table style="max-width: 500px;">' . $html . '</table>';
				
				//js code for auto fill fields
				if(count($auto_fill_fields)>0)
				{
					//print_r($auto_fill_fields);
					$auto_fill_fields_js = "var auto_fill_fields = new Array();\n";
					
					foreach($auto_fill_fields as $k=>$v)
					{
						$auto_fill_fields_js .= "auto_fill_fields[" . $k . "]='" . addslashes($v) . "';\n";
					}
					
					$html .='
							<script>
								' . $auto_fill_fields_js . '
								for (var key in auto_fill_fields) 
								{
									//alert(key)
									$("#fields_"+key).val(auto_fill_fields[key])
								}
							</script>
							';
				}
			}
			
			echo $html;
			
		}
						
		exit();
		
		break;
}