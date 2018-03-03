<?php
	session_start();

	include "db_connect.php";
	global $con;

//check for abuse first: 5 attempts per 2 minutes
	$attempts = 5 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/verify-email.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($id = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $id['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/verify-email.php', CURRENT_TIMESTAMP)");
      	echo "timeout";
      	die();
    }

	$hash = $_GET['id'];
	if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $hash . "'"))){
      	if($row['type'] == "phone_verified") mysqli_query($con, "UPDATE `accounts` SET `type` = 'both_verified' WHERE `hash` = '" . $hash . "'");
      	else mysqli_query($con, "UPDATE `accounts` SET `type` = 'email_verified' WHERE `hash` = '" . $hash . "'");
      
		header("Location: https://humanitystruth.com/index.php?notice=verification-confirmed");
	} else {
		//this link has expired
		header("Location: https://humanitystruth.com/index.php?notice=expired-link");
	}
?>