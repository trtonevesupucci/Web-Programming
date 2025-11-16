<?php
require_once __DIR__ . '/../dao/MenuItemDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

/**
 * MenuItem Service
 * Business logic for menu item operations
 */
class MenuItemService
{
    private $menuItemDao;
    private $categoryDao;

    public function __construct()
    {
        $this->menuItemDao = new MenuItemDao();
        $this->categoryDao = new CategoryDao();
    }

    /**
     * Get all menu items
     * @return array
     */
    public function getAll()
    {
        return $this->menuItemDao->getAll();
    }

    /**
     * Get menu item by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid menu item ID");
        }
        return $this->menuItemDao->getById($id);
    }

    /**
     * Get menu items by category
     * @param int $category_id
     * @return array
     */
    public function getByCategory($category_id)
    {
        if (!is_numeric($category_id) || $category_id <= 0) {
            throw new Exception("Invalid category ID");
        }
        return $this->menuItemDao->getByCategory($category_id);
    }

    /**
     * Get available menu items
     * @return array
     */
    public function getAvailableItems()
    {
        return $this->menuItemDao->getAvailableItems();
    }

    /**
     * Get menu items with category details
     * @return array
     */
    public function getMenuItemsWithCategory()
    {
        return $this->menuItemDao->getMenuItemsWithCategory();
    }

    /**
     * Search menu items by name
     * @param string $search_term
     * @return array
     */
    public function searchByName($search_term)
    {
        if (empty($search_term)) {
            throw new Exception("Search term is required");
        }
        return $this->menuItemDao->searchByName($search_term);
    }

    /**
     * Add new menu item
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['name'])) {
            throw new Exception("Menu item name is required");
        }
        if (empty($data['category_id']) || !is_numeric($data['category_id'])) {
            throw new Exception("Valid category ID is required");
        }
        if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] <= 0) {
            throw new Exception("Valid price is required");
        }

        // Check if category exists
        $category = $this->categoryDao->getById($data['category_id']);
        if (!$category) {
            throw new Exception("Category not found");
        }

        // Set default availability if not provided
        if (!isset($data['is_available'])) {
            $data['is_available'] = true;
        }

        return $this->menuItemDao->add($data);
    }

    /**
     * Update menu item
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid menu item ID");
        }

        // Check if menu item exists
        $existingItem = $this->menuItemDao->getById($id);
        if (!$existingItem) {
            throw new Exception("Menu item not found");
        }

        // Validate price if provided
        if (isset($data['price']) && (!is_numeric($data['price']) || $data['price'] <= 0)) {
            throw new Exception("Price must be a positive number");
        }

        // Validate category if provided
        if (isset($data['category_id'])) {
            if (!is_numeric($data['category_id'])) {
                throw new Exception("Invalid category ID");
            }
            $category = $this->categoryDao->getById($data['category_id']);
            if (!$category) {
                throw new Exception("Category not found");
            }
        }

        return $this->menuItemDao->update($data, $id);
    }

    /**
     * Update menu item availability
     * @param int $id
     * @param bool $is_available
     * @return bool
     */
    public function updateAvailability($id, $is_available)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid menu item ID");
        }

        // Check if menu item exists
        $item = $this->menuItemDao->getById($id);
        if (!$item) {
            throw new Exception("Menu item not found");
        }

        return $this->menuItemDao->updateAvailability($id, $is_available);
    }

    /**
     * Delete menu item
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid menu item ID");
        }

        // Check if menu item exists
        $item = $this->menuItemDao->getById($id);
        if (!$item) {
            throw new Exception("Menu item not found");
        }

        return $this->menuItemDao->delete($id);
    }
}
?>