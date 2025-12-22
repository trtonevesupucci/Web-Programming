<?php

/**
 * Base Service Class
 * Provides common CRUD operations for all services
 */
class BaseService
{
    protected $dao;

    /**
     * Constructor
     * @param object $dao - Data Access Object instance
     */
    public function __construct($dao)
    {
        $this->dao = $dao;
    }

    /**
     * Get all records
     * @return array
     */
    public function getAll()
    {
        return $this->dao->getAll();
    }

    /**
     * Get record by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        return $this->dao->getById($id);
    }

    /**
     * Create new record
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        return $this->dao->add($data);
    }

    /**
     * Update existing record
     * @param int $id
     * @param array $data
     * @return array
     */
    public function update($id, $data)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        return $this->dao->update($data, $id);
    }

    /**
     * Delete record
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("Invalid ID");
        }
        return $this->dao->delete($id);
    }
}

?>