"use strict"

var products, customerId;
var logo, menuIcon, content, contentTitle,
	sectionStart, sectionCart, sectionProduct, sectionCategory,
	categoryList, productList, subcategoriesList,
	productPrice, productForm, productEditButton,
	breadcrumbs;

var activeState;

document.addEventListener("DOMContentLoaded", function() {
	logo = document.querySelector("header .logo");
	menuIcon = document.querySelector("header svg.icon.menu");

	categoryList = document.querySelector("menu.categories");
	sectionProduct = document.querySelector("section.product");
	sectionCategory = document.querySelector("section.category");
	sectionStart = document.querySelector("section.start");
	sectionCart = document.querySelector("section.cart");

	breadcrumbs = document.querySelector(".breadcrumbs");
	content = document.querySelector("div.content");
	contentTitle = content.querySelector("h2");

	subcategoriesList = sectionCategory.querySelector("ul.subcategories");
	productList = sectionCategory.querySelector("ul.products");

	productPrice = sectionProduct.querySelector("div.price");
	productForm = sectionProduct.querySelector("form");

	productEditButton = sectionProduct.querySelector("button.editProduct");
	//if(productEditButton) productEditButton.addEventListener("click", ProductEditor.open, false);

	logo.addEventListener("click", logoClick, false);
	productList.addEventListener("click", productListClick, false);
	categoryList.addEventListener("click", categoryClick, false);
	subcategoriesList.addEventListener("click", categoryClick, false);
	breadcrumbs.addEventListener("click", categoryClick, false);
	productForm.addEventListener("submit", Cart.add, false);
	menuIcon.addEventListener("click", toggleMenu, false);

	XI.fire("DOMContentLoaded");

	updateContent();
});

window.addEventListener('popstate', function(event) {
	activeState = event.state;
	updateContent();
});



function toggleMenu(e) {
	console.log("toggle menu");
	categoryList.classList.toggle("open");
}

function updateContent() {

	console.log("state", activeState);
	if(!activeState) activeState={};
	var catId;


	if(!activeState) {
		showStartPage();
	} else if(activeState.page) {
		switch(activeState.page) {
			case "cart":
			showCart();
			break;

			default:
			showStartPage();
		}
	} else if(activeState.productId && products[activeState.productId]) {
		catId = activeState.categoryId;

		if(!catId) {
			for(var cId in categoryProducts) {
				if(categoryProducts[cId].indexOf(activeState.productId)!==-1) {
					catId = cId;
					break;
				}
			}
		}
		showProduct(activeState.productId, catId);

	} else if(activeState.categoryId && categories[activeState.categoryId]) {
		showCategoryContents(activeState.categoryId);
	} else {
		showStartPage();
	}

	categoryList.classList.remove("open");
	selectTopCategory(activeState.categoryId);
}


function selectTopCategory(cId) {
	var selected = categoryList.querySelectorAll(".selected");
	for(var i=0; i<selected.length; i++) {
		selected[i].classList.remove("selected");
	}

	if(!cId) return;
	var c = categories[cId];
	while(c.parent!==0) {
		cId = c.parent;
		c = categories[c.parent];
	}
	var topCat = categoryList.querySelector("li[data-id='"+cId+"']");
	topCat.classList.add("selected");
}


function logoClick(e) {
	activeState = {page: "start"};
	history.pushState(activeState, "title", "/");
	updateContent();
}

function categoryClick(e) {
	var li = e.target;
	if(li.dataset.action==="start") {
		logoClick(e);
		return;
	}
	if(!(li.nodeName==="LI" || li.nodeName==="SPAN" || !li.dataset.id)) return;

	var categoryId = li.dataset.id;
	console.log("show category", categoryId);

	activeState = {categoryId: categoryId};
	history.pushState(activeState, "title", "/category/"+categoryId);
	updateContent();
}

function productListClick(e) {
	var li = e.target;
	if(li.nodeName!=="LI") return;

	var productId = li.dataset.id;
	var parentCategoryId = li.dataset.parent;
	console.log("show product", productId);

	activeState = {productId: productId, categoryId: parentCategoryId};
	history.pushState(activeState, "title", "/product/"+productId);
	updateContent();
}



function clearOthers() {
	sectionStart.classList.add("hidden");
	sectionProduct.classList.add("hidden");
	sectionCategory.classList.add("hidden");
	sectionCart.classList.add("hidden");
}


function showStartPage() {
	clearOthers();
	contentTitle.textContent = "Välkommen";
	setTitle();
	sectionStart.classList.remove("hidden");
	printBreadCrumbs();
}

function showCart() {
	clearOthers();
	contentTitle.textContent = "Varukorgen";
	setTitle("Varukorgen");
	sectionCart.classList.remove("hidden");
	printBreadCrumbs();
	Cart.printTable();
}

function showProduct(pId, parentCategoryId) {
	clearOthers();
	var product = products[pId];
	setTitle(product.name);
	sectionProduct.classList.remove("hidden");
	sectionProduct.dataset.id = pId;
	contentTitle.textContent = product.name;
	productPrice.textContent = product.price + " kr";
	productForm.elements['product_id'].value = pId;
	printBreadCrumbs(parentCategoryId);
}

function showCategoryContents(catId) {
	clearOthers();
	var c = categories[catId];
	setTitle(c.name);
	contentTitle.textContent = c.name;
	sectionCategory.classList.remove("hidden");
	printBreadCrumbs(c.parent);
	printSubcategories(catId);
	printProducts(catId);
}



function setTitle(text) {
	document.title = "Joyful" + (text ? " - " + text : "");
}


function printBreadCrumbs(catId) {
	breadcrumbs.innerHTML="";
	printCrumb(catId);

	function printCrumb(catId) {
		var span = document.createElement("span");
		span.classList.add("crumb");
		if(!catId) {
			span.textContent = "Start";
			span.dataset.action = "start";
		} else {
		var cat = categories[catId];
			span.textContent = cat.name;
			span.dataset.id = catId;
			printCrumb(cat.parent);
			breadcrumbs.appendChild(document.createTextNode(">"));
		}
		breadcrumbs.appendChild(span);
	}
}

function printSubcategories(catId) {
	subcategoriesList.innerHTML = "";
	if(!categories[catId].children) return;

	var items = document.createDocumentFragment();
	for(var i=0; i<categories[catId].children.length; i++) {
		var id = categories[catId].children[i];
		var subCat = categories[id];
		var li = document.createElement("LI");
		li.textContent = subCat.name;
		li.dataset.id = id;
		items.appendChild(li);
	}
	subcategoriesList.appendChild(items);
}

function printProducts(catId) {
	var items = document.createDocumentFragment();
	for(var i in categoryProducts[catId]) {
		var id = categoryProducts[catId][i];
		var p = products[id];
		var li = document.createElement("LI");
		li.textContent = p.name;
		li.dataset.id = id;
		li.dataset.parent = catId;
		items.appendChild(li);
	}
	productList.innerHTML = "";
	productList.appendChild(items);
}


