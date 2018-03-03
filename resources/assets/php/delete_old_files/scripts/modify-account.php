<?php
	session_start();

	include("db_connect.php");
	include("email.php");
	
	global $con;

	$result = mysqli_query($con, "SELECT * FROM `accounts` WHERE `accountEnc` = '".$_SESSION['accountEnc']."'");
	if($admin = mysqli_fetch_array($result)) {
		if($admin['type'] != "admin" && $admin['type'] != "employee") die();
	} else die();
	
	$account = mysqli_fetch_array( mysqli_query($con, "SELECT * FROM `accounts` WHERE `uniqueId` = ".$_POST['modify_account']) );
	
	if(!$account) die();
	
	
	//admin wants to revise someones email
	if($_POST['email'] != $account['email']) {
		$result = mysqli_query($con, "SELECT * FROM `accounts` WHERE `email` = '" . $_POST['email'] . "'");
		if($row = mysqli_fetch_array($result)) {
			echo "Email already exists!";

		} else {

			$result = mysqli_query($con, "UPDATE `accounts` SET `email` = '" . $_POST['email'] . "', `type` = 'inactive' WHERE `email` = '".$account['email']."'");

			if($result) {
		//Send account verification email
				$subject = "Account Verification | Solar Engineering, LLC";
		
				$to = $_POST['email'];
				$message = 'Dear '.$to.','."<br/><br/>". 
				'An administrator has updated your email account with Solar Engineering, LLC'."<br/>".
				'Please verify your account with this link:'."<br/><br/>".
				'Verification Link: <a href="https://solarengineering.us/actions/verify-account.php?id='.$account['accountEnc'].'">https://solarengineering.us/actions/verify-account.php?id='.$account['accountEnc']."</a><br/><br/>".
				'Thank you,'."<br/>".
				'Solar Engineering, LLC';
			
				$email = new solarMail();
				$email->Theme($to, $subject, $message);
			}
		}
	}
	
	$array = json_encode($count);
	echo $_GET['callback'] . '(' . "{'user' : ".$array."}" . ')';

?>