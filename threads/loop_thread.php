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
		//ECHO_TEXT("$message");
		printf($message);
	}
	
	protected function alert_log($bool, $message)
	{
		//ECHO_ALERT($bool, "$message");
		printf($message);
	}
	
	public function block_log()
	{
		//ECHO_BLOCK();
		printf($message);
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
		//$SOURCE = "http://localhost:8080/Forager/";
		$SOURCE = "https://spsu.edu/";
		$DOMAIN = $SOURCE;
		//$GLOBALS = array('DOMAIN' => $DOMAIN);
		//$link = 'error.html';
		$link = '';
		$id = 0;
		$MAX = "";
		//$link = '';
		$temp_table = array();//source, link
		$DB_TASK = array();//FID, link, source, type ("good_link (unsearched, max, stop)","bad_link","good_file","bad_file","in_database")
		$FIND_TASK = array();//link, source, mainID-ID
		$TEST_TASK = array();//link, source, FID
		array_push($TEST_TASK,array("FID"=>-1,"link" => $link,"source"=>$SOURCE));
		do{
			$this->logger->block_log();
			$this->logger->log("Starting Search");
			//=========================================================================================================================================
			while(sizeof($TEST_TASK) > 0)
			{
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
			while(sizeof($FIND_TASK) > 0)
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
						
						if(EXISTS_IN_ARRAY($temp_table, $arr_temp))//check to see if in url table
						{
							$this->logger->alert_log(true,$arr_temp['source'] . $arr_temp['link'] . " Already in the database.");
							//Add another component to here that has an ID.
							array_push($DB_TASK, array("FID"=>$element['ID'], "type"=>"in_database"));
						}
						else
						{
							$this->logger->log("Added to be tested.");
							array_push($temp_table, $arr_temp);//remove this
							array_push($TEST_TASK, array("FID"=>$element['ID'],"link" => $arr_temp['link'],"source"=>$arr_temp['source']));
						}
					}
				}
			}
			//=========================================================================================================================================
			//Grab stop here
			//=========================================================================================================================================
			while(sizeof($DB_TASK) > 0)
			{
				$element = array_shift($DB_TASK);
				$this->logger->log("Moving " . $element['source'] . $element['link'] . " to db.");

				if($element['type'] == "in_database")
				{
					//find ID then input to link_rel
				}
				else
				{
					$type = "link";
					$state = true;
					$ID = 0;//insert into url.
					//insert into link_rel
					
					if($element['type'] == "good_link")
					{
						//if stopped change type skip this and add to database...
						array_push($FIND_TASK,array("ID"=>$ID,"link" => $element['link'],"source"=>$element['source']));
					}
				}
			}
			//=========================================================================================================================================
		}while(sizeof($TEST_TASK) > 0 || sizeof($FIND_TASK) > 0);
		$this->logger->block_log();
		$this->logger->alert_log(true,"done");
		$this->kill();
    }
}
?>