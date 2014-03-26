<?php

$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to server.....can you be cool just once! Just once be cool!: " . mysqli_connect_error();
  exit;
 }

 $scan_running = -1;
 $last_report_index = -1;

 $sql ="
 		SELECT *
 		FROM `report`
 		WHERE is_running = 1;
 		SELECT LAST(report_id) FROM `report`; 
 ";

// Possible Test Case will be if by 
// some accident two scans get started
// how to handle that event
if (mysqli_multi_query($con, $sql)) {
    do {
        /* store first result set */
 		$result = mysqli_store_result($con);
        $row = mysqli_fetch_row($result);
        if(!is_null($row) ){ 
        	echo "Scan already in progress!";
        	exit;
        }
        mysqli_next_result($con); 
            
    } while (mysqli_next_result($con));
}


// IF scan is running then exit if not then create the two tables
 $scan_check_sql ="
 		SELECT *
 		FROM `report`
 		WHERE is_running = 1 
 ";

$scan_check_result = mysqli_query($con,$scan_check_sql);
$scan_check_row =  mysqli_fetch_array($result, MYSQLI_BOTH);

if( is_null($scan_check_row) ){
	$create_tables


}
else{






}

// Test to make sure a scan has been started
// If scan is started then block new scan
// Else allow new table creation and execution
// 




$user_name = $_POST['un']; 
$password = $_POST['password'];

$user_name = mysql_real_escape_string($user_name);
$password  = mysql_real_escape_string($password);

$sql = "
	SELECT user_id, fname, lname
	FROM `user`
	WHERE '$user_name' = user_name AND 
		  '$password' = password 
";

$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result, MYSQLI_BOTH);
if( !is_null($row) ){
	$user_id = $row["user_id"];
	$_SESSION["user_id"] = $user_id;
	$name = $row["fname"].' '.$row["lname"];
	$_SESSION["name"] = $name;

	$data = array('user_id' => $user_id, 'name' => $name);
	$json_ob = json_encode($data);
	echo $json_ob;
}
else{
	echo "400";
}
	
}





















?>