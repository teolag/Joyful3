var ProductEditor = (function() {

	var box, boxTitle, form, imageList,

	init = function() {
		box = document.getElementById("product");
		boxTitle = box.querySelector(".title");

		form = box.querySelector("form");
		form.addEventListener("submit", save, false);

		var btnUploadImage = box.querySelector(".uploadImage");
		btnUploadImage.addEventListener("change", uploadImages, false);

		imageList = box.querySelector(".images");

		createNew();
	},

	clear = function() {
		form.reset();
		form.elements['product_id'].value = "";
		imageList.innerHTML="";
	},

	createNew = function() {
		clear();
		boxTitle.textContent = "Ny produkt";
	},

	open = function(productId) {
		clear();
		var product = getProductById(productId);
		update(product);
		getProductImages(productId);
	},

	update = function(product) {
		boxTitle.textContent = "Editera produkt - " + product.name;
		form.elements['product_id'].value = product.id;
		form.elements['name'].value = product.name;
		form.elements['price'].value = product.price;
	},

	save = function(e) {
		e.preventDefault();
		Ajax.post2JSON("/api/save-product", form, saveCallback);
		form.elements['save'].disabled = true;
		form.elements['save'].textContent = "Sparar...";
	},

	saveCallback = function(data) {
		console.log("save callback", data);
		form.elements['save'].disabled = false;
		form.elements['save'].textContent = "Spara";

		if(data.added) {
			products.push(data.product);
		} else {
			updateProduct(data.product);
		}
		update(data.product);
		ProductList.update();
	},

	getProductImages = function(productId) {
		Ajax.getJSON("/api/get-product-images", {product_id:productId}, productImagesCallback);
	},

	productImagesCallback = function(data) {
		console.log("sådär ja", data);
		imageList.innerHTML="";

		for(var i=0; i<data.images.length; i++) {
			var image = data.images[i];
			console.log("image", image);

			var li = document.createElement("li");

			var img = new Image();
			img.src="/products/" + image.product_id + "/" +image.product_image_id +"_"+image.name;
			img.height=80;
			li.appendChild(img);

			imageList.appendChild(li);
		}


	},

	uploadImages = function(e) {
		var productId = form.elements['product_id'].value;

		for(var i=0; i<e.target.files.length; i++) {
			var file = e.target.files[i];
			var xhr = new XMLHttpRequest();
			xhr.upload.addEventListener('progress', function(e){
				console.log((100*e.loaded/e.total)+'%');
			}, false);
			xhr.responseType = 'json';
			xhr.onload = imageUploaded;
			xhr.open("POST", "/api/upload-product-image", true);
			var data = new FormData();
			data.append('productId', productId);
			data.append('file', file);
			xhr.send(data);

			console.log("upload file", file);
		}
	},

	imageUploaded = function(e) {
		var data = e.target.response;
		console.log("Image uploaded", data);

		CropTool.open(data.url, imageCropped);
	},

	imageCropped = function(data) {

	};



	return {
		init: init,
		createNew: createNew,
		open: open
	}
}());