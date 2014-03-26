<?php
class MOVER extends Worker {
     public function __construct(POOLS &$pools, SafeLog $logger) {
		$this->pools = $pools;
		$this->logger = $logger;	
    }
	
	protected $pools;
	protected $logger;
}
class DBWORK extends Stackable
{
	protected $elem;
	protected $complete;
	
    public function __construct($elem) {
        $this->elem = $elem;
    }
	
	public function isComplete() 
	{
		return $this->complete;
	}
	
	public function run()
	{
		if($this->elem['type'] == "in_database")
		{
			//update relation!!
		}
		else
		{
			//Place into database and such then grab ID of what was just added.(need to still check to see if in database)
			$ID = 0;
			//If a good_link, automatically send to pool_scan
			if($this->elem['type'] == "good_link")//Block flow here!!!
			{
				$this->worker->pools->pool_scan->submit(new SCANWORK(array("ID"=>$this->$ID,"link" => $elem,"source"=>$this->elem['source'])));
			}
		}
		$this->complete = true;
	}
}

?>