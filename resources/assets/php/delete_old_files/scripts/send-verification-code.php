<?php
	session_start();

	include "twilio.php";
	include "db_connect.php";

	global $con;
	$sms = new SMS();

	if(!isset($_POST['phone']) die();
       
	$phone = $_POST['phone'];
    $response = "Error sending verification code";

//check for abuse first: 4 attempts per 15 minutes
	$attempts = 4 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/send-verification-code.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 15 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($id = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $id['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/send-verification-code.php', CURRENT_TIMESTAMP)");
      	$response = "Timeout";
    } else {

        $code = md5( bin2hex(mt_rand()) );
		$code = substr($hash, 0, 10);
      
        if($account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone` = '" . $phone . "'"))){
            if(mysqli_query($con, "INSERT INTO `sms_codes`(`uid`, `phone`, `verification_code`) VALUES (NULL,'".$phone."','".$code."')")) {
              $sms->send($phone, $code);
              $response = "SMS sent!";
            } else $response = "DB Error";

        } else {
            $response = "Error associating verification code";
        }
    }


	echo $_GET['callback'] . '(' . "{'response' : ".json_encode($response)."}" . ')';
?>