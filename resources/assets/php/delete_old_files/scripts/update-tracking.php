<?php
	session_start();

	global $con;
	include("db_connect.php");
	include("email.php");

//authorize user
	$admin_acc_enc = "";

	if(!isset($_SESSION['accountEnc'])) header("Location: index.php");
	$result = mysqli_query($con, "SELECT * FROM `accounts` WHERE `accountEnc` = '".$_SESSION['accountEnc']."'");
	if($admin = mysqli_fetch_array($result)) {
		if($admin['type'] != "admin" && $admin['type'] != "employee") header("Location: index.php");
		$admin_acc_enc = $admin['accountEnc'];
	} else header("Location: index.php");

	mysqli_query($con, "UPDATE `purchase_orders` SET `tracking_number` = '".$_GET['tracking']."' WHERE `uniqueId` = ".$_GET['po']);

	$subject = "Tracking for Purchase Order #".$_GET['po']." | Solar Engineering, LLC";
	
	$result = mysqli_query($con, "SELECT * FROM `purchase_orders` WHERE `uniqueId` = " . $_GET['po']);
	$order = mysqli_fetch_array($result);

	if($order['accountId'] != 0) {
		$result = mysqli_query($con, "SELECT * FROM `accounts` WHERE `uniqueId` = " . $order['accountId']);
		$account = mysqli_fetch_array($result);
		$to = $account['email'];
	} else {
		$result = mysqli_query($con, "SELECT * FROM `transactions` WHERE `InvoiceNumber` = 'PO#".$_GET['po']."'");
		$tx = mysqli_fetch_array($result);
		$to = $tx['PayerEmail'];
	}

	$message = 'Dear '.$to.','."<br/><br/>". 
		'Your order is on the way!'."<br/>".
		'Here\'s your tracking number: '. $_GET["tracking"] ."<br/><br/>".
		'Should you have any issues, please do not hesitate to contact us.'."<br/><br/>".
		'Thank you,'."<br/>".
		'Solar Engineering, LLC'."<br/>".
		'(559) 862-7390'."<br/>".
		'admin@solarengineering.us';
		
	$email = new solarMail();
	$email->Theme($to, $subject, $message);

?>