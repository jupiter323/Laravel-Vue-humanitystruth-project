<?php
	session_start();
	
	include "db_connect.php";

	global $con;

//check for abuse first: 4 attempts per 2 minutes
	$attempts = 4 - mysqli_num_rows(mysqli_query($con,"SELECT * FROM `traffic` WHERE `ip` = '" . $_SERVER['REMOTE_ADDR'] . "' AND `action` = '/scripts/login.php' AND `timestamp` > DATE_SUB(now(), INTERVAL 2 MINUTE)"));

	if($attempts <= 0) {
      	$attempts = 0;
      	$id = 0;
        if(isset($_SESSION['hash'])) {
            $id = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '" . $_SESSION['hash'] . "'"))["uid"];
        }
      	mysqli_query($con, "INSERT INTO `traffic` VALUES (NULL, '".$_SERVER['REMOTE_ADDR']."', ".$id.", 'ABUSING: /scripts/login.php', CURRENT_TIMESTAMP)");
      
    } else if(isset($_POST['email_phone']) && isset($_POST['password'])) {

        $email_phone = $_POST['email_phone'];
        $pwd = md5($_POST['password']);

      //check email or phone
        if(!$row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `email` = '".$email_phone."' AND `password` = '".$pwd."'"))) {
          if($row = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `phone` = '".$email_phone."' AND `password` = '".$pwd."'"))) $attempts = -2; //require two-step authentication
          
        } else {
          	if($row['type'] == 'inactive') {
                $attempts = -1;
            } else {
                $attempts = $row['hash'];

                $_SESSION['email'] = $row['email'];
                $_SESSION['alias'] = $row['alias'];
                $_SESSION['phone'] = $row['phone'];
                $_SESSION['type'] = $row['type'];
                $_SESSION['hash'] = $row['hash'];
                $_SESSION['total'] = 0;


        //LOAD CART
                $result = mysqli_query($con, "SELECT * FROM `shopping_carts` WHERE `account` = ".$_SESSION['account']);
                while($cart = mysqli_fetch_array($result)) {
                    $json = (array)json_decode($cart['basket']);

                    for($i = 0; $i < count($json); $i++) {
                        foreach($json[$i] as $key => $value) {
                            // this is a product in shopping cart	
                            $new_product = array();
                            $res = mysqli_query($con, "SELECT * FROM `products` WHERE `uid` = ".$key);

                            while($row = mysqli_fetch_array($res)){ 
                                $new_product['code'] = $row['uid'];
                                $new_product['name'] = $row['name'];
                                $new_product['price'] = floatval($row['price']);
                                $new_product['quantity'] = intval($value);

                                if(isset($_SESSION['products'])) {  //if session var already exist
                                    if(isset($_SESSION['products'][$new_product['code']])) {
                                        unset($_SESSION["products"][$new_product['code']]); //unset old item
                                    }			
                                } else $_SESSION['products'] = array();

                                $_SESSION['products'][$new_product['code']] = $new_product;
                                $_SESSION['total'] += $new_product['price'] * $new_product['quantity'];
                            }
                        }
                    }
                }
            }
        }
    }

	echo $_GET['callback'] . '(' . "{'isUser' : ".json_encode($attempts)."}" . ')';

?>

