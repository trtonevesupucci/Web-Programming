<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Connection Test</h2>";

echo "Step 1: Loading config...<br>";
require_once 'rest/config.php';  // ← Ispravljena putanja

echo "Step 2: Config loaded<br>";
echo "DB Host: " . Config::DB_HOST() . "<br>";
echo "DB Name: " . Config::DB_NAME() . "<br>";
echo "DB User: " . Config::DB_USER() . "<br>";
echo "DB Port: " . Config::DB_PORT() . "<br><br>";

echo "Step 3: Trying to connect to database...<br>";

try {
    $connection = new PDO(
        "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT(),
        Config::DB_USER(),
        Config::DB_PASSWORD(),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    echo "<strong style='color: green;'>✓ SUCCESS!</strong> Database connection established!<br><br>";
    
    // Test if we can query
    echo "Step 4: Testing query - showing all tables...<br>";
    $stmt = $connection->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database:<br>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . $table . "</li>";
    }
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "<strong style='color: red;'>✗ ERROR:</strong> " . $e->getMessage() . "<br>";
}

echo "<br>Test completed!";
?>