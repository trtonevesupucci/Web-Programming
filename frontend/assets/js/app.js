$(document).ready(function () {
    // Check authentication
    var token = localStorage.getItem("user_token");
    
    if (!token) {
        // Not logged in - show login page
        window.location.replace("pages/login.html");
        return;
    }
    
    // Logged in - generate menu based on role
    UserService.generateMenuItems();
    
    // Setup SPAPP for dynamic page loading
    var app = $.spapp({
        defaultView: "home"
    });
    
    app.run();
});