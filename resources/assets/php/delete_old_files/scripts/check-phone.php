<?php
	include "db_connect.php";
	global $con;

	$response = -1; //we want a response code of 1
    if(isset($_POST) & !empty($_POST['phone'])){
        if(mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone`='".$_POST['phone']."'"))) $response = 0;
        else $response = 1;
    }

	echo $_GET['callback'] . '(' . "{'isAvailable' : ".json_encode($response)."}" . ')';
?>