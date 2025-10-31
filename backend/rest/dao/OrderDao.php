<?php
require_once 'BaseDao.php';

/**
 * Order DAO
 * Handles database operations for orders table
 */
class OrderDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "orders";
        parent::__construct($this->table_name);
    }

    /**
     * Get orders by user ID
     * @param int $user_id
     * @return array
     */
    public function getOrdersByUser($user_id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get orders by status
     * @param string $status
     * @return array
     */
    public function getOrdersByStatus($status)
    {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE status = :status ORDER BY order_date DESC");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order with items and user details
     * @param int $order_id
     * @return array
     */
    public function getOrderWithDetails($order_id)
    {
        $query = "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone
                  FROM orders o
                  JOIN users u ON o.user_id = u.id
                  WHERE o.id = :order_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Update order status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        $stmt = $this->connection->prepare("UPDATE orders SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Get total revenue
     * @return float
     */
    public function getTotalRevenue()
    {
        $stmt = $this->connection->prepare("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'delivered'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['revenue'] ?? 0;
    }

    /**
     * Get orders count by date range
     * @param string $start_date
     * @param string $end_date
     * @return int
     */
    public function getOrdersCountByDateRange($start_date, $end_date)
    {
        $stmt = $this->connection->prepare("SELECT COUNT(*) as count FROM orders WHERE order_date BETWEEN :start_date AND :end_date");
        $stmt->bindParam(':start_date', $start_date);
        $stmt->bindParam(':end_date', $end_date);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>