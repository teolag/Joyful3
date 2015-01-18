<?php
session_start();
setcookie('customer_id', NULL, time()-100, '/');
unset($_SESSION['customer_id']);
unset($_SESSION['last_visit']);
unset($_SESSION['visit_id']);

print_r($_SESSION);
print_r($_COOKIES);
?>