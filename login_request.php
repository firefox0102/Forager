<?php
$con = mysqli_connect("localhost","root","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
 }
 else{
// gets username and password
// echo back user_id and name
// "un" = user_name
// "password" =  password
$user_name = $_POST['un']; 
$password = $_POST['password'];

$user_name = mysqli_real_escape_string($user_name);
$password  = mysqli_real_escape_string($password);

$sql = "
	SELECT user_id, fname, lname
	FROM `user`
	WHERE 'apfundst' = user_name AND 
		  'yerp' = password 
";

$result = mysqli_query($con,$sql);
if(mysqli_num_rows($result)){
	$row = mysqli_fetch_array($result);
	$user_id = $row['user_id'];
	$_SESSION['user_id'] = $user_id;
	$name = $row['fname'].' '.$row['lname'];
	$_SESSION['name'] = $name;

	$data = array('user_id' => $user_id, 'name' => $name);
	$json_ob = json_encode($data);
	echo $json_ob;
}
	
} // first else statement close
?>