<?php
//=============================================================================================================================================
//Take an HTML page and edit the links to connect to a link on the page.
function BUILD_LINKS(&$HTML, $DATABASE, $PASSED)
{
	foreach($DATABASE as $i)
	{
		$filler = $i['source'];
		
		$pos = ($i['link'] != "");
		if($pos)
			$pos = strripos($HTML,$i['link']);
		
		if(gettype($pos) == "boolean")
		{
			$pos = strripos($HTML,$i['source'] . $i['link']);
			$filler = "";
		}
		$in = '#' . $i['source'] . $i['link'];
		$title = " title='FAILED:Click here to see error' ";
		if($PASSED)
		{
			if($GLOBALS['DOMAIN'] != "" && !EXISTS($i['source'],$GLOBALS['DOMAIN']))
				$title = " title='PASSED:No report made because fell outside of domain' ";
			else
				$title = " title='PASSED:Click here to see this report' ";
		}
		$HTML = substr_replace($HTML,'#' . $filler,$pos,0);
		
		$HTML = substr_replace($HTML,$title,$pos + strlen($in) + 1,0);
	}
}
//=============================================================================================================================================
//=============================================================================================================================================
//Takes an item out of an array (needed because I was trying to simulate a stack and database.)
function Remove_Index (&$array, $index) 
{ 
    if (array_key_exists($index, $array)) 
    { 
        $temp = $array[0]; 
        $array[0] = $array[$index]; 
        $array[$index] = $temp; 
        array_shift($array); 
 
        for ($i = 0 ; $i < $index ; $i++) 
        { 
            $dummy = $array[$i]; 
            $array[$i] = $temp; 
            $temp = $dummy; 
        } 
    } 
}
//=============================================================================================================================================
//=============================================================================================================================================
//Tests to see if any of the params following str are in str.
function Exists($str)
{
	if(sizeof(func_get_args(0)) > 1)
	{
		for ($i = 1; $i < sizeof(func_get_args(0)); $i++) 
		{
			$pos = strripos($str,func_get_args(0)[$i]);
			if(gettype($pos) != "boolean")
				return true;
		}
		return false;
	}
	else
	{
		return true;
	}
}
//=============================================================================================================================================
?>