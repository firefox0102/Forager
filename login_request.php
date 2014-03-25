<?php
$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
 }
 else{
// gets username and password
// echo back user_id and name
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
	
} // first else statement close
?>