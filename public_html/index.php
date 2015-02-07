<?php

session_start();
$admin = isset($_SESSION['admin']);

require "../config.php";

require "/git/DatabasePDO/DatabasePDO.php";
$db = new DatabasePDO($config['db']['server'], $config['db']['username'], $config['db']['password'], $config['db']['name']);

require "../includes/ProductHandler.php";
require "../includes/Product.php";

require "../includes/actions.php";
require "../includes/CssMagic.php";
require "../includes/Customer.php";
require "../includes/Cart.php";
$customer = new Customer($db);
$customerId = $customer->getId();
$categories = getCategories($db);
$products = getProducts($db);
$categoryProducts = getCategoryProducts($db);

$cart = new Cart($db, $customerId);


CssMagic::addFile("css/style.css");
CssMagic::addVariables("css/variables.json");


if(isset($_GET['page'])) {
	$startupState = array("page"=>$_GET['page']);
} elseif(isset($_GET['productId'])) {
	$category = $products[$_GET['productId']];
	$startupState = array("productId"=>$_GET['productId']);
} elseif(isset($_GET['category'])) {
	$category = $categories[$_GET['category']];
	$startupState = array("categoryId"=>$_GET['category']);
}

?>



<!doctype html>
<html>
	<head>
		<title>Joyful<?php if($admin) echo " admin" ?></title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<?php CssMagic::printInclude(); ?>
	</head>
	<body<?php if($admin) echo " class='admin'" ?>>

		<header>
			<svg class="icon menu"><use xlink:href="/img/icons2.svg#icon-menu" /></svg>
			<svg class="logo" alt="Joyful by Joona"><use xlink:href="/img/icons2.svg#svg-logo"/></svg>
			<svg class="icon cart"><use xlink:href="/img/icons2.svg#icon-cart" /></svg>

			<!--
			<div id="cart">
				<span class="count"></span>
				<span class="cost"></span>
			</div>
			-->
		</header>

		<div id="page">

			<menu class="categories"></menu>

			<div class="content">
				<h2></h2>
				<div class="breadcrumbs"></div>
				<section class="start">
					hohoooo
				</section>

				<section class="cart hidden">
					<form action="/payson_checkout.php" method="post">
						<table class="cart">
							<colgroup>
								<col>
								<col align="center" style="text-align: right;">
							</colgroup>
							<tbody></tbody>
						</table>
						<button type="button" class="clearCart">Töm varukorgen</button>
						<button type="submit">Betala</button>
					</form>
				</section>

				<section class="category hidden">
					<ul class="subcategories"></ul>
					<ul class="products"></ul>
				</section>

				<section class="product hidden">
					<button type="button" class="admin editProduct">
						Edit
					</button>
					<div class="price">123 kr</div>
					<form>
						<input type="hidden" name="product_id" value="" />
						<button type="submit">Lägg i korgen</button>
					</form>
				</section>
			</div>

			<div id="customer">
				CustomerId: <?php echo $customerId; ?>
			</div>
		</div>

		<script src="http://beta.xio.se/js/XI.js"></script>
		<script src="http://beta.xio.se/AjaXIO/AjaXIO.js"></script>
		<script src="/js/Categories.js"></script>
		<script src="/js/Cart.js"></script>

		<script src="/js/main.js"></script>
		<script>
			<?php if($admin) { echo "var admin = true;"; } ?>
			var categories = <?php echo json_encode($categories, JSON_NUMERIC_CHECK); ?>;
			var products = <?php echo json_encode($products, JSON_NUMERIC_CHECK); ?>;
			var categoryProducts = <?php echo json_encode($categoryProducts, JSON_NUMERIC_CHECK); ?>;
			Cart.setCartData({itemCount: <?php echo $cart->getNumberOfItems();?>, totalCost:  <?php echo $cart->getTotalCost();?>});
			Cart.setCartItems(<?php echo json_encode($cart->getItems(), JSON_NUMERIC_CHECK); ?>);
			activeState = <?php echo json_encode($startupState, JSON_NUMERIC_CHECK); ?>;
		</script>

	</body>
</html>