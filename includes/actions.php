<?php

function getCategories(&$db) {
	$categories = $db->getArray("SELECT category_id, name, hidden, parent, sort FROM categories ORDER BY parent, sort", null, true);
	$categories[0] = array("name"=>"root");

	foreach($categories as $id=>$c) {
		$categories[$id]['id'] = $id;
		if(isset($c['parent'])) {
			$categories[$c['parent']]['children'][] = $id;
		}
	}
	return $categories;
}


function getProduct(&$db, $productId){
	$product = ProductHandler::getProduct($db, $productId);
	return $product;
}

function getProducts(&$db){
	$products = $db->getArray("SELECT product_id, name, price FROM products", null, true);
	return $products;
}


function getCategoryProducts(&$db) {
	$sql = "SELECT category_id, product_id FROM _procat";
	$procats = $db->getArray($sql);

	$categoryProcucts = array();
	foreach($procats as $p) {
		$categoryProcucts[$p['category_id']][] = $p['product_id'];
	}
	return $categoryProcucts;
}


function saveProduct(&$db, &$response) {
	if(empty($_POST['product_id'])) {
		$data = array($_POST['name'], $_POST['price']);
		$productId = $db->insert("INSERT INTO products(name,price) VALUES(?,?)", $data);

		$response['status'] = 1000;
		$response['message'] = "new product added";
		$response['added'] = true;

	} else {
		$productId = $_POST['product_id'];
		$data = array($_POST['name'], $_POST['price'], $productId);
		$db->update("UPDATE products SET name=?, price=? WHERE product_id=?", $data);

		$response['status'] = 1000;
		$response['message'] = "product has been saved";
	}

	$product = getProduct($db, $productId);
	$response['product'] = $product;
}

function getProductImages(&$db, $productFolder, &$response) {
	$images = $db->getArray("SELECT * FROM product_images WHERE product_id=?", array($_GET['product_id']));
	$response['images'] = $images;
	$response['status'] = 1000;
	$response['message'] = "recived product images";
}

function uploadProductImage(&$db, $productFolder, &$response) {
	$productId = $_POST['productId'];
	$file = $_FILES['file'];

	$productImageId = $db->insert("INSERT INTO product_images(product_id, name) VALUES(?,?)", array($productId, $file['name']));

	if(!is_dir($productFolder.$productId)) {
		mkdir($productFolder.$productId);
	}
	$url = "/products/".$productId."/".$productImageId."_".$file['name'];
	$uri = $productFolder.$productId."/".$productImageId."_".$file['name'];
	move_uploaded_file($file["tmp_name"], $uri);

	$input = $uri;
	$output = $uri;
	$width = intval($_REQUEST['width']);
	$height = intval($_REQUEST['height']);
	$top = intval($_REQUEST['top']);
	$left = intval($_REQUEST['left']);
	$maxWidth = 500;
	$maxHeight = 500;
	$command = "convert '{$input}' -crop {$width}x{$height}+{$left}+{$top} -resize {$maxWidth}x{$maxHeight}\> '{$output}'";
	exec($command);

	$response['status'] = 100;
	$response['message'] = "image was uploaded cropped and resized";
	$response['url'] = $url;

}


function addToCart(&$db, &$customer, &$response) {
	$productId = $_POST['product_id'];
	if(empty($productId)) {
		$response['status'] = 100;
		$response['message'] = "productId was empty";
		return;
	}

	$cart = new Cart($db, $customer->getId());
	$cartItemId = $cart->addItem($_POST['product_id']);
	$response['item'] = $cart->getItem($cartItemId);
	$response['cart'] = array(
		"itemCount" => $cart->getNumberOfItems(),
		"totalCost" => $cart->getTotalCost()
	);
}

function removeFromCart(&$db, &$customer, &$response) {
	$cartItemId = $_POST['cartItemId'];
	$cart = new Cart($db, $customer->getId());
	$cart->removeItem($cartItemId);
	$response['status'] = 100;
	$response['message'] = "item was removed";
	$response['cartItemId'] = $cartItemId;

}

function clearCart(&$db, &$customer, &$response) {
	$cart = new Cart($db, $customer->getId());
	$cart->emptyCart();
	$response['status'] = 100;
	$response['message'] = "shopping cart cleared";
	$response['cart'] = array(
		"itemCount" => $cart->getNumberOfItems(),
		"totalCost" => $cart->getTotalCost()
	);
}

?>