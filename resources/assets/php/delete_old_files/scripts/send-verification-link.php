<?php
	session_start();

	include "functions.php";
	global $con;


//check for abuse first: 5 attempts per 2 minutes
	$attempts = 5 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/resend-verification-link.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($res = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $res['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/resend-verification-link.php', CURRENT_TIMESTAMP)");
		echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Timeout")."}" . ')';
      	die();
    }


	if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `email` = '".$_POST['email']."'"))) {
		if($row['type'] == "both-verified" || $row['type'] == "email-verified") {
			echo $_GET['callback'] . '(' . "{'response' : ".json_encode("No need to re-verify, thanks!")."}" . ')';
			die();
		}

            //Send account verification email
            $subject = "Email verification for humanitystruth.com";

            $to = $row['email'];
            $message = 'Dear '.$to.','."<br><br>". 
            'Thank you for signing up with humanitystruth.com!'."<br>".
            'Please verify your account with this link:'."<br><br>".
            'Verification Link: <a href="https://humanitystruth.com/scripts/verify-account.php?id='.$session.'">https://humanitystruth.com/scripts/verify-account.php?id='.$session."</a><br>".
            '(these links expire soon, please verify your account immediately)<br><br>';

            themedEmail($to, $subject, $message);
      
      		echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Email sent..")."}" . ')';

	} else echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Bad email")."}" . ')';

?>