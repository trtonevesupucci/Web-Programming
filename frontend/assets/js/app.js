// Mock Authentication State (will be replaced with real JWT in Milestone 4)
const Auth = {
    user: null,
    
    isLoggedIn() {
        // Check if user exists in sessionStorage
        const user = sessionStorage.getItem('user');
        if (user) {
            this.user = JSON.parse(user);
            return true;
        }
        return false;
    },
    
    login(userData) {
        this.user = userData;
        sessionStorage.setItem('user', JSON.stringify(userData));
        this.updateUI();
    },
    
    logout() {
        this.user = null;
        sessionStorage.removeItem('user');
        this.updateUI();
        router.navigate('/');
    },
    
    isAdmin() {
        return this.user && this.user.role === 'admin';
    },
    
    updateUI() {
        const loginLink = document.getElementById('loginLink');
        const registerLink = document.getElementById('registerLink');
        const profileLink = document.getElementById('profileLink');
        const logoutBtn = document.getElementById('logoutBtn');
        const adminLink = document.getElementById('adminLink');
        
        if (this.isLoggedIn()) {
            loginLink.style.display = 'none';
            registerLink.style.display = 'none';
            profileLink.style.display = 'block';
            logoutBtn.style.display = 'block';
            
            if (this.isAdmin()) {
                adminLink.style.display = 'block';
            }
        } else {
            loginLink.style.display = 'block';
            registerLink.style.display = 'block';
            profileLink.style.display = 'none';
            logoutBtn.style.display = 'none';
            adminLink.style.display = 'none';
        }
    }
};

// Define all routes
router.addRoute('/', async () => {
    await router.loadPage('home');
});

router.addRoute('/menu', async () => {
    await router.loadPage('menu');
    initMenuPage();
});

router.addRoute('/reservations', async () => {
    await router.loadPage('reservations');
    initReservationsPage();
});

router.addRoute('/orders', async () => {
    if (!Auth.isLoggedIn()) {
        router.navigate('/login');
        return;
    }
    await router.loadPage('orders');
    initOrdersPage();
});

router.addRoute('/login', async () => {
    if (Auth.isLoggedIn()) {
        router.navigate('/profile');
        return;
    }
    await router.loadPage('login');
    initLoginPage();
});

router.addRoute('/register', async () => {
    if (Auth.isLoggedIn()) {
        router.navigate('/profile');
        return;
    }
    await router.loadPage('register');
    initRegisterPage();
});

router.addRoute('/profile', async () => {
    if (!Auth.isLoggedIn()) {
        router.navigate('/login');
        return;
    }
    await router.loadPage('profile');
    initProfilePage();
});

router.addRoute('/admin', async () => {
    if (!Auth.isAdmin()) {
        router.navigate('/');
        return;
    }
    await router.loadPage('admin');
    initAdminPage();
});

router.addRoute('/404', async () => {
    await router.loadPage('404');
});

// Page Initialization Functions
function initMenuPage() {
    // Mock menu data - will be replaced with API calls in Milestone 4
    const mockMenuItems = [
        { id: 1, name: 'Margherita Pizza', category: 'Main Course', price: 12.99, description: 'Classic Italian pizza with fresh mozzarella' },
        { id: 2, name: 'Caesar Salad', category: 'Appetizer', price: 8.99, description: 'Crispy romaine with parmesan and croutons' },
        { id: 3, name: 'Tiramisu', category: 'Dessert', price: 6.99, description: 'Traditional Italian coffee-flavored dessert' },
        { id: 4, name: 'Grilled Salmon', category: 'Main Course', price: 18.99, description: 'Fresh Atlantic salmon with herbs' },
        { id: 5, name: 'Bruschetta', category: 'Appetizer', price: 7.99, description: 'Toasted bread with tomatoes and basil' },
        { id: 6, name: 'Chocolate Lava Cake', category: 'Dessert', price: 7.99, description: 'Warm chocolate cake with molten center' }
    ];
    
    const menuGrid = document.getElementById('menuGrid');
    if (menuGrid) {
        menuGrid.innerHTML = mockMenuItems.map(item => `
            <div class="menu-item">
                <div class="menu-item-image">🍽️</div>
                <div class="menu-item-content">
                    <h3 class="menu-item-title">${item.name}</h3>
                    <p class="menu-item-description">${item.description}</p>
                    <span class="badge badge-primary">${item.category}</span>
                    <div class="menu-item-footer">
                        <span class="menu-item-price">$${item.price}</span>
                        <button class="btn btn-primary" onclick="addToCart(${item.id})">Add to Cart</button>
                    </div>
                </div>
            </div>
        `).join('');
    }
}

function initReservationsPage() {
    const reservationForm = document.getElementById('reservationForm');
    if (reservationForm) {
        reservationForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(reservationForm);
            const data = Object.fromEntries(formData);
            
            // Mock submission - will be API call in Milestone 4
            console.log('Reservation data:', data);
            alert('Reservation submitted successfully! (Mock)');
            reservationForm.reset();
        });
    }
}

function initOrdersPage() {
    // Mock orders data
    const mockOrders = [
        { id: 1, date: '2025-10-05', items: 'Margherita Pizza, Caesar Salad', total: 21.98, status: 'Delivered' },
        { id: 2, date: '2025-10-03', items: 'Grilled Salmon, Tiramisu', total: 25.98, status: 'In Progress' },
        { id: 3, date: '2025-09-30', items: 'Bruschetta, Chocolate Lava Cake', total: 15.98, status: 'Delivered' }
    ];
    
    const ordersTable = document.getElementById('ordersTable');
    if (ordersTable) {
        const tbody = ordersTable.querySelector('tbody');
        tbody.innerHTML = mockOrders.map(order => `
            <tr>
                <td>#${order.id}</td>
                <td>${order.date}</td>
                <td>${order.items}</td>
                <td>$${order.total}</td>
                <td><span class="badge ${order.status === 'Delivered' ? 'badge-success' : 'badge-warning'}">${order.status}</span></td>
            </tr>
        `).join('');
    }
}

function initLoginPage() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Mock login - will be real API call with JWT in Milestone 4
            if (email && password) {
                const mockUser = {
                    id: 1,
                    email: email,
                    name: 'John Doe',
                    role: email.includes('admin') ? 'admin' : 'customer'
                };
                
                Auth.login(mockUser);
                alert('Login successful! (Mock)');
                router.navigate('/profile');
            }
        });
    }
}

function initRegisterPage() {
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(registerForm);
            const data = Object.fromEntries(formData);
            
            // Mock registration - will be API call in Milestone 4
            console.log('Registration data:', data);
            
            if (data.password !== data.confirmPassword) {
                alert('Passwords do not match!');
                return;
            }
            
            alert('Registration successful! Please login. (Mock)');
            router.navigate('/login');
        });
    }
}

function initProfilePage() {
    const user = Auth.user;
    if (user) {
        document.getElementById('userName').textContent = user.name;
        document.getElementById('userEmail').textContent = user.email;
        document.getElementById('userRole').textContent = user.role;
    }
}

function initAdminPage() {
    // Mock admin data
    const stats = {
        totalOrders: 156,
        totalRevenue: 3420.50,
        totalUsers: 89,
        pendingReservations: 12
    };
    
    document.getElementById('totalOrders').textContent = stats.totalOrders;
    document.getElementById('totalRevenue').textContent = `$${stats.totalRevenue}`;
    document.getElementById('totalUsers').textContent = stats.totalUsers;
    document.getElementById('pendingReservations').textContent = stats.pendingReservations;
}

// Global function for cart (will be implemented properly later)
function addToCart(itemId) {
    console.log('Adding item to cart:', itemId);
    alert('Item added to cart! (Mock functionality)');
}

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
    });
    
    // Close menu when clicking a link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
    
    // Logout button
    document.getElementById('logoutBtn').addEventListener('click', () => {
        Auth.logout();
    });
    
    // Initialize auth UI
    Auth.updateUI();
});