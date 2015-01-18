
var sections, menuItems;

document.addEventListener("DOMContentLoaded", function() {
	var btnNewProduct = document.getElementById("btnNewProduct");
	btnNewProduct.addEventListener("click", ProductEditor.createNew, false);

	var btnLogout = document.getElementById("btnLogout");
	btnLogout.addEventListener("click", logout, false);

	var nav = document.getElementsByTagName("menu")[0];
	nav.addEventListener("click", navClick, false);

	sections = document.getElementsByTagName("section");
	menuItems = document.getElementsByTagName("menu")[0].children;

	ProductList.init();
	ProductEditor.init();
	showSection("products");

	CropTool.open("/products/1/8_114_1408.JPG", 2, cropCallback);
});





function navClick(e) {
	var section = e.target.dataset.section;
	if(!section) return;

	showSection(section);
}

function showSection(section) {
	for(var i=0; i<menuItems.length; i++) {
		var li = menuItems[i];
		if(li.dataset.section === section) {
			li.classList.add("active");
		} else {
			li.classList.remove("active");
		}
	}

	for(var i=0; i<sections.length; i++) {
		var div = sections[i];
		if(div.dataset.section === section) {
			div.classList.remove("hidden");
		} else {
			div.classList.add("hidden");
		}
	}

}


function logout() {
	window.location.href = "/admin.php?logout";
}




function getProductById(productId) {
	var product;
	for(var i=0; i<products.length; i++) {
		if(products[i].id === productId) {
			return products[i];
		}
	}
}

function updateProduct(product) {
	for(var i=0; i<products.length; i++) {
		if(products[i].id === product.id) {
			products[i] = product;
		}
	}
}



function cropCallback(data) {
	console.log("Crop callback", data);
}



