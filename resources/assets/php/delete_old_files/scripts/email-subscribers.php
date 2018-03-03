<?php
	session_start();

	include "functions.php";
	global $con;
	
//authorize user
    $id = 0;
    if(isset($_SESSION['hash'])) {
        if($account = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))) $id = $account['uid'];
      
    } else {
        mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'UNAUTHORIZED: /scripts/email-subscribers.php', CURRENT_TIMESTAMP)");
        header('Location: index.php');
        die();
    }
	if($admin = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '".$_SESSION['hash']."'"))) {
		if($admin['type'] != "super_admin") {
			header("Location: index.php");
			die();
		}
      	//access granted
	} else {
       	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'UNAUTHORIZED: /scripts/email-subscribers.php', CURRENT_TIMESTAMP)");
		header("Location: index.php");
		die();
	}


	$result = mysqli_query($con, "SELECT * FROM `subscribers`");	
	while($account = mysqli_fetch_array($result)) { 
		$account_id = $account['account']; 
    	$acc = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `uid` = " .$account_id));
    	$to = $acc['email'];
		$subject = ""; //$_POST[data]?
        $message = ""; //$_POST[data]?
        mail($to, $subject, $message);

        echo "Emailing: " . $to . "<br>";
   }

	echo "Done!<br>";
?>