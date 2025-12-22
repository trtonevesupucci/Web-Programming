<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

/**
 * Category Service
 * Business logic for category operations
 */
class CategoryService extends BaseService
{
    public function __construct()
    {
        $dao = new CategoryDao();
        parent::__construct($dao);
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
        return $this->dao->getCategoryByName($name);
    }

    /**
     * Get categories with item count
     * @return array
     */
    public function getCategoriesWithItemCount()
    {
        return $this->dao->getCategoriesWithItemCount();
    }

    /**
     * Add new category (with validation)
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
        if ($this->dao->getCategoryByName($data['name'])) {
            throw new Exception("Category name already exists");
        }

        return parent::add($data);
    }

    /**
     * Update category (with validation)
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        // Check if category exists
        $existingCategory = $this->dao->getById($id);
        if (!$existingCategory) {
            throw new Exception("Category not found");
        }

        // Validate name if provided
        if (isset($data['name']) && empty($data['name'])) {
            throw new Exception("Category name cannot be empty");
        }

        return parent::update($id, $data);
    }
}
?>