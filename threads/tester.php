<?php
class TESTER extends Worker
{
    public function __construct(POOLS &$pools, DS &$temp_table, SafeLog $logger) {
		$this->pools = $pools;
		$this->logger = $logger;	
		$this->temp_table = &$temp_table;
    }
	
	protected $pools;
	protected $logger;
	protected $temp_table;
}
class TESTWORK extends Stackable 
{
	protected $complete;
	protected $elem;
	
    public function __construct($elem) {
        $this->elem = $elem;
    }

	public function isComplete() 
	{
		return $this->complete;
	}
	
    public function run() {
		if(IN_DATABASE($this->worker->temp_table, $this->elem))
		{
			//ECHO
			$this->worker->logger->alert_log(true, $arr_temp['source'] . $arr_temp['link'] . " is already in database.");
			//Submit to pool_db
			$this->woker->pools->pool_db->submit(new DBWORK(array("FID"=>$this->elem['FID'],"link" => $elem,"source"=>$this->elem['source'],"type"=>"in_database")));
		}
		else
		{
			$newLink = $this->Test_Connect($this->elem['source'] . $this->elem['link']);
			if(EXISTS($this->elem['link'],'.css','.png','.jpg','.doc','.ppt','.pdf'))
				$type = '_file';
			else
				$type = '_link';
			if(!$newLink)
			{
				$type = 'bad' . $type;
				//ECHO
				$this->worker->logger->alert_log(FALSE,$this->elem['source'] . $this->elem['link']);
			}
			else
			{
				$type = 'good' . $type;
				//ECHO
				$this->worker->logger->alert_log(true, $this->elem['source'] . $this->elem['link']);
			}
			$this->worker->temp_table[] = array("FID"=>$this->elem['FID'],"link" => $elem,"source"=>$this->elem['source'],"type"=>$type);
			
			//Submit to pool_db
			$this->woker->pools->pool_db->submit(new DBWORK(array("FID"=>$this->elem['FID'],"link" => $elem,"source"=>$this->elem['source'],"type"=>$type)));
		}
		$this->complete = true;
    }
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
}

?>