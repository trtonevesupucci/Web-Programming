<?php
require_once __DIR__ . '/../dao/OrderItemDao.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/MenuItemDao.php';

/**
 * OrderItem Service
 * Business logic for order item operations
 */
class OrderItemService
{
    private $orderItemDao;
    private $orderDao;
    private $menuItemDao;

    public function __construct()
    {
        $this->orderItemDao = new OrderItemDao();
        $this->orderDao = new OrderDao();
        $this->menuItemDao = new MenuItemDao();
    }

    /**
     * Get all order items
     * @return array
     */
    public function getAll()
    {
        return $this->orderItemDao->getAll();
    }

    /**
     * Get order item by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order item ID");
        }
        return $this->orderItemDao->getById($id);
    }

    /**
     * Get order items by order ID
     * @param int $order_id
     * @return array
     */
    public function getItemsByOrder($order_id)
    {
        if (!is_numeric($order_id) || $order_id <= 0) {
            throw new Exception("Invalid order ID");
        }
        return $this->orderItemDao->getItemsByOrder($order_id);
    }

    /**
     * Get order items with menu details
     * @param int $order_id
     * @return array
     */
    public function getItemsWithMenuDetails($order_id)
    {
        if (!is_numeric($order_id) || $order_id <= 0) {
            throw new Exception("Invalid order ID");
        }
        return $this->orderItemDao->getItemsWithMenuDetails($order_id);
    }

    /**
     * Get most ordered menu items
     * @param int $limit
     * @return array
     */
    public function getMostOrderedItems($limit = 10)
    {
        if (!is_numeric($limit) || $limit <= 0) {
            throw new Exception("Limit must be a positive number");
        }
        return $this->orderItemDao->getMostOrderedItems($limit);
    }

    /**
     * Add new order item
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['order_id']) || !is_numeric($data['order_id'])) {
            throw new Exception("Valid order ID is required");
        }
        if (empty($data['menu_item_id']) || !is_numeric($data['menu_item_id'])) {
            throw new Exception("Valid menu item ID is required");
        }
        if (empty($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            throw new Exception("Valid quantity is required");
        }
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception("Valid price is required");
        }

        // Check if order exists
        $order = $this->orderDao->getById($data['order_id']);
        if (!$order) {
            throw new Exception("Order not found");
        }

        // Check if menu item exists
        $menuItem = $this->menuItemDao->getById($data['menu_item_id']);
        if (!$menuItem) {
            throw new Exception("Menu item not found");
        }

        // Check if menu item is available
        if (!$menuItem['is_available']) {
            throw new Exception("Menu item is not available");
        }

        return $this->orderItemDao->add($data);
    }

    /**
     * Update order item
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order item ID");
        }

        // Check if order item exists
        $existingItem = $this->orderItemDao->getById($id);
        if (!$existingItem) {
            throw new Exception("Order item not found");
        }

        // Validate quantity if provided
        if (isset($data['quantity']) && (!is_numeric($data['quantity']) || $data['quantity'] <= 0)) {
            throw new Exception("Quantity must be a positive number");
        }

        // Validate price if provided
        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] <= 0)) {
            throw new Exception("Price must be a positive number");
        }

        return $this->orderItemDao->update($data, $id);
    }

    /**
     * Delete order item
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order item ID");
        }

        // Check if order item exists
        $item = $this->orderItemDao->getById($id);
        if (!$item) {
            throw new Exception("Order item not found");
        }

        return $this->orderItemDao->delete($id);
    }

    /**
     * Delete all items by order ID
     * @param int $order_id
     * @return bool
     */
    public function deleteByOrderId($order_id)
    {
        if (!is_numeric($order_id) || $order_id <= 0) {
            throw new Exception("Invalid order ID");
        }

        return $this->orderItemDao->deleteByOrderId($order_id);
    }
}
?>