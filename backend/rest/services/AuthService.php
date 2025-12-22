<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Auth Service
 * Business logic for authentication operations
 */
class AuthService extends BaseService
{
    private $auth_dao;

    public function __construct()
    {
        $this->auth_dao = new AuthDao();
        parent::__construct(new AuthDao());
    }

    /**
     * Get user by email
     * @param string $email
     * @return array|false
     */
    public function get_user_by_email($email)
    {
        return $this->auth_dao->get_user_by_email($email);
    }

    /**
     * Register a new user
     * @param array $entity
     * @return array
     */
    public function register($entity)
    {
        if (empty($entity['email']) || empty($entity['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // Check if email already exists
        $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
        if ($email_exists) {
            return ['success' => false, 'error' => 'Email already registered.'];
        }

        // Set default role if not provided
        if (empty($entity['role'])) {
            $entity['role'] = 'customer'; // default role
        }

        // Hash the password
        $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);

        // Add user to database
        $entity = parent::add($entity);

        // Remove password from response
        unset($entity['password']);

        return ['success' => true, 'data' => $entity];
    }

    /**
     * Login user and generate JWT token
     * @param array $entity
     * @return array
     */
    public function login($entity)
    {
        if (empty($entity['email']) || empty($entity['password'])) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // Get user by email
        $user = $this->auth_dao->get_user_by_email($entity['email']);
        
        if (!$user || !password_verify($entity['password'], $user['password'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        // Remove password from response
        unset($user['password']);

        // Create JWT payload
        $jwt_payload = [
            'user' => $user,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // valid for 24 hours
        ];

        // Encode JWT token
        $token = JWT::encode(
            $jwt_payload,
            Config::JWT_SECRET(),
            'HS256'
        );

        return ['success' => true, 'data' => array_merge($user, ['token' => $token])];
    }
}
?>
