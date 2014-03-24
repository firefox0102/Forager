<?php
include_once ('echo_functions.php');
include_once ('extra_functions.php');
include_once ('database_array_functions.php');
include_once ('search_and_test_functions.php');
//=============================================================================================================================================
function ECHO_hlink($hlink,$DATABASE)
{
	$disp = "<div class = 'BLOCK' style='background-color:#98FB98;'><p><center><h3>Links That Lead To The Above Page</h3>";
	if(sizeof($hlink) == 0)
	{
		$disp .= "No entry to this page found in the search.";
	}
	else
	{
		foreach($hlink as $j)
		{
			$element = GET_INFO($DATABASE, $j)[1];
			$disp .= "<a href=#" . $element['source'] . $element['link'] . ">";
			$disp .= $element['source'] . $element['link'];
			$disp .= "</a><br>";
		}
	}
	$disp .= "</center></p></div>";
	return $disp;
}
//=============================================================================================================================================
//=============================================================================================================================================
function BUILD_GOOD_LINKS(&$HTML, $ID, $DATABASE)
{
	$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'good_link');

	$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$ID);
	
	BUILD_LINKS($HTML, $arr,true);
}
//=============================================================================================================================================
//=============================================================================================================================================
function ECHO_ERROR(&$HTML, $i, $DATABASE)
{	

	$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'bad_file');
	$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
	//Will need to create table of contents so you can quickly jump to good pages and bad files.
	$disp = "<center>";
	if(sizeof($arr) > 0)
	{
	$disp .= "<div class = 'BLOCK' id = 'file_errors_for" . "'><a href='#" . $i['source'] . $i['link'] . "'><h3>Return To The Page</h3></a><br>";
	$disp .= "<p><h3>Internal Errors</h3></p>";
//	if(sizeof($arr)==0)
//		$disp .= "<p>No internal errors found for this page.</p>";
//	else
//	{
		foreach($arr as $j)
		{
			$disp .= "<div class = 'TEXT_BOX'><h3>". $j['source'] . $j['link'] ."</h3><p>This file cannot be found.<br>This file either does not exist, the name has changed, or there is a miss-type in the name.</p></div>";
		}
//	}
	$disp .= "</div>";
	}
	$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'bad_link');
	$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
	
	foreach($arr as $j)
	{
		$disp .= "<div class = 'BLOCK' id = '" . $j['source'] . $j['link'] . "'><a href='#" . $i['source'] . $i['link'] . "'><h3>Return To The Page</h3></a><br>";
		$disp .= "<p><h3>External Error</h3></p>";
		$disp .= "<div class = 'TEXT_BOX'><h3>". $j['source'] . $j['link'] ."</h3><p>This link is unconnectable.<br>This link either does not exist, the link has changed, or there is a miss-type in the link.</p></div></div>";
	}
	
	return $disp . "</center>";
}
//=============================================================================================================================================
//=============================================================================================================================================
function ECHO_STATS($i,$DATABASE)
{
		$disp = $disp = "<div class = 'BLOCK' style='background-color:#87CEEB;' id = 'stats_for" . $i['source'] . $i['link'] . "'><center><a href='#" . $i['source'] . $i['link'] . "'><h3>Return To The Page</h3></a></center><br>";
		$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'bad_link');
		$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
		$badL = sizeof($arr);
		$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'bad_file');
		$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
		$badF = sizeof($arr);
		$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'good_file');
		$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
		$goodF = sizeof($arr);
		$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'good_link');
		$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);
		$goodL = sizeof($arr);
		$disp .= "<p><center><h3>Quick Glance Stats</h3></center></p>";
		$disp .= "<div class = 'TEXT_BOX'>";
		$disp .= "<p><abbr style='float:left'>Amount of bad links:</abbr><abbr style='float:right'>$badL</abbr></p><br>";
		$disp .= "<p><abbr style='float:left'>Amount of bad files:</abbr><abbr style='float:right'>$badF</abbr></p><br>";
		$disp .= "<p><abbr style='float:left'>Amount of good links:</abbr><abbr style='float:right'>$goodL</abbr></p><br>";
		$disp .= "<p><abbr style='float:left'>Amount of good files:</abbr><abbr style='float:right'>$goodF</abbr></p><br></div>";
		
		$disp .= "</div>";
		return $disp;
}
//=============================================================================================================================================
//============================================================================================================================================= 
//Echoes a page into the correct report format
function ECHO_PAGE($i,$DATABASE)
{
	$HTML = Can_Connect($i['source'] . $i['link']);
	//=========================================================================================================================================
	//The white block
	$disp = "<div id='" . $i['source'] . $i['link'] . "' class='MIDDLE_PAGE'>";
	//$disp = "<div class='MIDDLE_PAGE'>";
		//=====================================================================================================================================
		//before display find all of the good and bad. Then replace with with #source.link (add mouse over later)
		$disp .= "<div class='PAGE'>";
		//$disp .= "<div id='" . $i['source'] . $i['link'] . "' class='PAGE'>";
		$disp .= "<div class='BLOCK'style='background-color:#0e855b;'><center><h3>PAGE LINK</h3>" . $i['source'] . $i['link'] . "</div></center>";
		BUILD_GOOD_LINKS($HTML, $i['ID'], $DATABASE);
		
		$arr = GET_FROM_DATABASE_TYPE($DATABASE, 'bad_link');
		$arr = GET_FROM_DATABASE_ID_IN_hlink($arr,$i['ID']);

		BUILD_LINKS($HTML, $arr,false);
		
		//$disp .= "<div id='" . $i['source'] . $i['link'] . "' class='PAGE_TITLE'><center><h3>PAGE LINK</h3>" . $i['source'] . $i['link'] . "</center></div>";
		$disp .= $HTML . "</div>";
		//=====================================================================================================================================
		//=====================================================================================================================================
		$disp .= ECHO_hlink($i['hlink'],$DATABASE);
		$disp .= ECHO_STATS($i,$DATABASE);
		$disp .= ECHO_ERROR($HTML,$i,$DATABASE);
		//=====================================================================================================================================
	$disp .= "</div>";
	//=========================================================================================================================================
	echo $disp;
}
//=============================================================================================================================================
?>