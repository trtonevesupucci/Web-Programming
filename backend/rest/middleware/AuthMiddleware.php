<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Authentication and Authorization Middleware
 * Handles JWT verification and role-based access control
 */
class AuthMiddleware
{
    /**
     * Verify JWT token from request header
     * @param string $token
     * @return bool
     */
    public function verifyToken($token)
    {
        if (!$token) {
            Flight::halt(401, json_encode(['error' => 'Missing authentication token']));
        }

        try {
            $decoded_token = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded_token->user);
            Flight::set('jwt_token', $token);
            return TRUE;
        } catch (\Exception $e) {
            Flight::halt(401, json_encode(['error' => 'Invalid token: ' . $e->getMessage()]));
        }
    }

    /**
     * Check if user has a specific role
     * @param string $requiredRole
     * @return bool
     */
    public function authorizeRole($requiredRole)
    {
        $user = Flight::get('user');
        
        // Convert object to array if needed
        if (is_object($user)) {
            $user_role = $user->role ?? null;
        } else {
            $user_role = $user['role'] ?? null;
        }
        
        if (!$user || $user_role !== $requiredRole) {
            Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
        }
        
        return TRUE;
    }

    /**
     * Check if user has one of multiple allowed roles
     * @param array $roles
     * @return bool
     */
    public function authorizeRoles($roles)
    {
        $user = Flight::get('user');
        
        if (!$user || !in_array($user->role, $roles)) {
            Flight::halt(403, json_encode(['error' => 'Forbidden: role not allowed']));
        }
        
        return TRUE;
    }

    /**
     * Check if user has a specific permission
     * @param string $permission
     * @return bool
     */
    public function authorizePermission($permission)
    {
        $user = Flight::get('user');
        
        if (!$user || !isset($user->permissions) || !in_array($permission, $user->permissions)) {
            Flight::halt(403, json_encode(['error' => 'Access denied: permission missing']));
        }
        
        return TRUE;
    }

    /**
     * Get current authenticated user
     * @return object
     */
    public function getUser()
    {
        return Flight::get('user');
    }

    /**
     * Check if current user is admin
     * @return bool
     */
    public function isAdmin()
    {
        $user = Flight::get('user');
        return $user && $user->role === 'admin';
    }

    /**
     * Check if current user is customer/user
     * @return bool
     */
    public function isCustomer()
    {
        $user = Flight::get('user');
        return $user && $user->role === 'customer';
    }
}
?>
