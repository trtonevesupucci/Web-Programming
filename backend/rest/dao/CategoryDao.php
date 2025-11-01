<?php
require_once 'BaseDao.php';

/**
 * Category DAO
 * Handles database operations for categories table
 */
class CategoryDao extends BaseDao
{
    protected $table_name;

    public function __construct()
    {
        $this->table_name = "categories";
        parent::__construct($this->table_name);
    }

    /**
     * Get category by name
     * @param string $name
     * @return array|false
     */
    public function getCategoryByName($name)
    {
        $stmt = $this->connection->prepare("SELECT * FROM categories WHERE name = :name");
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get categories with item count
     * @return array
     */
    public function getCategoriesWithItemCount()
    {
        $query = "SELECT c.*, COUNT(m.id) as item_count 
                  FROM categories c 
                  LEFT JOIN menu_items m ON c.id = m.category_id 
                  GROUP BY c.id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>