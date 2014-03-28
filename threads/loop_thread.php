<?php
session_start();
$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to server.....can you be cool just once! Just once be cool!: " . mysqli_connect_error();
  exit;
}

// return url_id from url_ table
function do_check_url($source, $link){
	$url_table = "url_".$_SESSION["current_id"];
	$sql ="
		SELECT url_id
		FROM `'$url_table'`
		WHERE source = '$source' AND
			  link = '$link'
	";
	$result = mysqli_query($con,$sql);
	$row = mysqli_fetch_row($result);
	if( !is_null($row) ){
		return $row[0];
	}
	else{
		return NULL;
	}
}

// insert into url_ table AND link_rel table
function do_insert_url($source, $link, $type, $state){
	$url_table = "url_".$_SESSION["current_id"]; 
	$sql ="
		INSERT INTO `'$url_table'`(`link`,`source`,`type`,`state`)
		VALUES('$link','$source','$type','$state')
	";
	$result = mysqli_query($con,$sql);
	$last_insert = mysqli_insert_id($con);
	return $last_insert;
}


// insert into link_rel table only
function do_insert_link_rel($url_id, $dest_id){
	$link_rel_table = "link_rel_".$_SESSION["current_id"]; 
	$sql ="
		INSERT INTO `'$link_rel_table'`(`url_id`,`dest_id`)
		VALUES('$url_id','$dest_id')
	";
	$result = mysqli_query($con,$sql);
}


// checks scan table to see if any scan is running
function do_check_running(){
	$sql ="
		SELECT *
		FROM `scan`
		WHERE is_running = 1
	";
	$result = mysqli_query($con,$sql);
	$row = mysqli_fetch_row($result);
	if( is_null($row) ){
		return 0;
	}
	else{
		return 1;
	}
}	
//===================================================================================================================================================
//included functions
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
//===================================================================================================================================================
function EXISTS_IN_ARRAY($ARRAY,$DATA)
{
	for($i = 0; $i < sizeof($ARRAY); $i++)
	{
		if(($ARRAY[$i]['link'] == $DATA['link']) && ($ARRAY[$i]['source'] == $DATA['source']))
			return true;
	}
	return false;
}
function Contain_in_array($arr, $search)
{
	foreach($arr as $i)
		if(Exists($search, $i))
			return true;
	return false;
}

class DS extends Stackable {
    public function run() {
    }
}

class SafeLog extends Stackable {
	public function run() {}
	protected function log($message) 
	{
		//ECHO_TEXT("$message");
		printf($message . "<br>");
	}
	
	protected function alert_log($bool, $message)
	{
		//ECHO_ALERT($bool, "$message");
		if($bool)
			printf("$--" . $message . "<br>");
		else
			printf("!--" . $message . "<br>");
	}
	
	public function block_log()
	{
		//ECHO_BLOCK();
		printf("<br>");
	}
}

class LOOPSCANNER extends Thread {

	protected $logger;
	protected $start_elements;
	protected $MAX;
	protected $USE_DOMAIN;
	
    public function __construct($start_elements, $MAX, $USE_DOMAIN) {
		$this->logger = new SafeLog();
        $this->start_elements = $start_elements;
		$this->MAX = $MAX;
		$this->USE_DOMAIN = $MAX;
        $this->start();
    }

    public function run() {
		$DOMAIN = new DS();//source
		
		$DB_TASK = new DS();//FID, link, source, type ("good_link (unsearched, max, stop)","bad_link","good_file","bad_file","in_database")
		$FIND_TASK = new DS();//link, source, ID
		$TEST_TASK = new DS();//link, source, FID
		
		$count = 0;
		foreach($this->start_elements as $elem)
		{
			$TEST_TASK[] = array("FID"=>-1,"link" => $elem['link'],"source"=>$elem['source']);
			if($this->USE_DOMAIN)
				$DOMAIN[] = $elem['source'];
		}
		do{
			$this->logger->block_log();
			$this->logger->log("Starting Search");
			//=========================================================================================================================================
			ini_set("max_execution_time", 2);
			while(sizeof($TEST_TASK) > 0)
			{
				$element = $TEST_TASK->POP();
				$this->logger->log("Starting test on " . "source = " . $element['source'] . " , link = " . $element['link']);
				$newLink = Test_Connect($element['source'] . $element['link']);
				if(Exists($element['link'],'.css','.png','.jpg','.doc','.ppt','.pdf','.pcf'))
					$type = '_file';
				else
					$type = '_link';
				if(!$newLink)
				{
					$type = 'bad' . $type;
					$this->logger->alert_log(FALSE,"BAD!!");
				}
				else
				{
					$type = 'good' . $type;
					if($type == "good_link")
					{
						if(gettype($this->MAX) != "string" && $this->MAX <= $count)
							$type .= "_max";
						else if($this->USE_DOMAIN && !Contain_in_array($DOMAIN, $element['source']))
							$type .= "_unsearched";
					}
					$this->logger->alert_log(true,"GOOD!!");
				}
				$this->logger->log("TYPE IS NOW: $type");
				//Adding to db tasks what just got tested...
				$DB_TASK[] = array("FID"=>$element['FID'],"link" => $element['link'],"source"=>$element['source'], "type"=>$type);
			}
			//==========================================================================================================================================
			//==========================================================================================================================================
			ini_set("max_execution_time", 2);
			//This would be a functionality for a single set of threads.
			while(sizeof($FIND_TASK) > 0)
			{
				$element = $FIND_TASK->POP();
				$this->logger->log("Starting search on " . "source = " . $element['source'] . " , link = " . $element['link']);
				//need to make where can grab all types... an array with the types might work...
				$arr = Extract_From_Quotes_Array(Extract_Specified_Attributes_Into_Array(Can_Connect($element['source'] . $element['link']),"href","="),"=");
				foreach($arr as $elem)
				{
					//make sure not some weird reference to a spot on the page...
					if(!Exists($elem, '#'))
					{
						$this->logger->log($elem);
						
						//Configure target...
						if(Exists($elem,"http"))
						{
							$SourceLen = Find_Source_Length($elem);
							$a = strlen($elem) - $SourceLen;
							$target = array("link" => substr($elem,$SourceLen,$a),"source"=>substr($elem,0,$SourceLen));
						}
						else
							$target = array("link" => $elem,"source"=>$element['source']);
						
						//Checks to see if already been tested
						$IN = do_check_url($target['source'],$target['link']);
						if($IN == NULL)//check to see if in url table
						{
							//insert into database here as well then add attr that has ID.
							$this->logger->log("Added to be tested.");
							$TEST_TASK[] = array("FID"=>$element['ID'],"link" => $target['link'],"source"=>$target['source']);
						}
						else
						{
							//if already in the url table update the link_rel table
							$this->logger->alert_log(true,$target['source'] . $target['link'] . " Already in the database.");
							do_insert_link_url($IN, $element['ID']);
						}
						$count++;
					}
				}
			}
			//=========================================================================================================================================
			//Grab stop here
			$running = do_check_running();
			//=========================================================================================================================================
			ini_set("max_execution_time", 2);
			while(sizeof($DB_TASK) > 0)
			{
				$element = $DB_TASK->POP();
				$this->logger->log("Moving " . $element['source'] . $element['link'] . " to db.");
				
				//can condense to less lines of code to make faster...
				//=======================================
				//Place into url table
				$state = Exists($element['type'],'good');
				
				//check to see if running
				if(!$running && $element['type'] == "good_link")
					$element['type'] .= "_stop";
					
				$ID = do_insert_url($element['source'], $element['link'], substr($element['type'], 3 + (int)$state, strlen($element['type']) - (4 + (int)$state)), $state);
				//=======================================
				//Place into link_rel
				do_insert_link_rel($ID, $element['FID']);
				
				//if running and a good link, place into the FIND_TASK...
				if($running && $element['type'] == "good_link")
					$FIND_TASK[] = array("ID"=>$ID,"link" => $element['link'],"source"=>$element['source']);
			}
			//=========================================================================================================================================
		}while(sizeof($TEST_TASK) > 0 || sizeof($FIND_TASK) > 0);
		$this->logger->block_log();
		$this->logger->alert_log(true,"done");
		$this->kill();
    }
}
//===================================================================================================================================================
//Run the thread!!!
$ds = new DS();
$ds[] = array('source'=>"http://spsu.edu/", 'link'=>"");
$t = new LOOPSCANNER($ds, 1, true);
//===================================================================================================================================================
?>