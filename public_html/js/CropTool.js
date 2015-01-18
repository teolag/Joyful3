var CropTool = (function() {
	var main, image, originalW, originalH, zoom, ratio, box, boxResizer, crop, cropCallback,
	mouseDown, action, startValue,

	open = function(openUrl, ratio, callback) {
		crop = {url: openUrl, r:ratio};
		cropCallback = callback;

		main = document.createElement("div");
		main.classList.add("ctMain");



		box = document.createElement("div");
		box.classList.add("ctBox");

		boxResizer = document.createElement("div");
		boxResizer.classList.add("ctBoxResizer");

		image = new Image();
		image.src = openUrl;
		image.classList.add("ctImage");
		image.addEventListener("load", imageLoaded, false);

		addEventListener("resize", layout, false);

		document.body.appendChild(main);
	},

	imageLoaded = function() {
		originalW = image.width;
		originalH = image.height;
		main.appendChild(image);
		main.appendChild(box);
		main.appendChild(boxResizer);

		box.addEventListener("mousedown", cropHandler, false);
		box.addEventListener("dblclick", doCrop, false);
		boxResizer.addEventListener("mousedown", cropHandler, false);

		crop.x = 10;
		crop.y = 10;
		crop.w = originalW/2;
		crop.h = originalH/2;

		if(crop.r) {
			crop.h = crop.w*crop.r;
		}

		layout();

	},

	layout = function() {
		zoom = Math.min(
			(window.innerWidth * 0.9)/originalW,
			(window.innerHeight * 0.9)/originalH,
			1
		);
		image.width = originalW * zoom;
		image.height = originalH * zoom;

		main.style.width = image.width + "px";
		main.style.height = image.height + "px";
		main.style.top = ((window.innerHeight-image.height)/2)+"px";
		main.style.left = ((window.innerWidth-image.width)/2)+"px";

		updateBox();
	},

	updateBox = function() {
		box.style.top = (crop.y*zoom) + "px";
		box.style.left = (crop.x*zoom) + "px";
		box.style.width = (crop.w*zoom) + "px";
		box.style.height = (crop.h*zoom) + "px";

		boxResizer.style.top = ((crop.y+crop.h)*zoom-5) + "px";
		boxResizer.style.left = ((crop.x+crop.w)*zoom-5) + "px";
	},

	cropHandler = function(e) {

		if(e.type === "mousedown") {
			e.preventDefault();
			mouseDown = {x: e.pageX, y: e.pageY};
			startValue = {x: crop.x, y: crop.y, w: crop.w, h: crop.h};

			addEventListener("mouseup", cropHandler, false);
			addEventListener("mousemove", cropHandler, false);
			if(e.target===box) {
				console.log("start moving");
				action="move";
			} else if(e.target===boxResizer) {
				console.log("start resizing");
				action="resize";
			}
		} else if(e.type === "mouseup") {
			console.log("end transform");
			action = null;
			removeEventListener("mouseup", cropHandler, false);
			removeEventListener("mousemove", cropHandler, false);
			console.log(crop);
		} else if(e.type === "mousemove") {
			var mouseNow = {x: e.pageX, y: e.pageY};
			var dx = mouseNow.x - mouseDown.x;
			var dy = mouseNow.y - mouseDown.y;
			if(action === "move") {
				crop.x = Math.round(startValue.x + dx/zoom);
				crop.y = Math.round(startValue.y + dy/zoom);

				if(crop.y<0) crop.y = 0;
				if(crop.x<0) crop.x = 0;
				if(crop.y+crop.h>originalH) crop.y = originalH-crop.h;
				if(crop.x+crop.w>originalW) crop.x = originalW-crop.w;

			} else if(action === "resize") {

				crop.w = Math.round(startValue.w + dx/zoom);
				crop.h = Math.round(startValue.h + dy/zoom);

				var minSize = Math.round(20/zoom);

				if(crop.w<minSize) crop.w = minSize;
				if(crop.h<minSize) crop.h = minSize;
				if(crop.y+crop.h>originalH) crop.h = originalH-crop.y;
				if(crop.x+crop.w>originalW) crop.w = originalW-crop.x;

				if(crop.r) {
					crop.h = crop.w*crop.r;
				}
			}
			updateBox();
		}

	},

	doCrop = function() {
		if(cropCallback) {
			cropCallback(crop);
		}
		close();
	},

	close = function() {
		removeEventListener("resize", layout, false);
		document.body.removeChild(main);
	};




	return {
		open: open
	}
}());