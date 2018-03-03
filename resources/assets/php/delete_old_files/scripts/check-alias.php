<?php
	include "db_connect.php";
	global $con;

	$response = -1; //we want a response code of 1
    if(isset($_POST) & !empty($_POST['alias'])){
        if(mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `alias`='".$_POST['alias']."'"))) $response = 0;
        else $response = 1;
    }

	echo $_GET['callback'] . '(' . "{'isAvailable' : ".json_encode($response)."}" . ')';
?>