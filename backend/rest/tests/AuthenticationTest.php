<?php

use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{
    private static $baseUrl = 'http://localhost/RijadTrtic/Web-Programming/backend/rest/index.php';
    private static $testEmail = null;
    private static $testPassword = 'password123';
    private static $testToken = null;

    /**
     * Setup before class
     */
    public static function setUpBeforeClass(): void
    {
        self::$testEmail = 'phpunit_test_' . time() . '@test.com';
    }

    /**
     * Test user registration endpoint
     */
    public function testUserRegistration()
    {
        $data = json_encode([
            'name' => 'PHPUnit Test User',
            'email' => self::$testEmail,
            'password' => self::$testPassword,
            'phone' => '5551234567'
        ]);

        $ch = curl_init(self::$baseUrl . '/auth/register');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(201, $http_code, 'Registration should return 201 Created');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON');
        $this->assertTrue($result['success'] ?? false, 'Registration should be successful');
        $this->assertEquals(self::$testEmail, $result['data']['email'], 'Email should match');
    }

    /**
     * Test user login endpoint
     */
    public function testUserLogin()
    {
        $data = json_encode([
            'email' => self::$testEmail,
            'password' => self::$testPassword
        ]);

        $ch = curl_init(self::$baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $http_code, 'Login should return 200 OK');
        
        $result = json_decode($response, true);
        $this->assertIsArray($result, 'Response should be JSON');
        $this->assertTrue($result['success'] ?? false, 'Login should be successful');
        $this->assertNotEmpty($result['data']['token'], 'Response should include JWT token');
        
        // Store token for authorization tests
        self::$testToken = $result['data']['token'];
    }

    /**
     * Test invalid login credentials
     */
    public function testInvalidLogin()
    {
        $data = json_encode([
            'email' => 'nonexistent@test.com',
            'password' => 'wrongpassword'
        ]);

        $ch = curl_init(self::$baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(500, $http_code, 'Invalid login should return 500');
        
        $result = json_decode($response, true);
        $this->assertArrayHasKey('error', $result, 'Response should include error message');
    }

    /**
     * Test missing authentication token
     */
    public function testMissingAuthenticationToken()
    {
        $ch = curl_init(self::$baseUrl . '/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, false);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Should return 403 (forbidden) because user lacks admin role when not authenticated
        $this->assertGreaterThanOrEqual(401, $http_code, 'Request without token should return 401+ error');
        
        $result = json_decode($response, true);
        $this->assertNotEmpty($result['error'] ?? '', 'Error response should have error message');
    }

    /**
     * Test invalid token
     */
    public function testInvalidToken()
    {
        $ch = curl_init(self::$baseUrl . '/users');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: invalid.jwt.token'
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Should return 401 or 403 (before hook returns 401, but route might return 403 if decoded somehow)
        $this->assertGreaterThanOrEqual(401, $http_code, 'Invalid token should return 401+ error');
    }

    /**
     * Get stored token for authorization tests
     */
    public static function getTestToken()
    {
        return self::$testToken;
    }

    /**
     * Get test email
     */
    public static function getTestEmail()
    {
        return self::$testEmail;
    }
}
?>
