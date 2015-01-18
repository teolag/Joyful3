<?php
class Cart {
	private $db;
	private $customerId;

	public function __construct($db, $customerId) {
		$this->db = $db;
		$this->customerId = $customerId;
	}

	public function __toString() {

	}

	public function getTotalCost() {
		$productsCost = $this->db->getValue("SELECT SUM(price) FROM cart_items JOIN products USING(product_id) WHERE customer_id=?", array($this->customerId));
		$extraCost = $this->db->getValue("SELECT SUM(add_price) FROM cart_item_choices JOIN choices USING(choice_id) JOIN cart_items USING(cart_item_id) WHERE customer_id=?", array($this->customerId));
		return intval($extraCost) + intval($productsCost);
	}

	public function getNumberOfItems() {
		$no = $this->db->getValue("SELECT COUNT(cart_item_id) FROM cart_items WHERE customer_id=?", array($this->customerId));
		return $no;
	}
	public function getItem($cartItemId) {
		return $this->db->getRow("SELECT cart_item_id AS cartItemId, product_id AS productId, added FROM cart_items WHERE cart_item_id=?", array($cartItemId));
	}
	public function getItems() {
		return $this->db->getArray("SELECT cart_item_id AS cartItemId, product_id AS productId, added FROM cart_items WHERE customer_id=?", array($this->customerId));
	}

	public function emptyCart() {
		$this->db->query("DELETE FROM cart_items WHERE customer_id=?", array($this->customerId));
	}

	public function removeItem($cartItemId) {
		return $this->db->getRow("DELETE FROM cart_items WHERE cart_item_id=? AND customer_id=?", array($cartItemId, $this->customerId));
	}

	public function addItem($productId) {
		$newId = $this->db->insert("INSERT INTO cart_items(product_id, customer_id) VALUES(?,?)", array($productId, $this->customerId));
		return $newId;
	}



}

?>