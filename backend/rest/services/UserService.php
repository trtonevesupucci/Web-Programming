<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';

/**
 * User Service
 * Business logic for user operations
 */
class UserService extends BaseService
{
    public function __construct()
    {
        $dao = new UserDao();
        parent::__construct($dao);
    }

    /**
     * Get user by email
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        return $this->dao->getUserByEmail($email);
    }

    /**
     * Get users by role
     * @param string $role
     * @return array
     */
    public function getUsersByRole($role)
    {
        $validRoles = ['customer', 'admin', 'staff'];
        if (!in_array($role, $validRoles)) {
            throw new Exception("Invalid role. Must be: customer, admin, or staff");
        }
        return $this->dao->getUsersByRole($role);
    }

    /**
     * Add new user (with validation)
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['name'])) {
            throw new Exception("Name is required");
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Valid email is required");
        }
        if (empty($data['password'])) {
            throw new Exception("Password is required");
        }
        if (strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters");
        }

        // Check if email already exists
        if ($this->dao->emailExists($data['email'])) {
            throw new Exception("Email already exists");
        }

        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Set default role if not provided
        if (empty($data['role'])) {
            $data['role'] = 'customer';
        }

        return parent::add($data);
    }

    /**
     * Update user (with validation)
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        // Check if user exists
        $existingUser = $this->dao->getById($id);
        if (!$existingUser) {
            throw new Exception("User not found");
        }

        // Validate email if provided
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Hash password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                throw new Exception("Password must be at least 6 characters");
            }
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        return parent::update($id, $data);
    }

    /**
     * Authenticate user (login)
     * @param string $email
     * @param string $password
     * @return array|false
     */
    public function authenticate($email, $password)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        $user = $this->dao->getUserByEmail($email);
        
        if (!$user) {
            throw new Exception("Invalid credentials");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        // Remove password from response
        unset($user['password']);

        return $user;
    }
}
?>