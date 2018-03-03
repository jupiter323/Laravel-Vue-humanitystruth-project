<?php
	session_start();
	include "functions.php";
	
	global $con;
	
	$basket = array();
	$jobs_total = 0;
	$addendums_total = 0;
	$subtotal = 0;
	$shipping = 0;
	$_SESSION['total'] = 0;

	
//add job invoice to session
	if(isset($_GET['add_job'])) {
		$result = mysqli_query($con, "SELECT * FROM `jobs` WHERE `jobUniqueId` = ".$_GET['add_job']." AND `jobStage` = 1");
		if($job = mysqli_fetch_array($result)) {
			if(isset($_SESSION['jobs'])) {  //if session var already exist
				if(isset($_SESSION['jobs'][$job['jobUniqueId']])) {
					unset($_SESSION['jobs'][$job['jobUniqueId']]); //unset old item
				}			
			} else $_SESSION['jobs'] = array();
				
			$_SESSION['jobs'][$job['jobUniqueId']] = $job;
		}		

//remove job invoice from shopping cart and database
	} else if(isset($_GET['remove_job'])) {
		if(isset($_SESSION['jobs'])) {  //if session var already exist
			if(isset($_SESSION['jobs'][$_GET["remove_job"]])) {
				unset($_SESSION['jobs'][$_GET["remove_job"]]); //unset old item
			}			
		}
		
		mysqli_query($con, "DELETE FROM `jobs` WHERE `jobUniqueId` = ".$_GET["remove_job"]." AND `jobStage` = 1");
	
//add product to shopping cart
	} else if(isset($_GET['product_code'])) {
		foreach($_GET as $key => $value){
			$new_product[$key] = $value;
		}
	
		$sql = "SELECT * FROM `products` WHERE `product_code` = ".$new_product['product_code'];
		$result = mysqli_query($con, $sql);

		while($row = mysqli_fetch_array($result)){
			if($row['minQuantity'] > $row['availableQuantity']) continue;

			$val = $row['minQuantity'] > $new_product['product_qty'] ? $row['minQuantity'] : $new_product['product_qty'];

			$new_product['product_name'] = $row['product_name'];
			$new_product['product_price'] = floatval($row['product_price']);

			$result2 = mysqli_query($con, "SELECT * FROM `datasheets` WHERE `uniqueId` = ".$row['datasheetId']);
			$row2 = mysqli_fetch_array($result2);

			$new_product['product_model'] = $row2['modelNumber'];
			$new_product['product_size'] = $row2['size'];
			$new_product['product_qty'] = $val > $row['availableQuantity'] ? $row['availableQuantity'] : $val;
			$new_product['product_subtotal'] = $new_product['product_price'] * $new_product['product_qty'];


		
			if(isset($_SESSION['products'])) {  //if session var already exist
				if(isset($_SESSION['products'][$new_product['product_code']])) {
					unset($_SESSION["products"][$new_product['product_code']]); //unset old item
				}			
			} else $_SESSION['products'] = array();
		
			$_SESSION['products'][$new_product['product_code']] = $new_product;
		}
	}

//remove item from shopping cart
	else if(isset($_GET['remove_product']) && isset($_SESSION['products'])) {
		if(isset($_SESSION["products"][$_GET["remove_product"]])) {
			unset($_SESSION["products"][$_GET["remove_product"]]);
		}
	}



//forcibly add any invoiced jobs and addendums to cart
	if($_SESSION['account'] > 0) {
		$result = mysqli_query($con, "SELECT * FROM `jobs` WHERE `jobOwnersAccountUid` = ".$_SESSION['account']." AND `jobStage` = 1");
		while($job = mysqli_fetch_array($result)) {
			if(isset($_SESSION['jobs'])) {  //if session var already exist
				if(isset($_SESSION['jobs'][$job['jobUniqueId']])) {
					unset($_SESSION['jobs'][$job['jobUniqueId']]); //unset old item
				}			
			} else $_SESSION['jobs'] = array();
					
			$_SESSION['jobs'][$job['jobUniqueId']] = $job;
				
		}
		
//search every job owned by account for an addendum
		$result = mysqli_query($con, "SELECT * FROM `jobs` WHERE `jobOwnersAccountUid` = ".$_SESSION['account']);
		while($row = mysqli_fetch_array($result)) {
			$result2 = mysqli_query($con, "SELECT * FROM `addendums` WHERE `jobUniqueId` = " . $row['jobUniqueId']);
			
			while($addendum = mysqli_fetch_array($result2)) {
				if($addendum['cost'] > 0) {
					$r = mysqli_query($con, "SELECT * FROM `transactions` WHERE `InvoiceNumber` LIKE '%ADDENDUM#" . $addendum['jobUniqueId'] . "%'");
					if(!mysqli_fetch_array($r)) {
						if(isset($_SESSION['addendums'])) {  //if session var already exist
							if(isset($_SESSION['addendums'][$addendum['uniqueId']])) {
								unset($_SESSION['addendums'][$addendum['uniqueId']]); //unset old item
							}			
						} else $_SESSION['addendums'] = array();
						
						$_SESSION['addendums'][$addendum['uniqueId']] = $addendum;
					}
				}
			}
		}
	}


//COMPUTING COSTS

//add cost of addendums in cart
	if(isset($_SESSION["addendums"]) && count($_SESSION["addendums"])>0) {
		foreach($_SESSION['addendums'] as $addendum) {
			$addendums_total += $addendum['cost'];
		}
	}	

//add cost of each job in cart	
	if(isset($_SESSION["jobs"]) && count($_SESSION["jobs"])>0){
		foreach($_SESSION["jobs"] as $job) {
			$jobs_total += get_job_cost($job['jobUniqueId']);
		}
	}
	
//add cost of each product in cart
	if(isset($_SESSION["products"]) && count($_SESSION["products"])>0){
		$pallets = 0;

		foreach($_SESSION["products"] as $product){
			$subtotal += ($product['product_price'] * $product['product_qty']);
			
			array_push($basket, array($product["product_code"]=>$product['product_qty']));

			$result = mysqli_query($con, "SELECT * FROM `products` WHERE `product_code` = ".$product["product_code"]);
			$res = mysqli_fetch_array($result);
			$pallets += ceil($product['product_qty'] / $res['minQuantity']);
			
		}

		$shipping = 100;
		$shipping += 40 * $pallets;
		if(isset($_GET['requiresLift'])) $shipping += 60 * intval($_GET['requiresLift']);
		if(isset($_GET['locationType'])) $shipping += 60 * intval($_GET['locationType']);

		if(isset($_GET['city']) && isset($_GET['state'])) {
			$city = str_replace(" ", "+", $_GET['city']);
			$state = str_replace(" ", "+", $_GET['state']);

			$results = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?origins=La+Puenta+CA&units=imperial&destinations=".$city."+".$state."&key=AIzaSyCSmxlPlWratQqZO42f9Cw6-0EVpf-J0fE");

			$decoded_array = (array)json_decode($results, true);
			$rows = $decoded_array["rows"][0];
			$elements = $rows["elements"][0];
			$distance = $elements["distance"]["text"];
			$distance = str_replace(",", "", $distance);

			$result = mysqli_query($con, "SELECT * FROM `references` WHERE `getPriceKey` = 'Shipping Mileage'");
			$rate = mysqli_fetch_array($result);
		
			$shipping += $rate['getPriceValue'] * intval($distance);
		}
	}
	
//INCLUDE TAX
	$grand_total += (($subtotal + $shipping) * 1.08225) + $jobs_total + $addendums_total;
	
	$_SESSION['total'] = $subtotal + $jobs_total + $addendums_total;
	
//REFLECT DATA TO CART SUMMARY
	$cart_summary = "$".number_format($_SESSION['total'], 2) . " ";
	$num = (count($_SESSION['products']) + count($_SESSION['jobs']) + count($_SESSION['addendums']));
	
	if($num > 0) {
		$cart_summary .= "(" . $num . " PRODUCT" . ($num == 1 ? "" : "S") . ")";
	} else {
		$cart_summary .= "(0 PRODUCTS)";
	}




//STORE CART FOR LOGGED IN USERS
	if(isset($_SESSION['account']) && $_SESSION['account'] > 0) {
		mysqli_query($con, "DELETE FROM `shopping_carts` WHERE `accountId` = ".$_SESSION['account']);
		
		if(count($_SESSION['products']) > 0) {
			mysqli_query($con, "INSERT INTO `shopping_carts` VALUES (NULL, ".$_SESSION['account'].", '".json_encode($basket)."')");
			
		}
	}

	echo $_GET['callback'] . '(' . "{'cart' : ".json_encode( array('summary'=>$cart_summary, 'shipping'=>$shipping, 'info'=>(count($_SESSION['products'])>0 ? true : false), 'grand_total'=>$grand_total, 'tax'=>(($subtotal + $shipping) * 0.08225)))."}" . ')';

?>