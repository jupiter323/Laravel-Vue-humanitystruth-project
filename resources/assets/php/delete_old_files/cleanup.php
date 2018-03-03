<?php
	include "db_connect.php";

	/* TODO MAKE ACCESS CHECK FOR SYSTEM ONLY
	$user = get_current_user();
	if($user != "system") {
		//log current user for debug
		echo "error!";
		error_log($user . " was denied access to cron task");
		die(); 
	}
    */

	global $con;

echo "Cleaning expired account blacklistings..\n";
	$result = mysqli_query($con, "SELECT * FROM `blacklist`");
	while($entry = mysqli_fetch_array($result)) {
		if($entry['expiration'] != "0000-00-00 00:00:00" && 
           strtotime($entry['expiration']) < strtotime("now")) 
        	mysqli_query($con, "DELETE FROM `blacklist` WHERE `account_id` = " . $entry['account_id']);
	}

echo "Mutating encryptions..\n";
	$result = mysqli_query($con, "SELECT * FROM `accounts`");
	while($account = mysqli_fetch_array($result)) {
		$mutation = md5( bin2hex(mt_rand()) );
		$res = mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $mutation . "'"); //ensure unique
		while($row = mysqli_fetch_array($res)) {
			$mutation = md5( bin2hex(mt_rand()) );
			$res = mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $mutation . "'");
		}
		mysqli_query($con, "UPDATE `accounts` SET `hash` = '".$mutation."' WHERE `uid` = " . $account['uid']);
	}

echo "TODO!!!!! Mutating phpMyAdmin url.. (I also need a counter table for failed login attempts on db, ftp, etc\n";

//echo "Refreshing all transactions...\n";
	//refresh_all_transactions();

echo "Cleanup complete!\n\n\n\n";

?>