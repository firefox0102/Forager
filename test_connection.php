<?php


 	$con = mysql_connect("localhost","root","forageme");
  	$db = mysql_select_db('db_forager');
  	if (!$con || !$db ){
    	die('Could not connect: ' . mysql_error());
  	}

$sql ="
 		UPDATE `users`
 		SET lname = Bronie
 		WHERE user_id = 1
";
$result = mysql_query($sql);
?>