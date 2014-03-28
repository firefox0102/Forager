<html>
<link rel="stylesheet" href="Report_CSS.css" type="text/css">
<title>Forager|Report</title>
<body style="width:100%;">
<div class="MAIN">
<?php
include_once ('echo_functions.php');
include_once ('extra_functions.php');
include_once ('database_array_functions.php');
include_once ('search_and_test_functions.php');
include_once ('build_interactable_report_functions.php');
//=============================================================================================================================================
//Set up Fake Forager
$SOURCE = "http://spsu.edu/";
//$SOURCE = 'http://fac-web.spsu.edu/tc/dcolebec/Fall%202010%20Arts%202001%20Webpage%20Folder/';
//$DOMAIN = "http://localhost:8080/Forager/";
$DOMAIN = $SOURCE;
$GLOBALS = array('DOMAIN' => $DOMAIN);
$link = '';
//$link = 'Arts%20Assignments.htm';
$hLink = array();
$id = 0;
$MAX = 2;
//$link = '';

$DATABASE_MAIN = array();//ID, link, source, array hlink, type ("good_link","bad_link","good_file","bad_file","undefined")
$DATABASE_FIND = array();//link, source, array hlink, mainID-ID
$DATABASE_TEST = array();//link, source, array hlink, mainID-ID

/*ECHO_BUTTON("100px","20px","http://google.com","GOOGLE");
ECHO_BUTTON("100px","20px","http://google.com","LOG IN","");
ECHO_BUTTON("100px","20px","http://google.com",true,"icon_go.png","auto","10px","#98FB98");
ECHO_BUTTON("100px","20px","http://google.com",true,"icon_left_arrow.png","10px","10px","");
ECHO_ANIMATE(250,"100px","56px","load_1_1.png","load_1_2.png","load_1_3.png","load_1_1.png","load_1_4.png","load_1_5.png");*/

array_push($DATABASE_MAIN,array("ID"=>"$id","link" => $link,"source"=>$SOURCE,"hlink"=>$hLink, "type"=>"undifined"));
//=============================================================================================================================================
//=============================================================================================================================================
//Finds all of the links in a page and organizes them to the correct data base.

do{
	ECHO_BLOCK();
	ECHO_TEXT("Starting Search");
	//=========================================================================================================================================
	//This would be a functionality for a single set of threads.
	//THREAD!!
	for($i = sizeof($DATABASE_TEST) - 1; $i >= 0; $i--)
	{
		ini_set("max_execution_time", 2);
		//Add a test case to make sure noone puts in this website... That could be bad...
		$element = $DATABASE_TEST[$i];
		ECHO_TEXT("Starting test on " . "source = " . $element['source'] . " , link = " . $element['link']);
		$newLink = Test_Connect($element['source'] . $element['link']);
		if(EXISTS($element['link'],'.css','.png','.jpg','.doc','.ppt','.pdf'))
			$type = '_file';
		else
			$type = '_link';
		if(!$newLink)
		{
			$type = 'bad' . $type;
			//writing to the database...
			$DATABASE_MAIN[GET_INFO($DATABASE_MAIN, $element['ID'])[0]]['type'] = $type;
			ECHO_ALERT(FALSE,"BAD!!");
		}
		else
		{
			$type = 'good' . $type;
			if($DOMAIN != "" && EXISTS($element['source'],$DOMAIN) && $type == "good_link")
				array_push($DATABASE_FIND,$element);
			//writing to the database...
			$DATABASE_MAIN[GET_INFO($DATABASE_MAIN, $element['ID'])[0]]['type'] = $type;
			ECHO_ALERT(true,"GOOD!!");
		}
		Remove_Index($DATABASE_TEST,$i);
	}
	//==========================================================================================================================================
	if(gettype($MAX) != "string" && $MAX <= sizeof($DATABASE_MAIN))
		break;
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//Note: Need to add a feature to the error builder that will locate any href's and such that have not been found on that page
	//and place a message that says 'This Link Was Not Scanned' and in the quick stats can add something that shows how many items were found
	//but not caught in the scan...
	//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
	//==========================================================================================================================================
	//This would be a functionality for a single set of threads.
	for($i = sizeof($DATABASE_FIND) - 1; $i >= 0; $i--)
	{
		ini_set("max_execution_time", 2);
		$element = $DATABASE_FIND[$i];
		ECHO_TEXT("Starting search on " . "source = " . $element['source'] . " , link = " . $element['link']);
		//need to make where can grab all types... an array with the types might work...
		$arr = Extract_From_Quotes_Array(Extract_Specified_Attributes_Into_Array(Can_Connect($element['source'] . $element['link']),"href","="),"=");
		foreach($arr as $elem)
		{
			if(!Exists($elem, '#'))
			{
				ECHO_TEXT($elem);
				
				if(Exists($elem,"http"))
				{
					$SourceLen = Find_Source_Length($elem);
					$a = strlen($elem) - $SourceLen;
					$arr_temp = array("ID"=>$element['ID'],"link" => substr($elem,$SourceLen,$a),"source"=>substr($elem,0,$SourceLen));
				}
				else
				{
					$arr_temp = array("ID"=>$element['ID'],"link" => $elem,"source"=>$element['source']);
				}
				if(IN_DATABASE($DATABASE_MAIN, $arr_temp))
					ECHO_ALERT(true,"Already in the database.");
				else
				{
					ECHO_TEXT("Added to be tested.");
					++$id;
					array_push($DATABASE_MAIN, array("ID"=>"$id","link" => $arr_temp['link'],"source"=>$arr_temp['source'],"hlink"=>array($element['ID']), "type"=>"undifined"));
				}
			}
		}
		
		Remove_Index($DATABASE_FIND,$i);
	}
	//=========================================================================================================================================
	//=========================================================================================================================================
	//Testing anything that is unknown in the main database.
	//THREAD!!
	$arr_temp = GET_FROM_DATABASE_TYPE($DATABASE_MAIN, "undifined");
	foreach($arr_temp as $i)
	{
		ini_set("max_execution_time", 2);
		ECHO_TEXT("Moving " . $i['source'] . $i['link'] . " to be tested.");
		array_push($DATABASE_TEST,array("ID"=>$i['ID'],"link" => $i['link'],"source"=>$i['source']));
	}
	//=========================================================================================================================================
}while(sizeof($DATABASE_TEST) > 0 || sizeof($DATABASE_FIND) > 0);
ECHO_BLOCK();
ECHO_TEXT("All branches from links have been found...");
//=============================================================================================================================================
//=============================================================================================================================================
//Making awesome display!!
	$arr_temp = GET_FROM_DATABASE_TYPE($DATABASE_MAIN, "good_link");
	foreach($arr_temp as $i)
	{
		ini_set("max_execution_time", 6);
		if($DOMAIN != "" && EXISTS($i['source'],$DOMAIN))
			ECHO_PAGE($i,$DATABASE_MAIN);
	}
//=============================================================================================================================================
?>
</div>
</div>
</body>
</html>