<?php
require_once 'BaseDao.php';

/**
 * OrderItem DAO
 * Handles database operations for order_items table
 */
class OrderItemDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "order_items";
        parent::__construct($this->table_name);
    }

    /**
     * Get order items by order ID
     * @param int $order_id
     * @return array
     */
    public function getItemsByOrder($order_id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get order items with menu item details
     * @param int $order_id
     * @return array
     */
    public function getItemsWithMenuDetails($order_id)
    {
        $query = "SELECT oi.*, m.name as item_name, m.description as item_description
                  FROM order_items oi
                  JOIN menu_items m ON oi.menu_item_id = m.id
                  WHERE oi.order_id = :order_id";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get most ordered menu items
     * @param int $limit
     * @return array
     */
    public function getMostOrderedItems($limit = 10)
    {
        $query = "SELECT m.id, m.name, SUM(oi.quantity) as total_ordered
                  FROM order_items oi
                  JOIN menu_items m ON oi.menu_item_id = m.id
                  GROUP BY m.id
                  ORDER BY total_ordered DESC
                  LIMIT :limit";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Delete items by order ID
     * @param int $order_id
     * @return bool
     */
    public function deleteByOrderId($order_id)
    {
        $stmt = $this->connection->prepare("DELETE FROM order_items WHERE order_id = :order_id");
        $stmt->bindParam(':order_id', $order_id);
        return $stmt->execute();
    }
}
?>