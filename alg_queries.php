<?php

$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to server.....can you be cool just once! Just once be cool!: " . mysqli_connect_error();
  exit;
}
 // in_database query
$in_database_sql ="
		INSERT INTO `report`(`report_name`,`started_by`,`start_time`,`is_running`)
		VALUES('$report_name','$started_by','$start_time','1')
";




?>