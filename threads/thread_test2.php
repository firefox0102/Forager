<html>
<?php
include_once ('echo_functions.php');
class WebWorker extends Worker {

	public function __construct(SafeLog $logger,DS $arr) {
		$this->logger = $logger;
		$this->arr = $arr;
		$this->arr[] = "JUNK";
	}
	protected $arr;
	protected $logger;
	protected $m = "MEMEMEMEME";
}

class DS extends Stackable {
	public function run() {}
}

class WebWork extends Stackable {

	public function __construct($hello,$arr)
	{
		$this->hello = $hello;
		$this->arr = $arr;
	}
	
	public function isComplete() {
		return $this->complete;
	}

	public function run() {
		$this->worker->logger->log("%s " . $this->hello . $this->worker->m ." executing in Thread #%lu", __CLASS__, $this->worker->getThreadId());
		$this->arr[] = 'sdkljflsdjflsdjflsjdflsjfljsdlfjlj';
		$this->complete = true;
	}
	
	protected $arr;
	protected $complete;
	protected $hello;
}

class SafeLog extends Stackable {

	protected function log($message, $args = []) {
		$args = func_get_args();	

		if (($message = array_shift($args))) {
			ECHO_TEXT( vsprintf("{$message}\n", $args));
		}
	}
}

$arr = new DS();
$arr[] = "WHY";
$pool = new Pool(8, \WebWorker::class, [new SafeLog(),$arr]);
$i = 0;
for($k = 0; $k < 200; ++$k)
$pool->submit($w=new WebWork("jk" . $i++,$arr));
$pool->shutdown();

$pool->collect(function($work){
	return $work->isComplete();
});

foreach($arr as $j)
	ECHO_TEXT($j);
ECHO_TEXT("END?");
//var_dump($pool);
?>
</html>