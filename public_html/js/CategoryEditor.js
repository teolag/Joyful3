var CategoryEditor = (function() {

	var box, boxTitle, form,

	init = function() {
		box = document.getElementById("category");
		boxTitle = box.querySelector(".title");

		form = box.querySelector("form");
		form.addEventListener("submit", save, false);

	},

	clear = function() {
		form.reset();
		form.elements['category_id'].value = "";
	},

	createNew = function() {
		var name = prompt("Namn på den nya kategorin?");
		if(!name) return;

		console.log("spara kategori inte implementerat");
		/*
		Ajax.post2JSON("/api/save-product", {name:name, price:0}, function(data) {
			open(data.id);
		});
		*/
	},

	open = function(categoryId) {
		clear();
		var category = categories[categoryId];

		update(category);
		box.classList.remove("hidden");
	},

	update = function(category) {
		boxTitle.textContent = "Editera kategori - " + category.name;
		form.elements['category_id'].value = category.id;
		form.elements['name'].value = category.name;
	},

	save = function(e) {
		e.preventDefault();
		/*
		Ajax.post2JSON("/api/save-product", form, saveCallback);
		form.elements['save'].disabled = true;
		form.elements['save'].textContent = "Sparar...";
		*/
	},

	saveCallback = function(data) {
		console.log("save callback", data);
		form.elements['save'].disabled = false;
		form.elements['save'].textContent = "Spara";

		if(data.added) {
			categories.push(data.category);
		} else {
			categories[data.category.id] = (data.category);
		}
		update(data.category);
		CategoryList.update();
	};


	return {
		init: init,
		createNew: createNew,
		open: open
	}
}());