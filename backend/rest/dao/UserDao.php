<?php
require_once 'BaseDao.php';

/**
 * User DAO
 * Handles database operations for users table
 */
class UserDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "users";
        parent::__construct($this->table_name);
    }

    /**
     * Get user by email
     * @param string $email
     * @return array|false
     */
    public function getUserByEmail($email)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get users by role
     * @param string $role - customer, admin, or staff
     * @return array
     */
    public function getUsersByRole($role)
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE role = :role");
        $stmt->bindParam(':role', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email)
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }

    /**
     * Update user password
     * @param int $id
     * @param string $password - hashed password
     * @return bool
     */
    public function updatePassword($id, $password)
    {
        $stmt = $this->connection->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>