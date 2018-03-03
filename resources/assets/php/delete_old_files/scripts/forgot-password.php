<?php
	session_start();

	include "functions.php";

	global $con;

	$email_phone = isset($_POST['email_phone']) ? $_POST['email_phone'] : "";

//check for abuse first: 5 attempts per 2 minutes
	$attempts = 5 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/forgot-password.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($res = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $res['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/forgot-password.php', CURRENT_TIMESTAMP)");
		echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Timeout")."}" . ')';
      	die();
    }


	$ret = "Unknown email or cellphone";

	if($account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `email` = '" . $email_phone . "'"))) {

		//send email with confirm-reset.php link
        $subject = "Password Reset | HumanitysTruth.com";
        $to = $account['email'];
        $message = 'Dear '.$to.','."<br><br>". 
        "We've received a password reset request for your humanitystruth.com account." ."<br>".
        'Please reset your account password with this link. If you did not request this password reset, you may disregard this email.'."<br><br>".
        'Password Reset Link: <a href="https://humanitystruth.com/scripts/confirm-pw-reset.php?id='.$account['hash'].'">https://humanitystruth.com/scripts/confirm-pw-reset.php?id='.$account['hash']."</a><br><br>";

        themedEmail($to, $subject, $message);
		$ret = "A password reset link has been emailed to you..";
      
	} else if(preg_match('/^[0-9]{10}$/', $email_phone)) { 
        if($account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone` = '" . $email_phone . "'"))) {

            $code = md5( bin2hex(mt_rand()) );
            $code = substr($hash, 0, 10);
          
			if(mysqli_query($con, "INSERT INTO `sms_codes` VALUES (NULL,'".$email_phone."','".$code."')")) {
            	$sms->send($phone, $code);
            	$ret = "A password reset sms has been texted to you..";
            } else $ret = "DB Error";
        }
	}
	echo $_GET['callback'] . '(' . "{'response' : ".json_encode($ret)."}" . ')';
?>