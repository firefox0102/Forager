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
						{
							$target = array("link" => $elem,"source"=>$element['source']);
						}
						
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
?>