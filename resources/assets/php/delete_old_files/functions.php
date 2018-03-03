<?php
	session_start();

	include "db_connect.php";

	$vision = "An open-source intelligence community promoting a decentralized economy of abundance for all humanity on earth by exposing suppressed knowledge.";

/*
** Abbreviations
*/
	function debug($string) {
    	file_put_contents("debug.log" , $string, FILE_APPEND);
    }


/*
** File control functions
*/


    function delete($path) {
        if (is_dir($path) === true) {
            $files = array_diff(scandir($path), array('.', '..'));

            foreach ($files as $file) delete(realpath($path) . '/' . $file);

            return rmdir($path);

        } else if (is_file($path) === true) {
            return unlink($path);
        }
    }



/*
** Emailer functions
*/

	$signature = "Thanks,<br>Humanitys Truth<br>https://humanitystruth.com";

	function fileEncode($file_name, $content_id) {
		$message_file = "";
		$mime_boundary = "==Multipart_Boundary_x".md5('humanitystruth.com')."x";
		$file = fopen($_SERVER["DOCUMENT_ROOT"].$file_name,"rb");
		$data = fread($file,filesize($_SERVER["DOCUMENT_ROOT"].$file_name));
		fclose($file);
		$data = chunk_split(base64_encode($data));
		$message_file = "Content-Type: \"image/png\" name=\"".basename($_SERVER["DOCUMENT_ROOT"].$file_name)."\"\n" . 
		"Content-Disposition: inline; filename=\"".basename($_SERVER["DOCUMENT_ROOT"].$file_name)."\"\n" .
		"Content-ID: <".$content_id.">"."\n" .
		"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
		$message_file .= "--".$mime_boundary."\n";
		return $message_file;
	}
		
	function blankEmail($to, $subject, $message, $from = 'noreply@humanitystruth.com', $header = null) { 
		return mail($to, $subject, $message, $header, '-f'.$from);
	} 

	function themedEmail($to, $subject, $message, $message_file = "", $from = 'noreply@humanitystruth.com', $header = null) { 
        global $signature;
		$mime_boundary = "==Multipart_Boundary_x".md5('humanitystruth.com')."x";
		$logo = fileEncode('/data/imgs/humanitystruth_business_card+bleed.png', 'email_logo');
			
		$headers = "From: " . $from . "\r\n";
		$headers .= "Reply-To: ". $from . "\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: multipart/mixed;\n" . " boundary=\"".$mime_boundary."\"";
		$header = $headers.$header;

		$message = "This is a multi-part message in MIME format.\n\n" . '--'.$mime_boundary."\n" . "Content-Type: text/html; charset=ISO-8859-1\n" . "Content-Transfer-Encoding: 7bit\n\n".'
		<html>
            <meta name="viewport" content="width=device-width">
            <head>
                <link rel="stylesheet" href="https://humanitystruth.com/w3.css">
                <link rel="stylesheet" href="https://humanitystruth.com/style.css">
            </head>
            	
            <body>
				<a href="https://humanitystruth.com"><img src="cid:email_logo" style="width:100%;"></a><br>
                <!--<a href="https://humanitystruth.com"><img src="https://humanitystruth.com/data/imgs/humanitystruth_business_card+bleed.png" style="width:100%;"></a>-->
                    
                <p>'.$message.'</p>
            	<p>'.$signature.'</p>
                    
			</body>
        </html>'."\n\n--".$mime_boundary.
		"\n".  $logo.
		$message_file."\n\n--".$mime_boundary;
		return mail($to, $subject, $message, $header, '-f'.$from);
	}

/*
** paypal functions
*/
    //TODO GET PAYPAL CERT FOR humanitystruth
    function paypal_encrypt($hash) {
        $MY_KEY_FILE      = '/home/humanitystruth/www/certs/humanitystruth_pp_key.pem';
        $MY_CERT_FILE     = '/home/humanitystruth/www/certs/humanitystruth_pp_csr.pem';
        $PAYPAL_CERT_FILE = '/home/humanitystruth/www/certs/paypal_cert_pem.txt';
        $OPENSSL					= '/usr/bin/openssl';



        if (!file_exists($MY_KEY_FILE)) {
            die( "ERROR: MY_KEY_FILE $MY_KEY_FILE not found\n");
        }
        if (!file_exists($MY_CERT_FILE)) {
                die(  "ERROR: MY_CERT_FILE $MY_CERT_FILE not found\n");
        }
        if (!file_exists($PAYPAL_CERT_FILE)) {
                die(  "ERROR: PAYPAL_CERT_FILE $PAYPAL_CERT_FILE not found\n");
        }


        $data = "";
        foreach ($hash as $key => $value) {
            if ($value != "") {
                //echo "Adding to blob: $key=$value\n";
                $data .= "$key=$value\n";
            }
        }

        $openssl_cmd = "($OPENSSL smime -sign -signer $MY_CERT_FILE -inkey $MY_KEY_FILE " .
                            "-outform der -nodetach -binary <<_EOF_\n$data\n_EOF_\n) | " .
                            "$OPENSSL smime -encrypt -des3 -binary -outform pem $PAYPAL_CERT_FILE";

        exec($openssl_cmd, $output, $error);
        //print_r($output);exit;

        if (!$error) {
            return implode("\n",$output);
        } else {
            return "ERROR: encryption failed";
        }
    }


    function generate_paypal_button($hash) {
        $result = paypal_encrypt($hash);
        if ($result != "ERROR: encryption failed") {
            return '<input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="encrypted" value="' . $result . '">
                <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">';
        } else {
            return $result;
        }
    }

    //TODO REPAIR CODE
    function generate_button($price, $description, $reference_id ) {

        $hash = array(
                'cmd'			=> '_xclick',
                'business'		=> 'admin@humanitystruth.com',
                'lc'			=> 'US',
                'amount'		=> $price,
                'item_name'		=> $description,
                'item_number'	=> $reference_id,
                'button_subtype'=> 'services',
                'no_note'		=> 1,
                'no_shipping'	=> 2,
                'notify_url'	=> 'https://humanitystruth.com/scripts/paypal_ipn.php',
                'return'		=> 'https://humanitystruth.com/receipt.php',
                'cancel'		=> 'https://humanitystruth.com/shopping-cart.php',
                'currency_code'	=> 'USD',
                'bn'			=> 'PP-BuyNowBF:btn_paynowCC_LG.gif:NonHosted',
                'cert_id'		=> 'R6QGVRKXVRJ58'
            );

        return generate_paypal_button($hash);
    }

    function blacklist($ip_address, $uid, $expiration) {
        global $con;
        mysqli_query($con, "INSERT INTO `blacklist` VALUES (NULL, '".$ip_address."', ".$uid.", '".$expiration."')");
    }

	/**
	* Make API request
	*
	* @param string $method string API method to request
	* @param array $params Additional request parameters
	* @return array / boolean Response array / boolean false on failure
	*/
	function request($method, $params) {

		/**
		* API Credentials
		* Use the correct credentials for the environment in use (Live / Sandbox)
		* @var array
		*/
		$_credentials = array(
			'USER' => 'alex.crayne_api1.gmail.com',
			'PWD' => 'XWP8Y3L7VUG9QH2Z',
			'SIGNATURE' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AC7JzwFniUl6MBsNwXQl7MmIwmbq' ); //TODO CONVERT TO CERTIFICATE!!!!

		/**
		* API endpoint
		* Live - https://api-3t.paypal.com/nvp
		* Sandbox - https://api-3t.sandbox.paypal.com/nvp
		* @var string
		*/
		$_endPoint = 'https://api-3t.paypal.com/nvp';

		/**
		* API Version
		* @var string
		*/
		$_version = '74.0';

		if( empty($method) ) { //Check if API method is not empty
 			debug('API method is missing');
			return false;
		}

		//Our request parameters
		$requestParams = array(
		'METHOD' => $method,
		'VERSION' => $_version
		) + $_credentials;

		//Building our NVP string
		$request = http_build_query($requestParams + $params);

		//cURL settings
		$curlOptions = array (
			CURLOPT_URL => $_endPoint,
			CURLOPT_VERBOSE => 1,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			//CURLOPT_CAINFO => '/home/humanitystruth/www/certs/pp_api_cert_pem.txt', //paypals api cert file
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $request );

		$ch = curl_init();
		curl_setopt_array($ch, $curlOptions);

		//Sending our request - $response will hold the API response
		$response = curl_exec($ch);

		//Checking for cURL errors
		if (curl_errno($ch)) {
			debug(curl_error($ch));
			curl_close($ch);
			return false;
			
		} else {
			curl_close($ch);
			$responseArray = array();
			parse_str($response, $responseArray); // Break the NVP string to an array
			return $responseArray;
			
		}
	}
	
	//iterate thru all transactions
	function refresh_all_transactions() {
		global $con;
		$sql = "SELECT * FROM `transactions`";
		$result = mysqli_query($con, $sql);
		while($row = mysqli_fetch_array($result)) {
			refresh_transaction_status($row['tx_id']);
		}
	}


	function refresh_transaction_status($tx_id) {
		global $con;
		$params = array(
			'STARTDATE' => "2018-01-01T00:00:01Z",
			'TRANSACTIONID' => $tx_id );
			
		$response = request("TransactionSearch", $params);

		if(!$response) { 
			echo "No response from paypal!";
		} else {
			$payment_status = $response['L_TYPE0'] . " " . $response['L_STATUS0'];
			$payment_amount = $response['L_AMT0'];
			$timestamp = $response['L_TIMESTAMP0'] . " (" . gmdate("Y-m-d H:i:s") . ")";

			$sql = "UPDATE `transactions` SET `status` = '".$payment_status."', `amount` = '".$payment_amount."', `timestamp` = '".$timestamp."' WHERE `tx_id` = '" . $response['L_TRANSACTIONID0'] . "'";
			
			return mysqli_query($con, $sql);
			
		}
	}
	



    function get_account_by_uid($uid) {
        global $con;

        return mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `uid` = ".$uid));
    }

    function get_account_by_hash($hash) {
        global $con;

        return mysqli_fetch_array(mysqli_query($con, "SELECT * FROM `accounts` WHERE `hash` = '".$hash ."'"));
    }













?>