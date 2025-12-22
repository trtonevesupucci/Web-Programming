<?php

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Authentication");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Fix URL parsing for subdirectory XAMPP installation
// Remove /RijadTrtic/Web-Programming/backend/rest/index.php from the request path
$base_path = '/RijadTrtic/Web-Programming/backend/rest/index.php';
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Strip the base path if it's in the request
if (strpos($request_path, $base_path) === 0) {
    $request_path = substr($request_path, strlen($base_path));
    if (empty($request_path)) {
        $request_path = '/';
    }
    $_SERVER['REQUEST_URI'] = $request_path;
}

// Load Config
require_once __DIR__ . '/config.php';

// Load Roles and Permissions
require_once __DIR__ . '/data/roles.php';

// Load Middleware
require_once __DIR__ . '/middleware/AuthMiddleware.php';

// Import JWT classes
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Create a global variable to hold the authenticated user (since Flight context doesn't persist)
$GLOBALS['authenticated_user'] = null;

// Helper function to get authenticated user from token
function getAuthenticatedUser() {
    try {
        // Get Authorization header using getallheaders() since Flight's getHeader might have issues
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        
        if (!$token) {
            error_log("DEBUG: No token in Authorization header");
            return null;
        }
        
        error_log("DEBUG: Token found, attempting to decode: " . substr($token, 0, 50) . "...");
        
        $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
        error_log("DEBUG: Token decoded successfully");
        error_log("DEBUG: User role: " . (is_object($decoded_token->user) ? $decoded_token->user->role : $decoded_token->user['role']));
        
        return $decoded_token->user;
    } catch (\Exception $e) {
        error_log("DEBUG: Token decode error: " . $e->getMessage());
        return null;
    }
}

// Load Auth Service (before other services)
require_once __DIR__ . '/services/AuthService.php';

// Load Other Services
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/MenuItemService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/services/OrderItemService.php';
require_once __DIR__ . '/services/ReservationService.php';

// Register Services with Flight
Flight::register('auth_service', 'AuthService');
Flight::register('userService', 'UserService');
Flight::register('categoryService', 'CategoryService');
Flight::register('menuItemService', 'MenuItemService');
Flight::register('orderService', 'OrderService');
Flight::register('orderItemService', 'OrderItemService');
Flight::register('reservationService', 'ReservationService');
Flight::register('auth_middleware', 'AuthMiddleware');

// Error handler
Flight::map('error', function(Exception $ex) {
    Flight::json([
        'error' => $ex->getMessage(),
        'code' => $ex->getCode() ?: 500
    ], $ex->getCode() ?: 500);
});

// 404 handler
Flight::map('notFound', function() {
    Flight::json([
        'error' => 'Route not found',
        'code' => 404
    ], 404);
});

// Debug: Log incoming requests (comment out in production)
// error_log("Request URL: " . Flight::request()->url);
// error_log("Request Method: " . Flight::request()->method);

// Global Authentication using before hook
Flight::before('route', function(&$route, &$params) {
    // Public routes that don't require authentication
    $public_routes = [
        '/auth/login',
        '/auth/register',
        '/'
    ];

    $request_url = Flight::request()->url;
    $is_public = false;

    // Check if current route is public
    foreach ($public_routes as $route_path) {
        if (strpos($request_url, $route_path) === 0) {
            $is_public = true;
            break;
        }
    }

    // If route is public, skip authentication
    if ($is_public) {
        return;
    }

    // For protected routes, verify JWT token exists
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? null;
    if (!$token) {
        Flight::halt(401, json_encode(['error' => 'Missing authentication token']));
    }

    try {
        // Just verify the token is valid
        JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
    } catch (\Exception $e) {
        Flight::halt(401, json_encode(['error' => 'Invalid token: ' . $e->getMessage()]));
    }
});

// Test route (public)
Flight::route('GET /', function() {
    Flight::json([
        'message' => 'Restaurant API - JWT Protected with RBAC',
        'version' => '1.0',
        'auth_endpoints' => [
            'POST /auth/register - Register new user',
            'POST /auth/login - Login and get JWT token'
        ],
        'protected_endpoints' => [
            'GET /users - Get all users (admin only)',
            'GET /categories - Get all categories',
            'GET /menu-items - Get all menu items',
            'GET /orders - Get user orders',
            'GET /reservations - Get user reservations'
        ]
    ]);
});

// Debug endpoint - remove in production
Flight::route('GET /debug/user', function() {
    $user = getAuthenticatedUser();
    
    // Debug: check what headers are available
    $all_headers = getallheaders();
    
    Flight::json([
        'debug' => true,
        'all_headers' => $all_headers,
        'authentication_header' => Flight::request()->getHeader("Authentication"),
        'auth_header_lowercase' => Flight::request()->getHeader("authentication"),
        'user_set' => $user !== null,
        'user_value' => $user,
        'user_type' => gettype($user),
        'user_role' => $user ? (is_object($user) ? $user->role : $user['role']) : 'N/A'
    ]);
});

// Load Auth Routes (public - no JWT required)
require_once __DIR__ . '/routes/AuthRoutes.php';

// Load Protected Routes
require_once __DIR__ . '/routes/UserRoutes.php';
require_once __DIR__ . '/routes/CategoryRoutes.php';
require_once __DIR__ . '/routes/MenuItemRoutes.php';
require_once __DIR__ . '/routes/OrderRoutes.php';
require_once __DIR__ . '/routes/ReservationRoutes.php';

// Start the application
Flight::start();

?>


