<?php
require_once 'BaseDao.php';

/**
 * MenuItem DAO
 * Handles database operations for menu_items table
 */
class MenuItemDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "menu_items";
        parent::__construct($this->table_name);
    }

    /**
     * Get menu items by category ID
     * @param int $category_id
     * @return array
     */
    public function getByCategory($category_id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM menu_items WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get available menu items
     * @return array
     */
    public function getAvailableItems()
    {
        $stmt = $this->connection->prepare("SELECT * FROM menu_items WHERE is_available = 1");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get menu items with category details
     * @return array
     */
    public function getMenuItemsWithCategory()
    {
        $query = "SELECT m.*, c.name as category_name, c.description as category_description 
                  FROM menu_items m 
                  JOIN categories c ON m.category_id = c.id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Search menu items by name
     * @param string $search_term
     * @return array
     */
    public function searchByName($search_term)
    {
        $stmt = $this->connection->prepare("SELECT * FROM menu_items WHERE name LIKE :search");
        $search = "%" . $search_term . "%";
        $stmt->bindParam(':search', $search);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Update item availability
     * @param int $id
     * @param bool $is_available
     * @return bool
     */
    public function updateAvailability($id, $is_available)
    {
        $stmt = $this->connection->prepare("UPDATE menu_items SET is_available = :is_available WHERE id = :id");
        $stmt->bindParam(':is_available', $is_available);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>