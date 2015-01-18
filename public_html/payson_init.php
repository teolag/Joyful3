<?php
define("ROOT", $_SERVER['DOCUMENT_ROOT']."/../");

session_start();
require ROOT . "config.php";
require "/var/www/DatabasePDO/DatabasePDO.php";
require "/var/www/PaysonAPI/lib/paysonapi.php";

$db = new DatabasePDO($config['db']['server'],$config['db']['username'],$config['db']['password'],$config['db']['name']);

?>