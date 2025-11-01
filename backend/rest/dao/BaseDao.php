<?php
require_once __DIR__ . "/../config.php";

/**
 * Base DAO class
 * Provides basic database operations for all entities
 */
class BaseDao
{
    protected $connection;
    private $table_name;

    /**
     * Constructor - establishes database connection
     * @param string $table_name - name of the database table
     */
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
        
        try {
            $this->connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT(),
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Execute a query with parameters
     * @param string $query - SQL query
     * @param array $params - query parameters
     * @return array - query results
     */
    protected function query($query, $params)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Execute a query and return a single result
     * @param string $query - SQL query
     * @param array $params - query parameters
     * @return array|false - single result or false
     */
    protected function query_unique($query, $params)
    {
        $results = $this->query($query, $params);
        return reset($results);
    }

    /**
     * Get all records from the table
     * @return array - all records
     */
    public function getAll()
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a single record by ID
     * @param int $id - record ID
     * @return array|false - record or false if not found
     */
    public function getById($id)
    {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table_name . " WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Add a new record to the table
     * @param array $entity - associative array of column => value
     * @return array - inserted entity with ID
     */
    public function add($entity)
    {
        $query = "INSERT INTO " . $this->table_name . " (";
        
        foreach ($entity as $column => $value) {
            $query .= $column . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ") VALUES (";
        
        foreach ($entity as $column => $value) {
            $query .= ":" . $column . ', ';
        }
        $query = substr($query, 0, -2);
        $query .= ")";

        $stmt = $this->connection->prepare($query);
        $stmt->execute($entity);
        $entity['id'] = $this->connection->lastInsertId();
        
        return $entity;
    }

    /**
     * Update an existing record
     * @param array $entity - associative array of column => value
     * @param int $id - record ID to update
     * @param string $id_column - name of ID column (default: "id")
     * @return array - updated entity
     */
    public function update($entity, $id, $id_column = "id")
    {
        $query = "UPDATE " . $this->table_name . " SET ";
        
        foreach ($entity as $column => $value) {
            $query .= $column . "=:" . $column . ", ";
        }
        $query = substr($query, 0, -2);
        $query .= " WHERE " . $id_column . " = :id";

        $stmt = $this->connection->prepare($query);
        $entity['id'] = $id;
        $stmt->execute($entity);
        
        return $entity;
    }

    /**
     * Delete a record by ID
     * @param int $id - record ID to delete
     * @return bool - true if successful
     */
    public function delete($id)
    {
        $stmt = $this->connection->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }
}
?>
