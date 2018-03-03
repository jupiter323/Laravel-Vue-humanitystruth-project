<?php

EXIT;

$dn = array("countryName" => 'US', "stateOrProvinceName" => 'California', "localityName" => 'Fresno', "organizationName" => 'humanitystruth.com', "organizationalUnitName" => 'humanitystruth.com', "commonName" => 'humanitystruth.com', "emailAddress" => 'admin@humanitystruth.com');
$numberofdays = 10000;

$privkey = openssl_pkey_new();
$csr = openssl_csr_new($dn, $privkey);
$sscert = openssl_csr_sign($csr, null, $privkey, $numberofdays);
openssl_x509_export($sscert, $publickey);
openssl_pkey_export($privkey, $privatekey);
openssl_csr_export($csr, $csrStr);

echo $privatekey . "<br>"; // Will hold the exported PriKey

echo $publickey . "<br>";  // Will hold the exported PubKey

echo $csrStr;     // Will hold the exported Certificate
?>