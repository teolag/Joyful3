<?php
require "payson_init.php";


echo "<pre>" . print_r($_POST,1) . "</pre>";

$tax = 0.25;

$amountToReceive = 0;

$orderItems = array();

foreach($_POST['item'] as $item) {
	$productId = $item['productId'];
	$amount = $item['amount'];

	$product = $db->getRow("SELECT * FROM products WHERE product_id = ?", array($productId));
	$name = $product['name'];
	$priceWithoutTax = $product['price'] / (1+$tax);
	$priceTax = $product['price'] - $priceWithoutTax;

	$orderItems[] = new OrderItem($name, $priceWithoutTax, $amount, $tax, "p".$productId);
	$amountToReceive += $product['price'];
}

/*
$orderItems[] = new OrderItem("Cykel", 1000, 1, 0.25, "p654");
$amountToReceive = 1250;
*/


/*

um + extra = mm
100 + 25 = 125


um * (1+m) = mm

um + um*m = mm

um*m = extra





mm = um * (1+m)

um = mm/(1+m)


extra = mm - um

extra = mm - (mm/(1+m))


*/




$senderFirstname = "Test Nisse";
$senderLastname = "Jansson";
$senderEmail = "testnisse@teodor.se";


$description = "Beställning från Joyful";




$credentials = new PaysonCredentials($config['payson']['agentID'], $config['payson']['md5Key']);

$api = new PaysonApi($credentials, $config['payson']['testAPI']);



$receivers = array();
$receivers[] = new Receiver($config['payson']['receiverEmail'], $amountToReceive, "Lennart", "Gunnarsson", true);
//$receiver[] = new Receiver("payson@teodor.se", $amountToReceive, "Tödde", "L", false);

$sender = new Sender($senderEmail, $senderFirstname, $senderLastname);
$payData = new PayData($config['payson']['returnURL'], $config['payson']['cancelURL'], $config['payson']['ipnURL'], $description, $sender, $receivers);
$payData->setOrderItems($orderItems);
$payData->setFundingConstraints(array(FundingConstraint::BANK, FundingConstraint::CREDITCARD));
$payData->setFeesPayer(FeesPayer::PRIMARYRECEIVER);
$payData->setCurrencyCode(CurrencyCode::SEK);
$payData->setLocaleCode(LocaleCode::SWEDISH);
//$payData->setLocaleCode(LocaleCode::ENGLISH);
//$payData->setGuaranteeOffered(GuaranteeOffered::OPTIONAL);
$payData->setGuaranteeOffered(GuaranteeOffered::NO);
$payData->setShowReceiptPage(false);
$payData->setTrackingId("order456789");

$payResponse = $api->pay($payData);
if ($payResponse->getResponseEnvelope()->wasSuccessful()) {
    header("Location: " . $api->getForwardPayUrl($payResponse));
} else {
	echo "nehepp...";
	var_dump($payResponse);
}





?>