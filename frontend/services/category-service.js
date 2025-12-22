let CategoryService = {
  init: function () {
    $("#addCategoryForm").validate({
      submitHandler: function (form) {
        var category = Object.fromEntries(new FormData(form).entries());
        CategoryService.addCategory(category);
        form.reset();
      },
    });
    
    $("#editCategoryForm").validate({
      submitHandler: function (form) {
        var category = Object.fromEntries(new FormData(form).entries());
        CategoryService.editCategory(category);
      },
    });
    
    CategoryService.getAllCategories();
  },

  openAddModal: function () {
    $('#addCategoryModal').modal('show');
  },

  closeModal: function () {
    $('#addCategoryModal').modal('hide');
    $('#editCategoryModal').modal('hide');
    $('#deleteCategoryModal').modal('hide');
  },

  getAllCategories: function () {
    RestClient.get("categories", function (data) {
      Utils.datatable('categories-table', [
        { data: 'id', title: 'ID' },
        { data: 'name', title: 'Name' },
        { data: 'description', title: 'Description' },
        {
          title: 'Actions',
          render: function (data, type, row, meta) {
            return `<div class="d-flex justify-content-center gap-2">
              <button class="btn btn-sm btn-primary" onclick="CategoryService.openEditModal('${row.id}')">Edit</button>
              <button class="btn btn-sm btn-danger" onclick="CategoryService.openDeleteModal('${row.id}', '${row.name}')">Delete</button>
            </div>`;
          }
        }
      ], data, 10);
    }, function (xhr) {
      toastr.error("Failed to load categories");
    });
  },

  addCategory: function (category) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    RestClient.post('categories', category, function (response) {
      $.unblockUI();
      toastr.success("Category added successfully");
      CategoryService.closeModal();
      CategoryService.getAllCategories();
    }, function (xhr) {
      $.unblockUI();
      CategoryService.closeModal();
      toastr.error(xhr?.responseJSON?.error || "Failed to add category");
    });
  },

  getCategoryById: function (id) {
    RestClient.get('categories/' + id, function (data) {
      $('[name="edit_id"]').val(data.id);
      $('[name="edit_name"]').val(data.name);
      $('[name="edit_description"]').val(data.description);
    }, function (xhr) {
      toastr.error("Failed to load category");
    });
  },

  openEditModal: function (id) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    $('#editCategoryModal').modal('show');
    CategoryService.getCategoryById(id);
    $.unblockUI();
  },

  editCategory: function (category) {
    $.blockUI({ message: '<h3>Processing...</h3>' });
    RestClient.put('categories/' + category.edit_id, {
      name: category.edit_name,
      description: category.edit_description
    }, function (data) {
      $.unblockUI();
      toastr.success("Category updated successfully");
      CategoryService.closeModal();
      CategoryService.getAllCategories();
    }, function (xhr) {
      $.unblockUI();
      toastr.error(xhr?.responseJSON?.error || "Failed to update category");
    });
  },

  openDeleteModal: function (id, name) {
    $('#deleteCategoryModal').modal('show');
    $("#delete-category-body").html("Are you sure you want to delete category: <strong>" + name + "</strong>?");
    $("#delete_category_id").val(id);
  },

  deleteCategory: function () {
    var id = $("#delete_category_id").val();
    RestClient.delete('categories/' + id, null, function (response) {
      CategoryService.closeModal();
      toastr.success("Category deleted successfully");
      CategoryService.getAllCategories();
    }, function (xhr) {
      CategoryService.closeModal();
      toastr.error(xhr?.responseJSON?.error || "Failed to delete category");
    });
  }
};
