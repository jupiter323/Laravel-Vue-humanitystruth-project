<?php
	include "db_connect.php";
	global $con;

	$response = -1; //we want a response code of 1
    if(isset($_POST) & !empty($_POST['email'])){
        if(mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `email`='".$_POST['email']."'"))) $response = 0;
        else $response = 1;
    }

	echo $_GET['callback'] . '(' . "{'isAvailable' : ".json_encode($response)."}" . ')';
?>