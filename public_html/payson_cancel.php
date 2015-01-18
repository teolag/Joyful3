<?php
require "payson_init.php";
?>



<h3>Cancel</h3>



<?php
// Fetch the token that are returned
$token = $_GET["TOKEN"];

// Initialize the API in test mode
$credentials = new PaysonCredentials($config['payson']['agentID'], $config['payson']['md5Key']);
$api = new PaysonApi($credentials, $config['payson']['testAPI']);

// Get the details about this purchase
$detailsResponse = $api->paymentDetails(new PaymentDetailsData($token));

// First we verify that the call to payson succeeded.
if ($detailsResponse->getResponseEnvelope()->wasSuccessful()) {

    // Get the payment details from the response
    $details = $detailsResponse->getPaymentDetails();
	print_r($details);

	?>
	<div class="box">
		Beställningen avbruten
	</div>
	<?php
}
?>