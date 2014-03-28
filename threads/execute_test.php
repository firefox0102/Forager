<?php
include_once ('../server/extra_functions.php');
include_once ('../server/search_and_test_functions.php');
include_once ('loop_thread.php');
include_once ('../alg_queries.php');

$ds = new DS();
$ds[] = array('source'=>"http://spsu.edu/", 'link'=>"");
$t = new LOOPSCANNER($ds, 1, true);
?>
