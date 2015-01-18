<?php
class Customer {
	private	$sessionID;
	private $db;
	private $status;
	private $visit_id;
	private $customer_id;

	const NEW_VISIT_TIME_LIMIT = 14400; //4hours

	const NEW_CUSTOMER = 1;
	const NEW_VISIT = 2;
	const NEW_HIT = 3;

	public function __construct($db) {
		$this->db = $db;
		$this->sessionID = session_id();

		//Try to find a customer_id
		if(!empty($_SESSION['customer_id'])) $this->customer_id = $_SESSION['customer_id'];
		elseif(!empty($_COOKIE['customer_id'])) $this->customer_id = $_COOKIE['customer_id'];

		//If no customer_id is found
		if(empty($this->customer_id)) {
			$this->insertNewCustomer();
		}

		//Try to find last visit
		$this->visit_id = $_SESSION['visit_id'];
		$this->last_visit = ($_SESSION['last_visit']>0) ? $_SESSION['last_visit'] : 0;

		//If old visit is gone or too long ago, create new visit
		if(empty($this->visit_id) || (time()-$this->last_visit) > self::NEW_VISIT_TIME_LIMIT) {
			$this->insertNewVisit();
		}

		//Register this hit
		$this->insertNewHit();


		$_SESSION['visit_id'] = $this->visit_id;
		$_SESSION['last_visit'] = time();
		$_SESSION['customer_id'] = $this->customer_id;
		setcookie('customer_id', $this->customer_id, time()+(60*60*24*365), '/');
	}

	public function __toString() {
		$out = "customer_id: ".$this->customer_id."<br />";
		$out .= "visit_id: ".$this->visit_id."<br />";

		return $out;
	}

	public function getId() {
		return $this->customer_id;
	}

	private function insertNewCustomer() {
		$sql = "INSERT INTO customers() VALUES()";
		$this->customer_id = $this->db->insert($sql);
	}

	private function insertNewVisit() {
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
		$referer = $_SERVER['HTTP_REFERER'];
		if(empty($referer)) $referer="";

		$sql = "INSERT INTO visits(agent, ip, accept_language, referer, customer_id) VALUES(?,?,?,?,?)";
		$this->visit_id = $this->db->insert($sql, array($agent, $ip, $accept_language, $referer, $this->customer_id));
	}

	private function insertNewHit() {
		$uri = $_SERVER["REQUEST_URI"];
		$visuri_id = $this->db->getValue("SELECT visuri_id FROM visuris WHERE uri=? LIMIT 1", array($uri));

		if(empty($visuri_id)) {
			$visuri_id = $this->db->insert("INSERT INTO visuris(uri, requests) VALUES(?, ?)", array($uri, 1));
		}
		else {
			$sql = "UPDATE visuris SET requests=requests+1 WHERE visuri_id=?";
			$this->db->execute($sql, array($visuri_id));
		}

		$sql = "INSERT INTO vishits(visit_id, visuri_id) VALUES(?,?)";
		$this->db->insert($sql, array($this->visit_id, $visuri_id));
	}

}

?>