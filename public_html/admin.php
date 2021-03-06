<?php
session_start();
require "../config.php";
require "/git/DatabasePDO/DatabasePDO.php";
$db = new DatabasePDO($config['db']['server'], $config['db']['username'], $config['db']['password'], $config['db']['name']);

require "../includes/ProductHandler.php";
require "../includes/Product.php";

require "../includes/actions.php";
$categories = getCategories($db);
$products = ProductHandler::getAllProducts($db);
$categoryProducts = getCategoryProducts($db);


if($_POST['password'] === $config['adminPass']) {
	$_SESSION['admin'] = true;
} elseif(isset($_GET['logout'])) {
	unset($_SESSION['admin']);
}

$admin = isset($_SESSION['admin']);

?>


<!doctype html>
<html>
	<head>
		<title>Joyful Admin</title>
		<meta charset="utf-8" />
		<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Roboto' type='text/css'>
		<link rel="stylesheet" href="http://xio.se/projects/croptool/CropTool.css" type="text/css" />
		<link rel="stylesheet" href="/css/admin.css" type="text/css" />
	</head>
	<body>

		<?php if(!$admin): ?>
		<form action="/admin.php" method="post">
			<input type="password" name="password" autofocus />
			<button type="submit">Logga in</button>
		</form>
		<?php else: ?>



		<header>
			<h1>Joyful Admin</h1>
			<button id="btnLogout">Logga ut</button>
			<menu>
				<li data-section="products">Produkter</li>
				<li data-section="categories">Kategorier</li>
			</menu>
		</header>


		<section data-section="categories">
			<aside>
				<button id="btnNewCategory">Ny kategori</button>
				<ul id="categoryList"></ul>
			</aside>
			<div id="category">
				<div class="title">Kategorier</div>
				<form name="category">
					<input type="text" readonly size="2" name="category_id" />
					<input type="text" placeholder="Kategorins namn" name="name" />
					<button type="input" name="save">Spara</button>
				</form>
			</div>
		</section>

		<section data-section="products">
			<aside>
				<button id="btnNewProduct">Ny produkt</button>
				<ul id="productList"></ul>
			</aside>

			<div id="product" class="hidden">
				<div class="title"></div>
				<form name="product">
					<input type="text" name="product_id" size="2" readonly />
					<input type="text" placeholder="Produktens namn" name="name" />
					<input type="number" size="3" placeholder="Pris" name="price" />
					<button type="input" name="save">Spara</button>
				</form>

				<ul class="images"></ul>
				<input type="file" multiple="multiple" class="uploadImage" />
			</div>
		</section>

		<script src="http://beta.xio.se/js/XI.js"></script>
		<script src="http://beta.xio.se/AjaXIO/AjaXIO.js"></script>
		<script src="http://xio.se/projects/croptool/CropTool.js"></script>
		<script src="/js/CategoryList.js"></script>
		<script src="/js/CategoryEditor.js"></script>
		<script src="/js/ProductList.js"></script>
		<script src="/js/ProductEditor.js"></script>
		<script src="/js/admin.js"></script>

		<script>
			var categories = <?php echo json_encode($categories, JSON_NUMERIC_CHECK); ?>;
			var products = <?php echo json_encode($products, JSON_NUMERIC_CHECK); ?>;
			var categoryProducts = <?php echo json_encode($categoryProducts, JSON_NUMERIC_CHECK); ?>;
		</script>

		<?php endif; ?>

	</body>
</html>