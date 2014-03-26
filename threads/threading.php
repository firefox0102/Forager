<?php
include_once('database_array_functions.php');
include_once('extra_functions.php');
include_once('scanner.php');
include_once('tester.php');
include_once('dbthread.php');

class DS extends Stackable {
	public function run() {}
}

class SafeLog extends Stackable {

	public function run() {}
	
	protected function log($message) 
	{
		ECHO_TEXT("$message");
	}
	protected function alert_log($bool, $message)
	{
		ECHO_ALERT($bool, "$message");
	}
}
class POOLS extends Stackable
{
	public function __construct($temp_table,$db_count,$test_count,$scan_count)
	{
		$this->pool_test = new Pool($test_count, \TESTER::class, [$this, $temp_table, new SafeLog()]);
		$this->pool_scan = new Pool($scan_count, \SCANNER::class, [$this, new SafeLog()]);
		$this->pool_db = new Pool($db_count, \MOVER::class, [$this, new SafeLog()]);
	}
	
	public $pool_scan;
	public $pool_test;
	public $pool_db;
	
	public function shutdown()
	{
		$this->pool_test->shutdown();
		$this->pool_scan->shutdown();
		$this->pool_db->shutdown();
	}
}
?>