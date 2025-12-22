<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/OrderItemDao.php';

class OrderItemService extends BaseService {
    
    public function __construct() {
        $dao = new OrderItemDao();
        parent::__construct($dao);
    }
    
    // Get all items for a specific order
    public function getByOrderId($order_id) {
        return $this->dao->getItemsByOrder($order_id); // ← OVDJE promijeni
    }
    
    // Get order items with menu details
    public function getItemsWithMenuDetails($order_id) {
        return $this->dao->getItemsWithMenuDetails($order_id);
    }
    
    // Get most ordered items
    public function getMostOrderedItems($limit = 10) {
        return $this->dao->getMostOrderedItems($limit);
    }
    
    // Business logic: Validate quantity before creating order item
    public function createOrderItem($data) {
        // Ensure quantity is positive
        if (!isset($data['quantity']) || $data['quantity'] <= 0) {
            throw new Exception('Quantity must be a positive value.');
        }
        
        // Ensure required fields are present
        if (!isset($data['order_id']) || !isset($data['menu_item_id'])) {
            throw new Exception('Order ID and Menu Item ID are required.');
        }
        
        return $this->create($data);
    }
    
    // Business logic: Calculate total price for order items
    public function calculateOrderTotal($order_id) {
        $items = $this->getByOrderId($order_id);
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    // Update order item with validation
    public function updateOrderItem($id, $data) {
        if (isset($data['quantity']) && $data['quantity'] <= 0) {
            throw new Exception('Quantity must be a positive value.');
        }
        
        return $this->update($id, $data);
    }
    
    // Delete items by order ID
    public function deleteByOrderId($order_id) {
        return $this->dao->deleteByOrderId($order_id);
    }
}

?>