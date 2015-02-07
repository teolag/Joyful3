<?php
session_start();

$response = array();

$action = $_GET['action'];
if(empty($action)) {
	$response['status'] = 0;
	$response['message'] = "no action specified";
} else {


	require "/git/DatabasePDO/DatabasePDO.php";
	require "../config.php";
	require "../includes/Customer.php";
	require "../includes/Cart.php";
	require "../includes/Product.php";
	require "../includes/ProductHandler.php";
	require "../includes/actions.php";

	session_start();


	$db = new DatabasePDO($config['db']['server'],$config['db']['username'],$config['db']['password'],$config['db']['name']);
	$customer = new Customer($db);
	$response['action'] = $action;

	switch($action) {
		case "get-products":
		$response['products'] = getProducts($db);
		break;

		case "save-product":
		checkAccess($response);
		saveProduct($db, $response);
		break;

		case "get-categories":
		$response['categories'] = getCategories($db);
		break;

		case "get-product-images":
		getProductImages($db, $config['productFolder'], $response);
		break;

		case "upload-product-image":
		checkAccess($response);
		uploadProductImage($db, $config['productFolder'], $response);
		break;

		case "add-to-cart":
		addToCart($db, $customer, $response);
		break;

		case "remove-from-cart":
		removeFromCart($db, $customer, $response);
		break;

		case "clear-cart":
		clearCart($db, $customer, $response);
		break;


		default:
		$response['status'] = 0;
		$response['message'] = "'" . $action . "' is not a part of the API";
	}


	if(isset($file)) {
		include "../actions/".$file;
	}
}

header('Content-Type: application/json');
echo json_encode($response, JSON_NUMERIC_CHECK);



function checkAccess(&$response) {
	if(!isset($_SESSION['admin'])) {
		$response['status'] = 0;
		$response['message'] = "Access denied!";
	}
}


?>