let MenuService = {
  init: function () {
    MenuService.getAllMenuItems();
  },

  getAllMenuItems: function () {
    RestClient.get("menu-items", function (data) {
      let html = '<div class="row">';
      data.forEach(function (item) {
        html += `
          <div class="col-md-4 mb-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">${item.name}</h5>
                <p class="card-text">${item.description}</p>
                <h6 class="card-subtitle mb-2 text-muted">$${item.price}</h6>
                <button class="btn btn-sm btn-primary" onclick="MenuService.addToOrder('${item.id}', '${item.name}', '${item.price}')">Add to Order</button>
              </div>
            </div>
          </div>
        `;
      });
      html += '</div>';
      $("#menu-items-container").html(html);
    }, function (xhr) {
      toastr.error("Failed to load menu");
    });
  },

  addToOrder: function (itemId, itemName, itemPrice) {
    toastr.success(itemName + " added to cart!");
    // Store in session or localStorage for checkout
    let cart = JSON.parse(localStorage.getItem("cart") || "[]");
    cart.push({
      menu_item_id: itemId,
      name: itemName,
      price: parseFloat(itemPrice),
      quantity: 1
    });
    localStorage.setItem("cart", JSON.stringify(cart));
  }
};
