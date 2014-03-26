<!DOCTYPE html>
<html>
<link rel="stylesheet" href="Report_CSS.css" type="text/css">
<title>Forager|Report</title>
<?php
include_once ('echo_functions.php');

?>
<body style="width:100%;word-wrap: break-word;">
	<div class="STOP_SCAN" style="background-color:white;width:59%;float:left;">
		<div style="width:100px;margin: 0 auto;">
		<?php ECHO_MULTIPLE_ANIMATE(250,"100%","56px","load_animation","LOAD_IMG/load_2_1.png","LOAD_IMG/load_2_2.png","LOAD_IMG/load_2_3.png",
								"LOAD_IMG/load_2_1.png","LOAD_IMG/load_2_4.png","LOAD_IMG/load_2_5.png");
		?>
		</div>
	</div>
	<div class="UPADATE_BOX" style="background-color:white;width:40%;float:left;">
		<div style="width:32px;margin: 0 auto;">

		<?php ECHO_MULTIPLE_ANIMATE(80,"100%","32px","load_progress","LOAD_IMG/loader_1.png","LOAD_IMG/loader_2.png","LOAD_IMG/loader_3.png",
								"LOAD_IMG/loader_4.png","LOAD_IMG/loader_5.png","LOAD_IMG/loader_6.png","LOAD_IMG/loader_7.png","LOAD_IMG/loader_8.png",
								"LOAD_IMG/loader_9.png","LOAD_IMG/loader_10.png","LOAD_IMG/loader_11.png","LOAD_IMG/loader_12.png");?>
		</div>
		<div style="overflow-y:scroll;width:100%;height:600px;">
		<?php 
			include_once ('threading.php');
			$temp_table = new DS();
			
			$pools = new POOLS($temp_table,1,1,1);
			
			$pools->pool_scan->submit(new SCANWORK(array('ID'=>0,'source'=>'http://localhost:8080/Forager/','link'=>'error.html')));
			$pools->shutdown();
			
		?>
		</div>
	</div>
</body>
</html>