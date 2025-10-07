// Simple SPA Router
class Router {
    constructor() {
        this.routes = {};
        this.currentRoute = null;
        this.init();
    }

    // Initialize router
    init() {
        // Listen for hash changes
        window.addEventListener('hashchange', () => this.handleRoute());
        
        // Handle initial load
        window.addEventListener('load', () => this.handleRoute());
        
        // Intercept all anchor clicks
        document.addEventListener('click', (e) => {
            if (e.target.tagName === 'A' && e.target.getAttribute('href')?.startsWith('#')) {
                e.preventDefault();
                const hash = e.target.getAttribute('href');
                window.location.hash = hash;
            }
        });
    }

    // Register a route
    addRoute(path, handler) {
        this.routes[path] = handler;
    }

    // Handle route changes
    async handleRoute() {
        // Get current hash or default to home
        const hash = window.location.hash.slice(1) || '/';
        const route = hash.split('?')[0]; // Remove query params
        
        this.currentRoute = route;

        // Find matching route
        const handler = this.routes[route] || this.routes['/404'];

        if (handler) {
            try {
                await handler();
                this.updateActiveNav();
            } catch (error) {
                console.error('Route error:', error);
                this.showError('Failed to load page');
            }
        } else {
            this.showError('Page not found');
        }
    }

    // Update active navigation link
    updateActiveNav() {
        const navLinks = document.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === `#${this.currentRoute}`) {
                link.style.fontWeight = 'bold';
                link.style.color = 'var(--primary-color)';
            } else {
                link.style.fontWeight = 'normal';
                link.style.color = '';
            }
        });
    }

    // Navigate programmatically
    navigate(path) {
        window.location.hash = path;
    }

    // Show error message
    showError(message) {
        const app = document.getElementById('app');
        app.innerHTML = `
            <div class="container">
                <div class="alert alert-danger">
                    <h3>Error</h3>
                    <p>${message}</p>
                    <button class="btn btn-primary" onclick="location.hash='/'">Go Home</button>
                </div>
            </div>
        `;
    }

    // Load HTML from pages folder
    async loadPage(pageName) {
        try {
            const response = await fetch(`pages/${pageName}.html`);
            if (!response.ok) {
                throw new Error(`Failed to load ${pageName}`);
            }
            const html = await response.text();
            document.getElementById('app').innerHTML = html;
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            return true;
        } catch (error) {
            console.error('Load page error:', error);
            this.showError(`Could not load page: ${pageName}`);
            return false;
        }
    }
}

// Create global router instance
const router = new Router();