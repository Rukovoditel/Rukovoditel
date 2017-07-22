<?php

//prepare forumulas query
$formulas_sql_query_select_array = fieldtype_formula::prepare_query_select($current_entity_id, array());

//echo '<pre>';
//print_r($formulas_sql_query_select_array);
//echo '</pre>';

$sum_sql_query = array();
foreach($listing_numeric_fields as $id)
{
  $field_name = "field_" . $id;
  
  foreach($formulas_sql_query_select_array as $formulas_sql_query_select)
  {
    
    if(strstr($formulas_sql_query_select,'as ' . $field_name))
    {    
      if(preg_match('/(.*?) as ' . $field_name . '/',$formulas_sql_query_select,$matches))
      {
        //echo '<pre>';
        //print_r($matches);
        //echo '</pre>';
        
        $field_name = $matches[1];
      }
    }
  }
   
  $sum_sql_query[] = "sum(" . $field_name . ") as total_" . $id;
}

//echo '<pre>';
//print_r($sum_sql_query);
//echo '</pre>';

$totals_array = array();
$totals_query = db_query("select " . implode(', ',$sum_sql_query) . " " . $listing_sql_query_select . " from app_entity_" . $current_entity_id . " e "  . $listing_sql_query_join . " where e.id>0 " . str_replace('having','group by e.id having',$listing_sql_query));
while($totals = db_fetch_array($totals_query))
{	
	//print_r($totals);
	
	foreach($totals as $k=>$v)
	{
		if(isset($totals_array[$k]))
		{
			$totals_array[$k]+=$v;
		}
		else
		{
			$totals_array[$k]=$v;
		}
	}	
}

$totals = $totals_array;

$html .= '
  <tfoot>
    <tr>
      <td></td>
';

foreach($listing_fields as $field)
{
  if(in_array($field['id'],$listing_numeric_fields))
  {
    $value = $totals['total_' . $field['id']];
        
    $avg_value = ($value>0 ? $value/$listing_split->number_of_rows : '');
    
    $cfg = new fields_types_cfg($field['configuration']);
                    
    if(strlen($cfg->get('number_format'))>0 and strlen($value)>0)
    {
      $format = explode('/',str_replace('*','',$cfg->get('number_format')));
            
      $value = number_format($value,$format[0],$format[1],$format[2]);
      
      if(strlen($avg_value))
      {
      	$avg_value = number_format($avg_value,$format[0],$format[1],$format[2]);
      }
    }
    elseif(strstr($value,'.'))
    {
      $value = number_format($value,2,'.','');
      
      $avg_value = number_format($avg_value,2,'.','');
    }
         
    $html .= '<td class="numeric_fields_total_values">' . ($cfg->get('calclulate_totals')==1 ? $value . '<br>':'') . ($cfg->get('calculate_average')==1 ?  $avg_value:'').  '</td>';
  }
  else
  {
    $html .= '<td></td>'; 
  }
}

$html .= '
    </tr>
  </tfoot>   
';