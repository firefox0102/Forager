<?php

$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to server.....can you be cool just once! Just once be cool!: " . mysqli_connect_error();
  exit;
}

$report_name = $_POST["report_name"];
$started_by  = $_POST["started_by"];
$start_time  = date('H:i:s');
//DON'T NEED MAX FOR PHASE 3
//$max		 = $_POST["max"];

$scan_running_sql ="
 		SELECT *
 		FROM `report`
 		WHERE is_running = 1;
";
$result = mysqli_store_result($con);
$row = mysqli_fetch_row($result);
if(is_null($row) ){ 
    echo "Scan already in progress!";
    exit;
}

//IF max is not set then insert null...
$new_report_sql ="
		INSERT INTO `report`(`report_name`,`started_by`,`start_time`,`is_running`)
		VALUES('$report_name','$started_by','$start_time','1')
"; 
$result = mysqli_query($con,$new_report_sql);
$last_report_id = mysqli_insert_id($con);
// Creates a new url table for the scan
$new_url_table_name      = "url_".$last_report_id;
$new_link_rel_table_name = "link_rel_".$last_report_id;

$new_url_table_sql ="
		CREATE TABLE IF NOT EXISTS `'$new_url_table_name'` (
  		`url_id` int(11) NOT NULL AUTO_INCREMENT,
  		`report_id` int(11) NOT NULL,
  		`link` varchar(1000) NOT NULL,
  		`source` varchar(1000) NOT NULL,
  		`type` varchar(1000) NOT NULL,
  		`state` tinyint(1) NOT NULL,
  		PRIMARY KEY (`url_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

		CREATE TABLE IF NOT EXISTS `'$new_link_rel_table_name'` (
  		`url_id` int(11) NOT NULL,
  		`dest_id` int(11) NOT NULL,
  		`report_id` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
";
$result = mysqli_query($con,$new_url_table_sql);
// Then begin the threading adventure...

?>