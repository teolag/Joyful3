<?php

class ProductHandler {

	const COLUMNS = "product_id AS id, name, price, created";


	public static function getAllProducts(&$db) {
		$sql = "SELECT " . self::COLUMNS . " FROM products";
		return $db->getClasses($sql, "Product");
	}

	public static function getProduct(&$db, $id) {
		$sql = "SELECT " . self::COLUMNS . " FROM products WHERE product_id = ?";
		return $db->getClass($sql, "Product", array($id));
	}

	public static function save(&$db, $product) {
		$sql = "INSERT INTO products(product_id, name, price) VALUES (:id, :name, :price) ON DUPLICATE KEY UPDATE name=:name, price=:price;";

		$arr = array(
			":id" => $product->getId(),
			":name" => $product->getName(),
			":price" => $product->getPrice()
		);

		$db->update($sql, $arr);
	}


}

?>