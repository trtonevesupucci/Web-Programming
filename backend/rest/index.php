<?php

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS Headers - Allow frontend to communicate with API
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load Composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Load Config
require_once __DIR__ . '/config.php';

// Load Services
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CategoryService.php';
require_once __DIR__ . '/services/MenuItemService.php';
require_once __DIR__ . '/services/OrderService.php';
require_once __DIR__ . '/services/OrderItemService.php';
require_once __DIR__ . '/services/ReservationService.php';

// Initialize Flight
Flight::set('flight.log_errors', true);

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

/**
 * ========================================
 * USER ROUTES
 * ========================================
 */

// GET /users - Get all users
Flight::route('GET /users', function() {
    $userService = new UserService();
    $users = $userService->getAll();
    Flight::json($users);
});

// GET /users/email/@email - Get user by email (MUST be before /users/@id)
Flight::route('GET /users/email/@email', function($email) {
    $userService = new UserService();
    $user = $userService->getUserByEmail($email);
    if ($user) {
        Flight::json($user);
    } else {
        Flight::json(['error' => 'User not found'], 404);
    }
});

// GET /users/@id - Get user by ID
Flight::route('GET /users/@id', function($id) {
    $userService = new UserService();
    $user = $userService->getById($id);
    if ($user) {
        Flight::json($user);
    } else {
        Flight::json(['error' => 'User not found'], 404);
    }
});

// POST /users - Create new user
Flight::route('POST /users', function() {
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    $user = $userService->add($data);
    Flight::json($user, 201);
});

// PUT /users/@id - Update user
Flight::route('PUT /users/@id', function($id) {
    $data = Flight::request()->data->getData();
    $userService = new UserService();
    $user = $userService->update($id, $data);
    Flight::json($user);
});

// DELETE /users/@id - Delete user
Flight::route('DELETE /users/@id', function($id) {
    $userService = new UserService();
    $result = $userService->delete($id);
    Flight::json(['success' => $result, 'message' => 'User deleted']);
});

/**
 * ========================================
 * CATEGORY ROUTES
 * ========================================
 */

// GET /categories - Get all categories
Flight::route('GET /categories', function() {
    $categoryService = new CategoryService();
    $categories = $categoryService->getAll();
    Flight::json($categories);
});

// GET /categories/@id - Get category by ID
Flight::route('GET /categories/@id', function($id) {
    $categoryService = new CategoryService();
    $category = $categoryService->getById($id);
    if ($category) {
        Flight::json($category);
    } else {
        Flight::json(['error' => 'Category not found'], 404);
    }
});

// POST /categories - Create new category
Flight::route('POST /categories', function() {
    $data = Flight::request()->data->getData();
    $categoryService = new CategoryService();
    $category = $categoryService->add($data);
    Flight::json($category, 201);
});

// PUT /categories/@id - Update category
Flight::route('PUT /categories/@id', function($id) {
    $data = Flight::request()->data->getData();
    $categoryService = new CategoryService();
    $category = $categoryService->update($id, $data);
    Flight::json($category);
});

// DELETE /categories/@id - Delete category
Flight::route('DELETE /categories/@id', function($id) {
    $categoryService = new CategoryService();
    $result = $categoryService->delete($id);
    Flight::json(['success' => $result, 'message' => 'Category deleted']);
});

/**
 * ========================================
 * MENU ITEM ROUTES
 * ========================================
 */

// GET /menu-items - Get all menu items
Flight::route('GET /menu-items', function() {
    $menuItemService = new MenuItemService();
    $items = $menuItemService->getAll();
    Flight::json($items);
});

// GET /menu-items/@id - Get menu item by ID
Flight::route('GET /menu-items/@id', function($id) {
    $menuItemService = new MenuItemService();
    $item = $menuItemService->getById($id);
    if ($item) {
        Flight::json($item);
    } else {
        Flight::json(['error' => 'Menu item not found'], 404);
    }
});

// GET /menu-items/category/@category_id - Get items by category
Flight::route('GET /menu-items/category/@category_id', function($category_id) {
    $menuItemService = new MenuItemService();
    $items = $menuItemService->getByCategory($category_id);
    Flight::json($items);
});

// GET /menu-items/available - Get available menu items
Flight::route('GET /menu-items/available', function() {
    $menuItemService = new MenuItemService();
    $items = $menuItemService->getAvailableItems();
    Flight::json($items);
});

// POST /menu-items - Create new menu item
Flight::route('POST /menu-items', function() {
    $data = Flight::request()->data->getData();
    $menuItemService = new MenuItemService();
    $item = $menuItemService->add($data);
    Flight::json($item, 201);
});

// PUT /menu-items/@id - Update menu item
Flight::route('PUT /menu-items/@id', function($id) {
    $data = Flight::request()->data->getData();
    $menuItemService = new MenuItemService();
    $item = $menuItemService->update($id, $data);
    Flight::json($item);
});

// DELETE /menu-items/@id - Delete menu item
Flight::route('DELETE /menu-items/@id', function($id) {
    $menuItemService = new MenuItemService();
    $result = $menuItemService->delete($id);
    Flight::json(['success' => $result, 'message' => 'Menu item deleted']);
});

/**
 * ========================================
 * ORDER ROUTES
 * ========================================
 */

// GET /orders - Get all orders
Flight::route('GET /orders', function() {
    $orderService = new OrderService();
    $orders = $orderService->getAll();
    Flight::json($orders);
});

// GET /orders/@id - Get order by ID
Flight::route('GET /orders/@id', function($id) {
    $orderService = new OrderService();
    $order = $orderService->getById($id);
    if ($order) {
        Flight::json($order);
    } else {
        Flight::json(['error' => 'Order not found'], 404);
    }
});

// GET /orders/user/@user_id - Get orders by user
Flight::route('GET /orders/user/@user_id', function($user_id) {
    $orderService = new OrderService();
    $orders = $orderService->getOrdersByUser($user_id);
    Flight::json($orders);
});

// POST /orders - Create new order
Flight::route('POST /orders', function() {
    $data = Flight::request()->data->getData();
    $orderService = new OrderService();
    $order = $orderService->add($data);
    Flight::json($order, 201);
});

// PUT /orders/@id - Update order
Flight::route('PUT /orders/@id', function($id) {
    $data = Flight::request()->data->getData();
    $orderService = new OrderService();
    $order = $orderService->update($id, $data);
    Flight::json($order);
});

// PUT /orders/@id/status - Update order status
Flight::route('PUT /orders/@id/status', function($id) {
    $data = Flight::request()->data->getData();
    $orderService = new OrderService();
    $order = $orderService->updateStatus($id, $data['status']);
    Flight::json($order);
});

// DELETE /orders/@id - Delete order
Flight::route('DELETE /orders/@id', function($id) {
    $orderService = new OrderService();
    $result = $orderService->delete($id);
    Flight::json(['success' => $result, 'message' => 'Order deleted']);
});

/**
 * ========================================
 * RESERVATION ROUTES
 * ========================================
 */

// GET /reservations - Get all reservations
Flight::route('GET /reservations', function() {
    $reservationService = new ReservationService();
    $reservations = $reservationService->getAll();
    Flight::json($reservations);
});

// GET /reservations/@id - Get reservation by ID
Flight::route('GET /reservations/@id', function($id) {
    $reservationService = new ReservationService();
    $reservation = $reservationService->getById($id);
    if ($reservation) {
        Flight::json($reservation);
    } else {
        Flight::json(['error' => 'Reservation not found'], 404);
    }
});

// GET /reservations/user/@user_id - Get reservations by user
Flight::route('GET /reservations/user/@user_id', function($user_id) {
    $reservationService = new ReservationService();
    $reservations = $reservationService->getReservationsByUser($user_id);
    Flight::json($reservations);
});

// POST /reservations - Create new reservation
Flight::route('POST /reservations', function() {
    $data = Flight::request()->data->getData();
    $reservationService = new ReservationService();
    $reservation = $reservationService->add($data);
    Flight::json($reservation, 201);
});

// PUT /reservations/@id - Update reservation
Flight::route('PUT /reservations/@id', function($id) {
    $data = Flight::request()->data->getData();
    $reservationService = new ReservationService();
    $reservation = $reservationService->update($id, $data);
    Flight::json($reservation);
});

// PUT /reservations/@id/status - Update reservation status
Flight::route('PUT /reservations/@id/status', function($id) {
    $data = Flight::request()->data->getData();
    $reservationService = new ReservationService();
    $reservation = $reservationService->updateStatus($id, $data['status']);
    Flight::json($reservation);
});

// DELETE /reservations/@id - Delete reservation
Flight::route('DELETE /reservations/@id', function($id) {
    $reservationService = new ReservationService();
    $result = $reservationService->delete($id);
    Flight::json(['success' => $result, 'message' => 'Reservation deleted']);
});

/**
 * ========================================
 * TEST ROUTE
 * ========================================
 */

// GET / - Test route
Flight::route('GET /', function() {
    Flight::json([
        'message' => 'Restaurant API',
        'version' => '1.0',
        'endpoints' => [
            'GET /users',
            'GET /categories',
            'GET /menu-items',
            'GET /orders',
            'GET /reservations'
        ]
    ]);
});

// Start the application
Flight::start();
?>