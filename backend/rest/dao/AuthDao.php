<?php
require_once __DIR__ . '/BaseDao.php';

/**
 * Auth DAO
 * Data access for authentication operations
 */
class AuthDao extends BaseDao
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
    public function get_user_by_email($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
        return $this->query_unique($query, ['email' => $email]);
    }
}
?>
