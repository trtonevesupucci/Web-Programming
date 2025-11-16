<?php
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

/**
 * Order Service
 * Business logic for order operations
 */
class OrderService
{
    private $orderDao;
    private $userDao;

    public function __construct()
    {
        $this->orderDao = new OrderDao();
        $this->userDao = new UserDao();
    }

    /**
     * Get all orders
     * @return array
     */
    public function getAll()
    {
        return $this->orderDao->getAll();
    }

    /**
     * Get order by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order ID");
        }
        return $this->orderDao->getById($id);
    }

    /**
     * Get orders by user ID
     * @param int $user_id
     * @return array
     */
    public function getOrdersByUser($user_id)
    {
        if (!is_numeric($user_id) || $user_id <= 0) {
            throw new Exception("Invalid user ID");
        }
        return $this->orderDao->getOrdersByUser($user_id);
    }

    /**
     * Get orders by status
     * @param string $status
     * @return array
     */
    public function getOrdersByStatus($status)
    {
        $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status. Must be: " . implode(', ', $validStatuses));
        }
        return $this->orderDao->getOrdersByStatus($status);
    }

    /**
     * Get order with details
     * @param int $order_id
     * @return array
     */
    public function getOrderWithDetails($order_id)
    {
        if (!is_numeric($order_id) || $order_id <= 0) {
            throw new Exception("Invalid order ID");
        }
        return $this->orderDao->getOrderWithDetails($order_id);
    }

    /**
     * Add new order
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['user_id']) || !is_numeric($data['user_id'])) {
            throw new Exception("Valid user ID is required");
        }
        if (empty($data['total_amount']) || !is_numeric($data['total_amount']) || $data['total_amount'] <= 0) {
            throw new Exception("Valid total amount is required");
        }

        // Check if user exists
        $user = $this->userDao->getById($data['user_id']);
        if (!$user) {
            throw new Exception("User not found");
        }

        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->orderDao->add($data);
    }

    /**
     * Update order
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order ID");
        }

        // Check if order exists
        $existingOrder = $this->orderDao->getById($id);
        if (!$existingOrder) {
            throw new Exception("Order not found");
        }

        // Validate total_amount if provided
        if (isset($data['total_amount']) && (!is_numeric($data['total_amount']) || $data['total_amount'] <= 0)) {
            throw new Exception("Total amount must be a positive number");
        }

        // Validate status if provided
        if (isset($data['status'])) {
            $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
            if (!in_array($data['status'], $validStatuses)) {
                throw new Exception("Invalid status");
            }
        }

        return $this->orderDao->update($data, $id);
    }

    /**
     * Update order status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order ID");
        }

        $validStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid status. Must be: " . implode(', ', $validStatuses));
        }

        // Check if order exists
        $order = $this->orderDao->getById($id);
        if (!$order) {
            throw new Exception("Order not found");
        }

        return $this->orderDao->updateStatus($id, $status);
    }

    /**
     * Delete order
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid order ID");
        }

        // Check if order exists
        $order = $this->orderDao->getById($id);
        if (!$order) {
            throw new Exception("Order not found");
        }

        return $this->orderDao->delete($id);
    }

    /**
     * Get total revenue
     * @return float
     */
    public function getTotalRevenue()
    {
        return $this->orderDao->getTotalRevenue();
    }

    /**
     * Get orders count by date range
     * @param string $start_date
     * @param string $end_date
     * @return int
     */
    public function getOrdersCountByDateRange($start_date, $end_date)
    {
        return $this->orderDao->getOrdersCountByDateRange($start_date, $end_date);
    }
}
?>