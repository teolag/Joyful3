<?php


require "/var/www/DatabasePDO/DatabasePDO.php";
require "../config.php";
require "../includes/ProductHandler.php";
require "../includes/Product.php";


$db = new DatabasePDO($config['db']['server'], $config['db']['username'], $config['db']['password'], $config['db']['name']);




echo "Hämta batman (id 6):<br>";
$batman = ProductHandler::getProduct($db, 6);
echo $batman . "<br>";
echo "Sätt nytt random pris<br>";
$batman->setPrice(rand(10,100));
echo $batman . "<br>";
ProductHandler::save($db, $batman);
echo "Spara<br>";
echo "<br>";


echo "Skriv ut batman som json<br>";
$json = json_encode($batman);
echo $json;
echo "<br><br>";


echo "Hämta alla produkter:<br>";
$products = ProductHandler::getAllProducts($db);
foreach($products as $product) {
	echo $product . "<br>";
}
echo "<br>";


echo "Skriv alla som json<br>";
$json = json_encode($products);
echo $json;
echo "<br><br>";





?>