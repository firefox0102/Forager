<?php
//establishes connection to database on server
$con = mysqli_connect("76.122.85.17:10000","drew","forageme","forager");
if (mysqli_connect_errno()){
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
?>