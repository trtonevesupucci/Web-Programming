<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/OrderDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

/**
 * Order Service
 * Business logic for order operations
 */
class OrderService extends BaseService
{
    private $userDao;

    public function __construct()
    {
        $dao = new OrderDao();
        parent::__construct($dao);
        $this->userDao = new UserDao();
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
        return $this->dao->getOrdersByUser($user_id);
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
        return $this->dao->getOrdersByStatus($status);
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
        return $this->dao->getOrderWithDetails($order_id);
    }

    /**
     * Add new order (with validation)
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

        return parent::add($data);
    }

    /**
     * Update order (with validation)
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        // Check if order exists
        $existingOrder = $this->dao->getById($id);
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

        return parent::update($id, $data);
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

        $order = $this->dao->getById($id);
        if (!$order) {
            throw new Exception("Order not found");
        }

        return $this->dao->updateStatus($id, $status);
    }

    /**
     * Get total revenue
     * @return float
     */
    public function getTotalRevenue()
    {
        return $this->dao->getTotalRevenue();
    }
}
?>