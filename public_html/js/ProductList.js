var ProductList = (function() {
	var list,

	init = function() {
		list = document.getElementById("productList");
		list.addEventListener("click", clickHandler, false);
		update();
	},

	update = function() {
		list.innerHTML = "";
		for(var i=0; i<products.length; i++) {
			var p = products[i];
			var li = document.createElement("li");
			li.textContent = p.name;
			li.dataset.id = p.id;
			list.appendChild(li);
		}
	},

	clickHandler = function(e) {
		var item = e.target;
		var productId = parseInt(item.dataset.id);
		if(!productId) return;

		var selected = list.querySelectorAll(".selected");
		for(var i=0; i<selected.length; i++) {
			selected[i].classList.remove("selected");
		}

		item.classList.add("selected");

		console.log("clicked productId:", productId);
		ProductEditor.open(productId);

	};



	return {
		init: init,
		update: update
	}
}());