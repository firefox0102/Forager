<?php
//=============================================================================================================================================
//Reads through a html text string and finds all 
function Extract_Specified_Attributes_Into_Array($str,$search,$token)//Make one that also finds any include statements.
{
$array = array();
//Creates a template of what you are looking for.
//\s* :means a possible space.
//= :the equals that follows the href and etc.
//(\"??)([^\"]*?)\\1 :The information we want.
//(\"??) :Double or Single quotes.
//([^\"]*?) : The ending of the quotes (double or single).
//\\1 : Make sure stops grabbing at the end.
$search .= "\s*$token\s*(\"??)([^\" >]*?)\\1";
preg_match_all("/\s$search/siU", $str, $array);
//Grab the important information generated.
$array = $array[0];
return $array;
}
//=============================================================================================================================================
//=============================================================================================================================================
//Extracts information from a string (only works on cases such as Extract_From_Quotes(href="something_you_want")
function Extract_From_Quotes($str,$token)
{
	//Find the first occurrence of = and then we know where the " or ' first appears.
	$pos = stripos($str,$token);
	//Grab the substring starting from pos ignoring the = then the following ". So we add two
	//Also the length of the information between the quotes is strlen($str) - ($pos + 3)
	//substr(string, starting position, length)
	return substr($str,$pos + 2,strlen($str) - ($pos + 3));
}
//=============================================================================================================================================
//=============================================================================================================================================
//Works like Extract_From_Quotes except will do it to an entire array.
function Extract_From_Quotes_Array($array,$token)
{
	for($i = 0; $i < sizeof($array); ++$i)
	{
		$array[$i] = Extract_From_Quotes($array[$i],$token);
	}
	return $array;
}
//=============================================================================================================================================
//=============================================================================================================================================
//Extracts information from a string (only works on cases such as Extract_From_Quotes(href="something_you_want")
function Find_Source_Length($str)
{
	//Find the first occurrence of = and then we know where the " or ' first appears.
	if(strripos($str,"/") == strlen($str)-1)
		$pos = strripos($str,"/",strripos($str,"/") - 1);
	else
		$pos = strripos($str,"/");
	//Grab the substring starting from pos ignoring the = then the following ". So we add two
	//Also the length of the information between the quotes is strlen($str) - ($pos + 3)
	//substr(string, starting position, length)
	return strlen(substr($str,0,$pos + 1));
}
//=============================================================================================================================================
//=============================================================================================================================================
//Tests to see if can connect to a link.
//If can returns the contents of the link.
//If cannot then returns false.
function Can_Connect($link)
{
	$html = @file_get_contents($link);
	if(!$html)
		return false;
	return $html;
}
//=============================================================================================================================================
//=============================================================================================================================================
//Tests to see if can connect to a link.
function Test_Connect($link)
{
	$html = @file_get_contents($link);
	if(!$html)
		return false;
	return $html;
}
//=============================================================================================================================================
?>