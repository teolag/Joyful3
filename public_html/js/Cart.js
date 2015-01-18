var Cart = (function() {

	var cartData={}, items=[];
	var cartIcon, cartTableBody, summaryCost, summaryCount,
		btnClearCart;

	XI.listen("DOMContentLoaded", function() {
		cartIcon = document.querySelector("svg.icon.cart");
		cartTableBody = document.querySelector("table.cart tbody");
		btnClearCart = document.querySelector("button.clearCart");

		btnClearCart.addEventListener("click", clear, false);
		cartIcon.addEventListener("click", function() {
			activeState = {page:"cart"};
			history.pushState(activeState, "title", "/cart/");
			updateContent();
		}, false);

		updateCartSummary();
	});



	var clear = function() {
		Ajax.getJSON("/api/clear-cart", "", clearCallback);
	};

	var clearCallback = function(data) {
		items.length=0;
		cartData = data.cart;
		updateCartSummary();
		printTable();
	};


	var add = function(e) {
		e.preventDefault();
		Ajax.post2JSON("/api/add-to-cart", e.target, addToCartCallback);
		console.log("Add product to cart", e);
	};
	var addToCartCallback = function(data) {
		console.log("add to cart callback", data);
		cartData = data.cart;
		items.push(data.item);
		updateCartSummary();
		flash();
	};



	var remove = function(e) {

		var cartItemId = e.target.dataset.id;
		if(!cartItemId) return;

		Ajax.post2JSON("/api/remove-from-cart", {cartItemId:cartItemId}, removeFromCartCallback);
		console.log("Remove item from cart", cartItemId);
	}
	var removeFromCartCallback = function(data) {
		console.log("CartItem removed", data);

		for(var i=0; i<items.length; i++) {
			if(items[i].cartItemId === data.cartItemId) {
				items.splice(i,1);
				printTable();
			}
		}
	}


	var updateCartSummary = function() {
		/*
		summaryCost.textContent = cartData.totalCost + " kr";
		summaryCount.textContent = cartData.itemCount;
		*/
	};

	var flash = function() {
		cartIcon.className="icon cart flash";
		setTimeout(function() {
			cartOverview.className="icon cart fadeOut";
		}, 1);
	};

	var setCartData = function(data) {
		cartData = data;
	};
	var setCartItems = function(cartItems) {
		items = cartItems;
	};

	var printTable = function() {
		cartTableBody.innerHTML="";

		for(var i=0; i<items.length; i++) {
			var item = items[i];
			var product = products[item.productId];

			var tr = document.createElement("TR");

				var td = document.createElement("TD");
					td.textContent = product.name;
				tr.appendChild(td);

				var td = document.createElement("TD");
					td.textContent = product.price + " kr";
				tr.appendChild(td);

				var td = document.createElement("TD");
					var input = document.createElement("input");
						input.type="hidden";
						input.name="item["+i+"][productId]";
						input.value = item.productId;
					td.appendChild(input);

					var input = document.createElement("input");
						input.type="text";
						input.size = 3;
						input.name="item["+i+"][amount]";
						input.value = 1;
					td.appendChild(input);

					var btnRemove = document.createElement("button");
					btnRemove.textContent = "X";
					btnRemove.type="button";
					btnRemove.dataset.id=item.cartItemId;
					btnRemove.addEventListener("click", remove, false);
					td.appendChild(btnRemove);
				tr.appendChild(td);

			cartTableBody.appendChild(tr);
		}
	};


	return {
		add: add,
		setCartData: setCartData,
		setCartItems: setCartItems,
		flash: flash,
		printTable: printTable
	}
}());