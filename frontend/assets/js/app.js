$(document).ready(function () {
    // Inicijalizuj SPApp
    var app = $.spapp({
        defaultView: "home",
        templateDir: "pages/"
    });

    // Definiši sve rute
    app.route({
        view: "home",
        load: "home.html"
    });

    app.route({
        view: "menu",
        load: "menu.html",
        onReady: function() {
            initMenuPage();
        }
    });

    app.route({
        view: "reservations",
        load: "reservations.html",
        onReady: function() {
            initReservationPage();
        }
    });

    app.route({
        view: "orders",
        load: "orders.html",
        onReady: function() {
            checkAuth();
            initOrdersPage();
        }
    });

    app.route({
        view: "login",
        load: "login.html",
        onReady: function() {
            initLoginPage();
        }
    });

    app.route({
        view: "register",
        load: "register.html",
        onReady: function() {
            initRegisterPage();
        }
    });

    app.route({
        view: "profile",
        load: "profile.html",
        onReady: function() {
            checkAuth();
            initProfilePage();
        }
    });

    app.route({
        view: "admin",
        load: "admin.html",
        onReady: function() {
            checkAuth();
            checkAdmin();
            initAdminPage();
        }
    });

    app.route({
        view: "404",
        load: "404.html"
    });

    // Pokreni aplikaciju
    app.run();

    // Setup mobile menu toggle
    setupMobileMenu();
    
    // Setup logout button
    setupLogout();
    
    // Update navigation on load
    updateNavigation();
});



function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.hash = 'login';
        return false;
    }
    return true;
}

function checkAdmin() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (user.role !== 'admin') {
        alert('Access denied. Admin only!');
        window.location.hash = 'home';
        return false;
    }
    return true;
}

function updateNavigation() {
    const token = localStorage.getItem('token');
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    if (token) {
        // User is logged in
        $('#loginLink').hide();
        $('#registerLink').hide();
        $('#profileLink').show();
        $('#logoutBtn').show();
        
        // Show admin link only for admins
        if (user.role === 'admin') {
            $('#adminLink').show();
        } else {
            $('#adminLink').hide();
        }
    } else {
        // User is not logged in
        $('#loginLink').show();
        $('#registerLink').show();
        $('#profileLink').hide();
        $('#adminLink').hide();
        $('#logoutBtn').hide();
    }
}

function setupLogout() {
    $('#logoutBtn').off('click').on('click', function(e) {
        e.preventDefault();
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        updateNavigation();
        window.location.hash = 'home';
        alert('Logged out successfully!');
    });
}

function setupMobileMenu() {
    $('#navToggle').off('click').on('click', function() {
        $('#navMenu').toggleClass('active');
        $(this).toggleClass('active');
    });
}

// ========================================
// LOGIN PAGE
// ========================================

function initLoginPage() {
    $('#loginForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        // Simulate login (replace with real API call later)
        if (email && password) {
            // Mock user data
            const user = {
                id: 1,
                name: email.split('@')[0],
                email: email,
                role: email.includes('admin') ? 'admin' : 'customer'
            };
            
            // Save to localStorage
            localStorage.setItem('token', 'mock-jwt-token-' + Date.now());
            localStorage.setItem('user', JSON.stringify(user));
            
            updateNavigation();
            alert('Login successful!');
            window.location.hash = user.role === 'admin' ? 'admin' : 'home';
        }
    });
}

// ========================================
// REGISTER PAGE
// ========================================

function initRegisterPage() {
    $('#registerForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const fullName = $('#fullName').val();
        const email = $('#email').val();
        const phone = $('#phone').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        
        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        
        if (password.length < 6) {
            alert('Password must be at least 6 characters!');
            return;
        }
        
        // Simulate registration (replace with real API call later)
        const user = {
            id: Math.floor(Math.random() * 1000),
            name: fullName,
            email: email,
            phone: phone,
            role: 'customer'
        };
        
        localStorage.setItem('token', 'mock-jwt-token-' + Date.now());
        localStorage.setItem('user', JSON.stringify(user));
        
        updateNavigation();
        alert('Registration successful!');
        window.location.hash = 'home';
    });
}

// ========================================
// MENU PAGE
// ========================================

function initMenuPage() {
    // Mock menu items (replace with API call later)
    const menuItems = [
        { id: 1, name: 'Margherita Pizza', category: 'Main Course', price: 12.99, emoji: '🍕', description: 'Classic Italian pizza with fresh mozzarella' },
        { id: 2, name: 'Caesar Salad', category: 'Appetizer', price: 8.99, emoji: '🥗', description: 'Crispy romaine with parmesan and croutons' },
        { id: 3, name: 'Tiramisu', category: 'Dessert', price: 6.99, emoji: '🍰', description: 'Traditional Italian coffee-flavored dessert' },
        { id: 4, name: 'Pasta Carbonara', category: 'Main Course', price: 14.99, emoji: '🍝', description: 'Creamy pasta with bacon and parmesan' },
        { id: 5, name: 'Bruschetta', category: 'Appetizer', price: 7.99, emoji: '🍞', description: 'Toasted bread with tomatoes and basil' },
        { id: 6, name: 'Gelato', category: 'Dessert', price: 5.99, emoji: '🍨', description: 'Italian ice cream in various flavors' }
    ];
    
    displayMenuItems(menuItems);
}

function displayMenuItems(items) {
    const menuGrid = $('#menuGrid');
    menuGrid.empty();
    
    items.forEach(item => {
        const html = `
            <div class="menu-item">
                <div class="menu-item-image">${item.emoji}</div>
                <div class="menu-item-content">
                    <h3 class="menu-item-title">${item.name}</h3>
                    <p class="menu-item-description">${item.description}</p>
                    <div class="menu-item-footer">
                        <span class="menu-item-price">$${item.price}</span>
                        <button class="btn btn-primary" onclick="alert('Order functionality coming in later milestones!')">Order Now</button>
                    </div>
                </div>
            </div>
        `;
        menuGrid.append(html);
    });
}

// ========================================
// ORDERS PAGE
// ========================================

function initOrdersPage() {
    // Mock orders (replace with API call later)
    const orders = [
        { id: 1, date: '2025-01-15', items: 'Pizza, Salad', total: 21.98, status: 'Delivered' },
        { id: 2, date: '2025-01-10', items: 'Pasta, Tiramisu', total: 21.98, status: 'Delivered' },
        { id: 3, date: '2025-01-05', items: 'Bruschetta, Gelato', total: 13.98, status: 'Completed' }
    ];
    
    const tbody = $('#ordersTable tbody');
    tbody.empty();
    
    orders.forEach(order => {
        const statusClass = order.status === 'Delivered' ? 'badge-success' : 'badge-primary';
        const html = `
            <tr>
                <td>#${order.id}</td>
                <td>${order.date}</td>
                <td>${order.items}</td>
                <td>$${order.total}</td>
                <td><span class="badge ${statusClass}">${order.status}</span></td>
            </tr>
        `;
        tbody.append(html);
    });
}

// ========================================
// PROFILE PAGE
// ========================================

function initProfilePage() {
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    
    $('#userName').text(user.name || 'N/A');
    $('#userEmail').text(user.email || 'N/A');
    $('#userRole').text(user.role || 'N/A');
}

// ========================================
// ADMIN PAGE
// ========================================

function initAdminPage() {
    // Mock statistics (replace with API call later)
    $('#totalOrders').text('156');
    $('#totalRevenue').text('$12,450');
    $('#totalUsers').text('89');
    $('#pendingReservations').text('12');
}

// ========================================
// RESERVATION PAGE
// ========================================

function initReservationPage() {
    $('#reservationForm').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            date: $('#date').val(),
            time: $('#time').val(),
            guests: $('#guests').val(),
            specialRequests: $('#specialRequests').val()
        };
        
        // Simulate reservation (replace with API call later)
        alert('Reservation confirmed for ' + formData.name + ' on ' + formData.date + ' at ' + formData.time);
        $('#reservationForm')[0].reset();
    });
}