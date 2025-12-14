var UserService = {
  init: function () {
    var token = localStorage.getItem("user_token");
    if (token && token !== undefined) {
      window.location.replace("../index.html");
    }
    $("#login-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        UserService.login(entity);
      },
    });
  },

  login: function (entity) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      success: function (result) {
        console.log(result);
        localStorage.setItem("user_token", result.data.token);
        window.location.replace("../index.html");
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(XMLHttpRequest?.responseJSON?.error || XMLHttpRequest?.responseText || "Login failed");
      },
    });
  },

  logout: function () {
    localStorage.clear();
    window.location.replace("pages/login.html");
  },

  generateMenuItems: function () {
    const token = localStorage.getItem("user_token");
    const decodedToken = Utils.parseJwt(token);
    
    if (!decodedToken || !decodedToken.user) {
      window.location.replace("pages/login.html");
      return;
    }

    const user = decodedToken.user;

    if (user && user.role) {
      let nav = "";
      let main = "";
      
      switch (user.role) {
        case Constants.CUSTOMER_ROLE:
          nav = 
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#menu">Menu</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#orders">Orders</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#reservations">Reservations</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#profile">Profile</a>' +
            '</li>' +
            '<li>' +
              '<button class="btn btn-primary" onclick="UserService.logout()">Logout</button>' +
            '</li>';
          $("#tabs").html(nav);
          
          main =
            '<section id="menu" data-load="pages/menu.html"></section>' +
            '<section id="orders" data-load="pages/orders.html"></section>' +
            '<section id="reservations" data-load="pages/reservations.html"></section>' +
            '<section id="profile" data-load="pages/profile.html"></section>';
          $("#spapp").html(main);
          break;

        case Constants.ADMIN_ROLE:
          nav = 
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#categories">Categories</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#menu">Menu Items</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#orders">Orders</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#reservations">Reservations</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#users">Users</a>' +
            '</li>' +
            '<li>' +
              '<button class="btn btn-primary" onclick="UserService.logout()">Logout</button>' +
            '</li>';
          $("#tabs").html(nav);
          
          main =
            '<section id="categories" data-load="pages/admin/categories.html"></section>' +
            '<section id="menu" data-load="pages/admin/menu.html"></section>' +
            '<section id="orders" data-load="pages/admin/orders.html"></section>' +
            '<section id="reservations" data-load="pages/admin/reservations.html"></section>' +
            '<section id="users" data-load="pages/admin/users.html"></section>';
          $("#spapp").html(main);
          break;

        case Constants.STAFF_ROLE:
          nav = 
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#orders">Orders</a>' +
            '</li>' +
            '<li class="nav-item mx-0 mx-lg-1">' +
              '<a class="nav-link py-3 px-0 px-lg-3 rounded" href="#reservations">Reservations</a>' +
            '</li>' +
            '<li>' +
              '<button class="btn btn-primary" onclick="UserService.logout()">Logout</button>' +
            '</li>';
          $("#tabs").html(nav);
          
          main =
            '<section id="orders" data-load="pages/staff/orders.html"></section>' +
            '<section id="reservations" data-load="pages/staff/reservations.html"></section>';
          $("#spapp").html(main);
          break;

        default:
          window.location.replace("pages/login.html");
      }
    } else {
      window.location.replace("pages/login.html");
    }
  }
};
