<?php
$con = mysqli_connect("localhost","root","forageme","db_forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
 }
 else{
// gets username and password
// echo back user_id and name
 




$sql = "
	SELECT *
	FROM `report`
	
";

$result = mysqli_query($con,$sql);
$row = mysqli_fetch_array($result, MYSQLI_BOTH);
if( !is_null($row) ){
	

	$data = array('user_id' => $user_id, 'name' => $name);
	$json_ob = json_encode($data);
	echo $json_ob;
}
else{
	echo "400";
}
	
} // first else statement close
?>