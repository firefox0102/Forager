<?php
function EXISTS_IN_ARRAY($ARRAY,$DATA)
{
	for($i = 0; $i < sizeof($ARRAY); $i++)
	{
		if(($ARRAY[$i]['link'] == $DATA['link']) && ($ARRAY[$i]['source'] == $DATA['source']))
			return true;
	}
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
//		ECHO_TEXT("$message");
	}
	
	protected function alert_log($bool, $message)
	{
//		ECHO_ALERT($bool, "$message");
	}
	
	public function block_log()
	{
//		ECHO_BLOCK();
	}
}

class LOOPSCANNER extends Thread {

	protected $logger;
	protected $start_elements;
	
    public function __construct($start_elements) {
		$this->logger = new SafeLog();
        $this->start_elements = $start_elements;
        $this->start();
    }

    public function run() {
		$SOURCE = "http://localhost:8080/Forager/";
		//$SOURCE = "https://banweb.spsu.edu/pls/PROD/";
		$DOMAIN = $SOURCE;
		$GLOBALS = array('DOMAIN' => $DOMAIN);
		$link = 'error.html';
		//$link = "twbkwbis.P_WWWLogin";
		$id = 0;
		$MAX = "";
		//$link = '';
		$temp_table = array();//source, link
		$DB_TASK = array();//FID, link, source, type ("good_link (unsearched, max, stopped)","bad_link","good_file","bad_file","in_database")
		$FIND_TASK = array();//link, source, mainID-ID
		$TEST_TASK = array();//link, source, FID
		array_push($FIND_TASK,array("ID"=>-1,"link" => $link,"source"=>$SOURCE));
		do{
			$this->logger->block_log();
			$this->logger->log("Starting Search");
			//=========================================================================================================================================
			for($i = 0; $i < sizeof($TEST_TASK); $i++)
			{
				//Add a test case to make sure noone puts in this website... That could be bad...
				$element = array_shift($TEST_TASK);
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
						if(gettype($MAX) != "string" && $MAX <= sizeof($temp_table))
							$type .= "_max";
		//				else if($stopped)
		//					$type .= "_stop"
						else if($DOMAIN != "" && !EXISTS($element['source'],$DOMAIN))
							$type .= "_unsearched";
					}
					$this->logger->alert_log(true,"GOOD!!");
				}
				//Adding to db tasks what just got tested...
				array_push($DB_TASK, array("FID"=>$element['FID'],"link" => $element['link'],"source"=>$element['source'], "type"=>$type));
			}
			//==========================================================================================================================================
			//==========================================================================================================================================
			//This would be a functionality for a single set of threads.
			for($i = 0; $i < sizeof($FIND_TASK); $i++)
			{
				$element = array_shift($FIND_TASK);
				$this->logger->log("Starting search on " . "source = " . $element['source'] . " , link = " . $element['link']);
				//need to make where can grab all types... an array with the types might work...
				$arr = Extract_From_Quotes_Array(Extract_Specified_Attributes_Into_Array(Can_Connect($element['source'] . $element['link']),"href","="),"=");
				foreach($arr as $elem)
				{
					if(!Exists($elem, '#'))
					{
						$this->logger->log($elem);
						
						if(Exists($elem,"http"))
						{
							$SourceLen = Find_Source_Length($elem);
							$a = strlen($elem) - $SourceLen;
							$arr_temp = array("link" => substr($elem,$SourceLen,$a),"source"=>substr($elem,0,$SourceLen));
						}
						else
						{
							$arr_temp = array("link" => $elem,"source"=>$element['source']);
						}
						
						if(EXISTS_IN_ARRAY($temp_table, $arr_temp))
						{
							$this->logger->alert_log(true,"Already in the database.");
							array_push($DB_TASK, array("FID"=>$element['ID'],"link" => $element['link'],"source"=>$element['source'], "type"=>"in_database"));
						}
						else
						{
							$this->logger->log("Added to be tested.");
							array_push($temp_table, $arr_temp);
							array_push($TEST_TASK, array("FID"=>$element['ID'],"link" => $arr_temp['link'],"source"=>$arr_temp['source']));
						}
					}
				}
			}
			//=========================================================================================================================================
			//=========================================================================================================================================
			for($i = 0; $i < sizeof($DB_TASK); $i++)
			{
				$element = array_shift($DB_TASK);
				$this->logger->log("Moving " . $i['source'] . $i['link'] . " to db.");
				$ID = 0;//Where was inserted into the database...
				if($element['type'] == "good_link")
					array_push($FIND_TASK,array("ID"=>$ID,"link" => $element['link'],"source"=>$element['source']));
			}
			//=========================================================================================================================================
		}while(sizeof($TEST_TASK) > 0 || sizeof($FIND_TASK) > 0);
		$this->kill();
    }
}
?>