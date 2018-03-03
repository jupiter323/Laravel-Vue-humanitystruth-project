<?php
	session_start();

	include "db_connect.php";
	global $con;


	if(!isset($_POST['phone']) || !isset($_POST['code'])) die();
       
	$phone = $_POST['phone'];
	$code = $_POST['code'];
       
       
//check for abuse first: 4 attempts per 15 minutes
	$attempts = 4 - mysql_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/verify-phone.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 15 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($id = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $id['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/verify-phone.php', CURRENT_TIMESTAMP)");
      	$response = "Timeout";
    } else {
		if($verified = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `sms_codes` WHERE `phone` = '".$phone."' AND `verification_code` = '".$code."'"))){
        	if($account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone` = '" . $phone . "'"))) {
                if($account['type'] == "email_verified") mysqli_query($con, "UPDATE `accounts` SET `type` = 'both_verified' WHERE `uid` = " . $account['uid'] );
              	else mysqli_query($con, "UPDATE `accounts` SET `type` = 'phone_verified' WHERE `uid` = " . $account['uid'] );
              
              	$response = "Cellphone verified!";
            }
        }
	}

	echo $_GET['callback'] . '(' . "{'response' : ".json_encode($response)."}" . ')';
?>