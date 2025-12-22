<?php
/**
 * Authorization Test Script
 * Tests RBAC implementation across all routes
 */

echo "=== AUTHORIZATION TEST SUITE ===\n\n";

$api_base = "http://localhost:8000";

// Test 1: Register as admin
echo "1. Registering admin user...\n";
$admin_data = json_encode([
    'name' => 'Admin User',
    'email' => 'admin@restaurant.com',
    'password' => 'admin123',
    'phone' => '555-1234'
]);

$ch = curl_init("$api_base/auth/register");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $admin_data);
$result = curl_exec($ch);
$admin_response = json_decode($result, true);
echo "Response: " . json_encode($admin_response) . "\n\n";

// Test 2: Register as customer
echo "2. Registering customer user...\n";
$customer_data = json_encode([
    'name' => 'Customer User',
    'email' => 'customer@restaurant.com',
    'password' => 'customer123',
    'phone' => '555-5678'
]);

$ch = curl_init("$api_base/auth/register");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $customer_data);
$result = curl_exec($ch);
$customer_response = json_decode($result, true);
echo "Response: " . json_encode($customer_response) . "\n\n";

// Test 3: Login as admin
echo "3. Logging in as admin...\n";
$login_data = json_encode([
    'email' => 'admin@restaurant.com',
    'password' => 'admin123'
]);

$ch = curl_init("$api_base/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
$result = curl_exec($ch);
$admin_login = json_decode($result, true);
$admin_token = $admin_login['token'] ?? null;
echo "Token: " . substr($admin_token, 0, 50) . "...\n";
echo "User role: " . $admin_login['user']['role'] . "\n\n";

// Test 4: Login as customer
echo "4. Logging in as customer...\n";
$login_data = json_encode([
    'email' => 'customer@restaurant.com',
    'password' => 'customer123'
]);

$ch = curl_init("$api_base/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $login_data);
$result = curl_exec($ch);
$customer_login = json_decode($result, true);
$customer_token = $customer_login['token'] ?? null;
echo "Token: " . substr($customer_token, 0, 50) . "...\n";
echo "User role: " . $customer_login['user']['role'] . "\n\n";

// Test 5: Admin POST to /categories (should succeed - 201)
echo "5. Admin creating category (expect 201)...\n";
$category_data = json_encode([
    'name' => 'Appetizers',
    'description' => 'Starter dishes'
]);

$ch = curl_init("$api_base/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $admin_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $category_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 6: Customer POST to /categories (should fail - 403)
echo "6. Customer creating category (expect 403)...\n";
$ch = curl_init("$api_base/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $customer_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $category_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 7: Admin POST to /menu-items (should succeed - 201)
echo "7. Admin creating menu item (expect 201)...\n";
$menu_item_data = json_encode([
    'name' => 'Caesar Salad',
    'category_id' => 1,
    'description' => 'Fresh romaine with parmesan',
    'price' => 8.99,
    'is_available' => true
]);

$ch = curl_init("$api_base/menu-items");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $admin_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $menu_item_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 8: Customer POST to /menu-items (should fail - 403)
echo "8. Customer creating menu item (expect 403)...\n";
$ch = curl_init("$api_base/menu-items");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $customer_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $menu_item_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 9: Admin POST to /orders (should succeed - 201)
echo "9. Admin creating order (expect 201)...\n";
$order_data = json_encode([
    'user_id' => 2,
    'total_price' => 25.50,
    'status' => 'pending'
]);

$ch = curl_init("$api_base/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $admin_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 10: Customer POST to /orders (should fail - 403)
echo "10. Customer creating order (expect 403)...\n";
$ch = curl_init("$api_base/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $customer_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $order_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 11: Admin POST to /reservations (should succeed - 201)
echo "11. Admin creating reservation (expect 201)...\n";
$reservation_data = json_encode([
    'user_id' => 2,
    'reservation_date' => '2025-12-25 19:00:00',
    'num_guests' => 4,
    'status' => 'pending'
]);

$ch = curl_init("$api_base/reservations");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $admin_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $reservation_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 12: Customer POST to /reservations (should fail - 403)
echo "12. Customer creating reservation (expect 403)...\n";
$ch = curl_init("$api_base/reservations");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authentication: ' . $customer_token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $reservation_data);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

// Test 13: Customer GET /orders/user/{id} (should succeed - 200)
echo "13. Customer getting own orders (expect 200)...\n";
$customer_id = $customer_login['user']['id'] ?? 2;
$ch = curl_init("$api_base/orders/user/$customer_id");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authentication: ' . $customer_token
]);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP Status: $http_code\n";
echo "Response: " . json_encode(json_decode($result, true)) . "\n\n";

echo "=== AUTHORIZATION TEST COMPLETE ===\n";
?>
