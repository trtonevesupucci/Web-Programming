let MenuItemService = {
  init: function () {
    $("#addMenuItemForm").validate({
      submitHandler: function (form) {
        var item = Object.fromEntries(new FormData(form).entries());
        MenuItemService.addMenuItem(item);
        form.reset();
      },
    });
    
    $("#editMenuItemForm").validate({
      submitHandler: function (form) {
        var item = Object.fromEntries(new FormData(form).entries());
        MenuItemService.editMenuItem(item);
      },
    });
    
    MenuItemService.getAllMenuItems();
    MenuItemService.loadCategories();
  },

  loadCategories: function () {
    RestClient.get("categories", function (data) {
      let options = '<option value="">Select Category</option>';
      data.forEach(function (cat) {
        options += '<option value="' + cat.id + '">' + cat.name + '</option>';
      });
      $('[name="category_id"], [name="edit_category_id"]').html(options);
    });
  },

  openAddModal: function () {
    $('#addMenuItemModal').modal('show');
  },

  closeModal: function () {
    $('#addMenuItemModal').modal('hide');
    $('#editMenuItemModal').modal('hide');
    $('#deleteMenuItemModal').modal('hide');
  },

  getAllMenuItems: function () {
    RestClient.get("menu-items", function (data) {
      Utils.datatable('menu-items-table', [
        { data: 'id', title: 'ID' },
        { data: 'name', title: 'Name' },
        { data: 'description', title: 'Description' },
        { data: 'price', title: 'Price' },
        {
          title: 'Actions',
          render: function (data, type, row, meta) {
            return `<div class="d-flex justify-content-center gap-2">
              <button class="btn btn-sm btn-primary" onclick="MenuItemService.openEditModal('${row.id}')">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="MenuItemService.openDeleteModal('${row.id}', '${row.name}')">Delete</button>
            </div>`;
          }
        }
      ], data, 10);
    });
  },

  addMenuItem: function (item) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    RestClient.post('menu-items', item, function (response) {
      $.unblockUI();
      toastr.success("Menu item added successfully");
      MenuItemService.closeModal();
      MenuItemService.getAllMenuItems();
    }, function (xhr) {
      $.unblockUI();
      MenuItemService.closeModal();
      toastr.error(xhr?.responseJSON?.error || "Failed to add menu item");
    });
  },

  getMenuItemById: function (id) {
    RestClient.get('menu-items/' + id, function (data) {
      $('[name="edit_id"]').val(data.id);
      $('[name="edit_name"]').val(data.name);
      $('[name="edit_description"]').val(data.description);
      $('[name="edit_price"]').val(data.price);
      $('[name="edit_category_id"]').val(data.category_id);
    });
  },

  openEditModal: function (id) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    $('#editMenuItemModal').modal('show');
    MenuItemService.getMenuItemById(id);
    $.unblockUI();
  },

  editMenuItem: function (item) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    RestClient.put('menu-items/' + item.edit_id, {
      name: item.edit_name,
      description: item.edit_description,
      price: item.edit_price,
      category_id: item.edit_category_id
    }, function (data) {
      $.unblockUI();
      toastr.success("Menu item updated successfully");
      MenuItemService.closeModal();
      MenuItemService.getAllMenuItems();
    }, function (xhr) {
      $.unblockUI();
      toastr.error(xhr?.responseJSON?.error || "Failed to update menu item");
    });
  },

  openDeleteModal: function (id, name) {
    $('#deleteMenuItemModal').modal('show');
    $("#delete-menu-item-body").html("Are you sure you want to delete: <strong>" + name + "</strong>?");
    $("#delete_menu_item_id").val(id);
  },

  deleteMenuItem: function () {
    var id = $("#delete_menu_item_id").val();
    RestClient.delete('menu-items/' + id, null, function (response) {
      MenuItemService.closeModal();
      toastr.success("Menu item deleted successfully");
      MenuItemService.getAllMenuItems();
    }, function (xhr) {
      MenuItemService.closeModal();
      toastr.error(xhr?.responseJSON?.error || "Failed to delete menu item");
    });
  }
};
