<?php
require_once __DIR__ . '/../dao/CategoryDao.php';

/**
 * Category Service
 * Business logic for category operations
 */
class CategoryService
{
    private $categoryDao;

    public function __construct()
    {
        $this->categoryDao = new CategoryDao();
    }

    /**
     * Get all categories
     * @return array
     */
    public function getAll()
    {
        return $this->categoryDao->getAll();
    }

    /**
     * Get category by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid category ID");
        }
        return $this->categoryDao->getById($id);
    }

    /**
     * Get category by name
     * @param string $name
     * @return array|false
     */
    public function getCategoryByName($name)
    {
        if (empty($name)) {
            throw new Exception("Category name is required");
        }
        return $this->categoryDao->getCategoryByName($name);
    }

    /**
     * Get categories with item count
     * @return array
     */
    public function getCategoriesWithItemCount()
    {
        return $this->categoryDao->getCategoriesWithItemCount();
    }

    /**
     * Add new category
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        // Validation
        if (empty($data['name'])) {
            throw new Exception("Category name is required");
        }

        // Check if category name already exists
        if ($this->categoryDao->getCategoryByName($data['name'])) {
            throw new Exception("Category name already exists");
        }

        return $this->categoryDao->add($data);
    }

    /**
     * Update category
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid category ID");
        }

        // Check if category exists
        $existingCategory = $this->categoryDao->getById($id);
        if (!$existingCategory) {
            throw new Exception("Category not found");
        }

        // Validate name if provided
        if (isset($data['name']) && empty($data['name'])) {
            throw new Exception("Category name cannot be empty");
        }

        return $this->categoryDao->update($data, $id);
    }

    /**
     * Delete category
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid category ID");
        }

        // Check if category exists
        $category = $this->categoryDao->getById($id);
        if (!$category) {
            throw new Exception("Category not found");
        }

        // Note: CASCADE DELETE will automatically delete related menu items
        return $this->categoryDao->delete($id);
    }
}
?>