var Categories = (function(){

	XI.listen(['DOMContentLoaded'], function() {
		printRootCategories();
	});


	function load() {
		Ajax.getJSON("/api/get-categories", "", loadCallback);
	}

	function loadCallback(r) {
		categories = r.categories;
		XI.fire("categoriesLoaded");
	}



	function printRootCategories() {
		categoryList.innerHTML="";
		var rootIds = categories[0].children;
		for(var i=0; i<rootIds.length; i++) {
			var item = categories[rootIds[i]];
			var li = document.createElement("LI");
			li.textContent = item.name;
			li.dataset.id = rootIds[i];
			categoryList.appendChild(li);
		}
	}

	function printCategories(recursive) {
		recursive = !!recursive;
		categoryList.innerHTML="";
		var root = processCategories(categories[0].children, recursive);
		categoryList.appendChild(root);

		function processCategories(ids, recursive) {
			var ol = document.createElement("OL");
			for(var i=0; i<ids.length; i++) {
				var item = categories[ids[i]];
				var li = document.createElement("LI");
				li.textContent = item.name;
				li.dataset.id = ids[i];
				if(recursive && item.children) {
					li.appendChild(processCategories(item.children, recursive));
				}
				ol.appendChild(li);
			}
			return ol;
		}
	}




	return {
		load: load
	}

}());