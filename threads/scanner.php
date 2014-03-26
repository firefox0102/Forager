<?php
class SCANNER extends Worker
{
    public function __construct(POOLS &$pools, SafeLog $logger) {
		$this->pools = $pools;
		$this->logger = $logger;	
    }
	
	protected $pools;
	protected $logger;
}
class SCANWORK extends Stackable {
	protected $elem;
	protected $complete;
	
	public function __construct($elem)
	{
		$this->elem = $elem;
	}
	
	public function isComplete() 
	{
		return $this->complete;
	}
	
	public function run() {
		//ECHO
        $this->worker->logger->log("Starting search on " . "source = " . $this->elem['source'] . " , link = " . $this->elem['link']);
		//need to make where can grab all types... an array with the types might work...
		$arr = Extract_From_Quotes_Array(Extract_Specified_Attributes_Into_Array(Can_Connect($this->elem['source'] . $this->elem['link']),"href","="),"=");
		foreach($arr as $elem)
		{
			if(Exists($elem,"http"))
			{
				//PROBLEM: The source is being added on incorrectly... Getting something//something
				$SourceLen = Find_Source_Length($elem);
				$a = strlen($elem) - $SourceLen;
				$arr_temp = array("FID"=>$this->elem['ID'],"link" => substr($elem,$SourceLen,$a),"source"=>substr($elem,0,$SourceLen));
			}
			else
			{
				$arr_temp = array("FID"=>$this->elem['ID'],"link" => $elem,"source"=>$this->elem['source']);
			}

			$this->worker->logger->log($arr_temp['source'] . $arr_temp['link'] . " was found and is now ready for testing.");
			//Submit to pool_test
			$this->pool_test->submit(new TESTWORK($arr_temp));
			//place into temp_table
			//if(!IN_DATABASE($this->worker->temp_table, $this->elem))
			//	$this->worker->temp_table[] = array("source"=>$arr_temp['source'],"link"=>$arr_temp['link']);
		}
		$this->complete = true;
    }
}
//=============================================================================================================================================
function Find_Source_Length($str) {
	if(strripos($str,"/") == strlen($str)-1)
		$pos = strripos($str,"/",strripos($str,"/") - 1);
	else
		$pos = strripos($str,"/");
	return strlen(substr($str,0,$pos + 1));
}
//=============================================================================================================================================
//=============================================================================================================================================
//Extracts information from a string (only works on cases such as Extract_From_Quotes(href="something_you_want")
function Extract_From_Quotes($str,$token) {
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
function Extract_From_Quotes_Array($array,$token) {
	for($i = 0; $i < sizeof($array); ++$i)
	{
		$array[$i] = $this->Extract_From_Quotes($array[$i],$token);
	}
	return $array;
}
//=============================================================================================================================================
//=============================================================================================================================================
//Reads through a html text string and finds all 
function Extract_Specified_Attributes_Into_Array($str,$search,$token) {
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
//Tests to see if can connect to a link.
//If can returns the contents of the link.
//If cannot then returns false.
function Can_Connect($link) {
	$html = @file_get_contents($link);
	if(!$html)
		return false;
	return $html;
}
//=============================================================================================================================================
?>