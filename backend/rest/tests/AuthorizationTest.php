<?php

use PHPUnit\Framework\TestCase;

class AuthorizationTest extends TestCase
{
    private static $baseUrl = 'http://localhost/RijadTrtic/Web-Programming/backend/rest/index.php';
    private static $adminToken = null;
    private static $customerToken = null;

    /**
     * Set up test users - get tokens from existing test accounts
     */
    public static function setUpBeforeClass(): void
    {
        // Login admin user (existing in database)
        $ch = curl_init(self::$baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'testadmin@test.com',
            'password' => 'password123'
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        self::$adminToken = $result['data']['token'] ?? null;

        // Login customer user (existing in database)
        $ch = curl_init(self::$baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'email' => 'testcustomer@test.com',
            'password' => 'password123'
        ]));
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        self::$customerToken = $result['data']['token'] ?? null;
    }

    /**
     * Test admin can GET /users
     */
    public function testAdminCanGetUsers()
    {
        $ch = curl_init(self::$baseUrl . '/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$adminToken
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'Admin should be able to GET /users');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON array');
    }

    /**
     * Test customer CANNOT GET /users (403 Forbidden)
     */
    public function testCustomerCannotGetUsers()
    {
        $ch = curl_init(self::$baseUrl . '/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$customerToken
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(403, $http_code, 'Customer should NOT be able to GET /users');
        
        $result = json_decode($response, true);
        $this->assertStringContainsString('Access denied', $result['error'] ?? '', 'Should return access denied error');
    }

    /**
     * Test admin can POST /categories
     */
    public function testAdminCanCreateCategory()
    {
        $categoryData = json_encode([
            'name' => 'PHPUnit Test Category ' . time(),
            'description' => 'Test category for PHPUnit'
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

        $this->assertEquals(201, $http_code, 'Admin should be able to POST /categories');
        
        $result = json_decode($response, true);
        $this->assertNotEmpty($result['id'] ?? null, 'Response should include created category ID');
    }

    /**
     * Test customer CANNOT POST /categories (403 Forbidden)
     */
    public function testCustomerCannotCreateCategory()
    {
        $categoryData = json_encode([
            'name' => 'Unauthorized Category',
            'description' => 'Should not be created'
        ]);

        $ch = curl_init(self::$baseUrl . '/categories');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$customerToken
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $categoryData);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(403, $http_code, 'Customer should NOT be able to POST /categories');
    }

    /**
     * Test customer CAN GET /categories (public endpoint)
     */
    public function testCustomerCanGetCategories()
    {
        $ch = curl_init(self::$baseUrl . '/categories');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$customerToken
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'Customer should be able to GET /categories');
    }

    /**
     * Test admin can DELETE /menu-items/{id}
     */
    public function testAdminCanDeleteMenuItem()
    {
        $ch = curl_init(self::$baseUrl . '/menu-items/1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$adminToken
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Either 200 (success) or 404 (not found) is acceptable
        $this->assertThat($http_code, $this->logicalOr(
            $this->equalTo(200),
            $this->equalTo(404)
        ), 'Admin should be able to attempt DELETE /menu-items/{id}');
    }

    /**
     * Test customer CANNOT DELETE /menu-items/{id}
     */
    public function testCustomerCannotDeleteMenuItem()
    {
        $ch = curl_init(self::$baseUrl . '/menu-items/1');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . self::$customerToken
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(403, $http_code, 'Customer should NOT be able to DELETE /menu-items/{id}');
    }
}
?>
