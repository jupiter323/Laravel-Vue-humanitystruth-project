<?php
	session_start();
	include "functions.php";

	global $con;

//check for abuse first: 5 attempts per 2 minutes
	$attempts = 5 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/confirm-pw-reset.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($res = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $res['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/confirm-pw-reset.php', CURRENT_TIMESTAMP)");
      	die();
    }




	$hash = $_GET['token'];

	$account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $hash . "'"));
	if($account){
		if($account['phone'] != null) {
			//reset via phone number
          	//TODO TEXT PASSCODE
          
        } else {
			//reset via email
      		$password = md5( bin2hex( mt_rand()) );
			$password = substr($password, 0, 12);
			if($result = mysqli_query($con, "UPDATE `accounts` SET `password` = '" . md5($password) . "' WHERE `hash` = '" . $account['hash'] . "'")) {
				$subject = "Password Reset | HumanitysTruth.com";
				$to = $account['email'];

				$message = 'Dear '.$to.','."<br><br>". 
				"Here's your new temporary password for humanitystruth.com"."<br>".
				'Please login and change your password immediately!'."<br><br>".
				'Temporary Password: '.$password."<br>".
				'<a href ="https://humanitystruth.com/settings.php">https://humanitystruth.com/settings.php</a>';

				themedEmail($to, $subject, $message);
        	}
        }
		header( 'Location: https://humanitystruth.com/index.php?id=7');
	}
?>