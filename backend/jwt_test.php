<?php
/**
 * JWT Authentication Test Script
 * Tests /auth/register and /auth/login endpoints
 */

echo "=== Restaurant API - JWT Authentication Test ===\n\n";

$api_url = 'http://localhost/RijadTrtic/Web-Programming/backend/rest';

// Test 1: Register a new user
echo "Test 1: Register a new user\n";
echo "POST $api_url/auth/register\n";

$register_data = [
    'name' => 'Test User ' . date('His'),
    'email' => 'testuser' . date('His') . '@example.com',
    'password' => 'TestPassword123',
    'phone' => '+1234567890',
    'role' => 'customer'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$api_url/auth/register");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($register_data));

$register_response = curl_exec($ch);
$register_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $register_code\n";
echo "Response: " . json_encode(json_decode($register_response), JSON_PRETTY_PRINT) . "\n\n";

$registered_user = json_decode($register_response, true);
$test_email = $register_data['email'];
$test_password = $register_data['password'];

// Test 2: Login with the registered user
echo "Test 2: Login with registered user\n";
echo "POST $api_url/auth/login\n";

$login_data = [
    'email' => $test_email,
    'password' => $test_password
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$api_url/auth/login");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($login_data));

$login_response = curl_exec($ch);
$login_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $login_code\n";
echo "Response: " . json_encode(json_decode($login_response), JSON_PRETTY_PRINT) . "\n\n";

$login_result = json_decode($login_response, true);
$jwt_token = $login_result['data']['token'] ?? null;

// Test 3: Access protected route with JWT token
if ($jwt_token) {
    echo "Test 3: Access protected route with JWT token\n";
    echo "GET $api_url/categories\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$api_url/categories");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authentication: $jwt_token"
    ]);

    $protected_response = curl_exec($ch);
    $protected_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "Status Code: $protected_code\n";
    echo "First 500 chars: " . substr($protected_response, 0, 500) . "...\n\n";
} else {
    echo "Test 3: Could not test protected route - no JWT token obtained\n\n";
}

// Test 4: Try to access protected route without token
echo "Test 4: Try to access protected route WITHOUT token (should fail)\n";
echo "GET $api_url/categories\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$api_url/categories");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$no_token_response = curl_exec($ch);
$no_token_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status Code: $no_token_code (should be 401)\n";
echo "Response: " . json_encode(json_decode($no_token_response), JSON_PRETTY_PRINT) . "\n\n";

echo "=== Test Complete ===\n";
?>
