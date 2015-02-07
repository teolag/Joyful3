var ProductEditor = (function() {

	var box, boxTitle, form, imageList,f

	init = function() {
		box = document.getElementById("product");
		boxTitle = box.querySelector(".title");

		form = box.querySelector("form");
		form.addEventListener("submit", save, false);

		var btnUploadImage = box.querySelector(".uploadImage");
		btnUploadImage.addEventListener("change", uploadImages, false);

		imageList = box.querySelector(".images");

	},

	clear = function() {
		form.reset();
		form.elements['product_id'].value = "";
		imageList.innerHTML="";
	},

	createNew = function() {
		var name = prompt("Namn på den nya produkten?");
		if(!name) return;

		Ajax.post2JSON("/api/save-product", {name:name, price:0}, function(data) {
			open(data.id);
		});

	},

	open = function(productId) {
		clear();
		var product = getProductById(productId);

		update(product);
		box.classList.remove("hidden");
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
			var uri = "/products/" + image.product_id + "/" +image.product_image_id +"_"+image.name;
			console.log("image", image);


			var img = new Image();
			img.src= "/image.php?uri="+escape(uri)+"&type=productThumb";

			imageList.appendChild(img);
		}


	},

	uploadImages = function(e) {
		var fileQueue = [];
		for(var i=0; i<e.target.files.length; i++) {
			fileQueue.push(e.target.files[i]);
		}
		console.log("fileQueue", fileQueue);
		e.target.value = "";

		handleNextInQueue();


		function handleNextInQueue() {
			if(fileQueue.length===0) return;
			var file = fileQueue.shift();
			var reader = new FileReader();
			reader.addEventListener("load", fileRead, false);
			reader.readAsDataURL(file);

			function fileRead(e) {
				CropTool.open({
					dataUrl: reader.result,
					onCrop: cropDone,
					onCancel: cropAborted
				});
			}

			function cropDone(data) {
				console.log("send, crop & resize", data);

				var formData = new FormData();
				formData.append('productId', form.elements['product_id'].value);
				formData.append('file', file);
				formData.append('left', data.left);
				formData.append('top', data.top);
				formData.append('width', data.width);
				formData.append('height', data.height);
				Ajax.post2JSON("/api/upload-product-image", formData, uploadDone);
				handleNextInQueue();
			}

			function cropAborted(data) {
				console.log("cropAbort", data);
				handleNextInQueue();
			}

			function uploadDone(data) {
				console.log("UploadDone", data)
			}
		};

	};




	return {
		init: init,
		createNew: createNew,
		open: open
	}
}());