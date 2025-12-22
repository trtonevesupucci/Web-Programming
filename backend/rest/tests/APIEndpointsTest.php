<?php

use PHPUnit\Framework\TestCase;

class APIEndpointsTest extends TestCase
{
    private static $baseUrl = 'http://localhost/RijadTrtic/Web-Programming/backend/rest/index.php';
    private static $adminToken = null;

    /**
     * Set up - get admin token
     */
    public static function setUpBeforeClass(): void
    {
        // Use existing admin account
        $loginData = json_encode([
            'email' => 'testadmin@test.com',
            'password' => 'password123'
        ]);

        $ch = curl_init(self::$baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        self::$adminToken = $result['data']['token'] ?? null;
    }

    /**
     * Test GET / root endpoint (public)
     */
    public function testRootEndpoint()
    {
        $ch = curl_init(self::$baseUrl . '/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'Root endpoint should return 200');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON');
        $this->assertArrayHasKey('message', $result, 'Response should include message');
    }

    /**
     * Test GET /categories (public)
     */
    public function testGetCategories()
    {
        $ch = curl_init(self::$baseUrl . '/categories');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'GET /categories should return 200');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON array');
    }

    /**
     * Test GET /menu-items (public)
     */
    public function testGetMenuItems()
    {
        $ch = curl_init(self::$baseUrl . '/menu-items');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'GET /menu-items should return 200');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON array');
    }

    /**
     * Test POST /categories with admin token (admin-only)
     */
    public function testPostCategory()
    {
        $categoryData = json_encode([
            'name' => 'API Test Category ' . time(),
            'description' => 'Category created by PHPUnit API test'
        ]);

        $ch = curl_init(self::$baseUrl . '/categories');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$adminToken
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $categoryData);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(201, $http_code, 'POST /categories should return 201 Created');
        
        $result = json_decode($response, true);
        $this->assertNotEmpty($result['id'] ?? null, 'Response should include category ID');
    }

    /**
     * Test GET /categories/{id}
     */
    public function testGetCategoryById()
    {
        $ch = curl_init(self::$baseUrl . '/categories/1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Either 200 (found) or 404 (not found) is valid
        $this->assertThat($http_code, $this->logicalOr(
            $this->equalTo(200),
            $this->equalTo(404)
        ), 'GET /categories/{id} should return 200 or 404');
    }

    /**
     * Test PUT /categories/{id} with admin token (admin-only)
     */
    public function testPutCategory()
    {
        $categoryData = json_encode([
            'name' => 'Updated Category ' . time(),
            'description' => 'Updated by PHPUnit'
        ]);

        $ch = curl_init(self::$baseUrl . '/categories/1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$adminToken
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $categoryData);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Either 200 (success) or 404 (not found) is valid
        $this->assertThat($http_code, $this->logicalOr(
            $this->equalTo(200),
            $this->equalTo(404)
        ), 'PUT /categories/{id} should return 200 or 404');
    }

    /**
     * Test GET /orders requires authentication
     */
    public function testOrdersRequireAuthentication()
    {
        $ch = curl_init(self::$baseUrl . '/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(401, $http_code, 'GET /orders without token should return 401');
    }

    /**
     * Test GET /reservations requires authentication
     */
    public function testReservationsRequireAuthentication()
    {
        $ch = curl_init(self::$baseUrl . '/reservations');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(401, $http_code, 'GET /reservations without token should return 401');
    }

    /**
     * Test 404 error for non-existent route
     */
    public function testNonexistentRoute()
    {
        $ch = curl_init(self::$baseUrl . '/nonexistent');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(404, $http_code, 'Non-existent route should return 404');
    }
}
?>
