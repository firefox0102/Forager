<!DOCTYPE html>
<html>
<link rel="stylesheet" href="Report_CSS.css" type="text/css">
<title>Forager|Scanning</title>
<body style="width:100%;">
<div class="MAIN">
<?php
include_once ('echo_functions.php');
include_once ('extra_functions.php');
include_once ('database_array_functions.php');
include_once ('search_and_test_functions.php');
include_once ('build_interactable_report_functions.php');
include_once ('loop_thread.php');

//To make this work go to php.ini and change short_... to on (search it and the second one is what you need to change)
$cmd = "C:\PHP5\php.exe -f C:\xampp\htdocs\Forager\execute_test.php";
exec("$cmd > /dev/null &",$arr);

ECHO_TEXT("INFORMATION");
?>
<script>

</script>
</div>
</body>
</html>