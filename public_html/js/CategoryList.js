var CategoryList = (function() {
	var list,

	init = function() {
		list = document.getElementById("categoryList");
		list.addEventListener("click", clickHandler, false);
		update();
	},

	update = function() {
		list.innerHTML = "";

		var root = getCategory(categories[0]);
		list.appendChild(root);


		function getCategory(c) {
			console.log("cat", c);
			var li = document.createElement("li");
			li.textContent = c.name;
			li.dataset.id = c.id;

			if(c.children && c.children.length>0) {
				var ul = document.createElement("ul");
				for(var i=0; i<c.children.length; i++) {
					var child = categories[c.children[i]];
					ul.appendChild(getCategory(child));
				}
				li.appendChild(ul);
			}
			return li;
		}

	},

	clickHandler = function(e) {

		var item = e.target;
		var categoryId = parseInt(item.dataset.id);
		if(!categoryId) return;

		var selected = list.querySelectorAll(".selected");
		for(var i=0; i<selected.length; i++) {
			selected[i].classList.remove("selected");
		}

		item.classList.add("selected");

		console.log("clicked categoryId:", categoryId);
		CategoryEditor.open(categoryId);

	};



	return {
		init: init,
		update: update
	}
}());