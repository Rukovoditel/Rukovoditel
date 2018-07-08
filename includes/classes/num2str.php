<?php

class num2str
{
	public $data;
	
	function __construct()
	{
		$path = 'includes/languages/num2str/';
		if ($handle = opendir($path)) 
		{
			while (false !== ($entry = readdir($handle))) 
			{
				if ($entry != "." && $entry != "..") 
				{
					require('includes/languages/num2str/' . $entry);
					
					$this->data[str_replace('.php','',$entry)] = $data;
				}
			}
			closedir($handle);						
		}		
	}
	
	function prepare($text)
	{
		foreach($this->data as $code=>$data)
		{	
			if(preg_match_all('/num2str_' . $code . '((.)[^)]*)/',$text,$matches))
			{
				//echo '<pre>';
				//print_r($matches);
				
				foreach($matches[1] as $matches_key=>$number)
				{
					$number = trim(preg_replace("/[^0-9,.]/", "",$number),'.');
					$number = str_replace(',','',$number);
					if(!strlen($number)) $number = 0;
					
					//echo $matches[0][$matches_key] . ' - ' . $number . '<br>' . $this->convert($code, $number) . '<br><br>';
					
					$text = str_replace($matches[0][$matches_key] . ')',$this->convert($code, $number),$text);
				}
			}
		}
		
		return $text;
	}
	
	function convert($code,$num)
	{
		$nul=$this->data[$code]['nul'];
		$ten=$this->data[$code]['ten'];				
		$a20=$this->data[$code]['a20'];
		$tens=$this->data[$code]['tens'];
		$hundred=$this->data[$code]['hundred'];
		$unit=$this->data[$code]['unit'];
				
		//
		list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
		
		$out = array();
		
		if (intval($rub)>0) 
		{
			foreach(str_split($rub,3) as $uk=>$v) 
			{ // by 3 symbols
				if (!intval($v)) continue;
				
				$uk = sizeof($unit)-$uk-1; // unit key
				$gender = $unit[$uk][3];
				
				list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
				
				// mega-logic
				$out[] = $hundred[$i1]; # 1xx-9xx
				if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
				else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
				// units without rub & kop
				if ($uk>1) $out[]= $this->morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
			} //foreach
		}
		else $out[] = $nul;
		
		$out[] = $this->morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
		
		$out[] = $kop.' '.$this->morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
		
		return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	}
	
	function morph($n, $f1, $f2, $f5) 
	{
		$n = abs(intval($n)) % 100;
		if ($n>10 && $n<20) return $f5;
		$n = $n % 10;
		if ($n>1 && $n<5) return $f2;
		if ($n==1) return $f1;
		return $f5;
	}
}