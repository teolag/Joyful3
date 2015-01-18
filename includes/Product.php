<?php

class Product implements JsonSerializable{
	private $id;
	private $name;
	private $price;


	public function __construct() {
	}

	public function __toString() {
		return "[" . $this->id . "] " . $this->name . ", " . $this->price . "kr";
	}

	public function jsonSerialize() {
        return array(
			"id"=>$this->id,
			"name"=>$this->name,
			"price"=>$this->price
		);
    }

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getPrice() {
		return $this->price;
	}

	public function setPrice($price) {
		$this->price = $price;
	}

}
?>