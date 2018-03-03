<?php
	session_start();

	include "functions.php";

	global $con;

//check for abuse first: 5 attempts per 2 minutes
	$attempts = 5 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/new-account.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            if($id = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $id['uid'];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/new-account.php', CURRENT_TIMESTAMP)");
      	echo $_GET['callback'] . '(' . "{'response' : ".json_encode("timeout")."}" . ')';
      	die();
    }

	$email = isset($_POST['email']) ? $_POST['email'] : "";
	$phone = isset($_POST['phone']) ? $_POST['phone'] : "";
	$alias = isset($_POST['alias']) ? $_POST['alias'] : "";
	$password = isset($_POST['password']) ? $_POST['password'] : "";
	
	if($email != "") {
        if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `email` = '" . $email . "'"))) {
      		echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Email already exists")."}" . ')';
            die();
        }
		if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
      		echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Invalid email")."}" . ')';
            die();
		}
    }
	if($phone != "") {
      	if(strlen($phone) != 10 || !preg_match('/^[0-9]{10}$/', $phone)) {
            echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Invalid 10-digit cellphone number")."}" . ')';
            die();         
        }
        if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone` = '" . $phone . "'"))) {
            echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Cellphone already exists")."}" . ')';
            die();
        }
    }

    if($alias != "") {
      	if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `alias` = '" . $alias . "'"))) {
            echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Alias already exists")."}" . ')';
        	die();
        }
      	$length = strlen($alias);
      	if($length > 24 || $length < 5) {
            echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Alias length between 5 and 24 characters")."}" . ')';
            die();         
        }
    }

	//dissecting regex for error codes
	$uppercase = preg_match('@[A-Z]@', $password);
	$lowercase = preg_match('@[a-z]@', $password);
	$number = preg_match('@[0-9]@', $password);
	$special = preg_match('@[^\w]@', $password);

	if( ($email == "") && ($phone == "") ) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("An email or cellphone is required")."}" . ')';
        die();
    } else if($uppercase === false) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Passwords require uppercase letters")."}" . ')';
    	die();
	} else if($lowercase === false) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Passwords require lowercase letters")."}" . ')';
    	die();
    } else if($number === false) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Passwords require numbers")."}" . ')';
    	die();
	} else if($special === false) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Passwords require special characters")."}" . ')';
    	die();
	} else if(strlen($password) < 12) {
        echo $_GET['callback'] . '(' . "{'response' : ".json_encode("Password length atleast 12 characters")."}" . ')';
    	die();
    }

	$session = md5( bin2hex(mt_rand()) ); //unique hash
	while($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $session . "'"))) { //ensure unique hash
		$session = md5( bin2hex(mt_rand()) ); //USE random_bytes
	}

	//INSERT INTO `accounts`(`uid`, `email`, `phone`, `alias`, `password`, `hash`, `created`, `avatar`, `intel`, `default_text_notification`, `default_email_notification`, `type`)
	if (	mysqli_query($con, "INSERT INTO `accounts` VALUES (NULL, ".($email?"'".$email."'":"NULL").", ".($phone?"'".$phone."'":"NULL").", ".($alias?"'".$alias."'":"NULL").", '".md5($password)."',  '".$session."', NOW(), NULL, NULL, ".($phone?1:0).", ".($email?1:0).", 'inactive')")) {
     	$response = "";
		if($phone) {
       		$response = "vc";
        }
      	if($email) {
            //Send account verification email
            $subject = "Email verification for humanitystruth.com";



            $to = $email;
            $message = 'Dear '.$email.','."<br><br>". 
            'Thank you for signing up with humanitystruth.com!'."<br>".
            'Please verify your account with this link:'."<br><br>".
            'Verification Link: <a href="https://humanitystruth.com/scripts/verify-email.php?id='.$session.'">https://humanitystruth.com/scripts/verify-email.php?id='.$session."</a><br>".
            '(these links expire soon, please verify your account immediately)<br><br>';

            themedEmail($to, $subject, $message);

       		$response += "ve";
        }
      	echo $_GET['callback'] . '(' . "{'response' : ".json_encode($response)."}" . ')';
    } else {
       	echo $_GET['callback'] . '(' . "{'response' : ".json_encode("SQL Error")."}" . ')';
    }
?>