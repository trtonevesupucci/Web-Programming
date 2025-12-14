<?php
/**
 * Temporary script to set user roles
 * Run this once to set admin users
 */

require_once __DIR__ . '/rest/config.php';

$db = new mysqli(
    Config::DB_HOST(),
    Config::DB_USER(),
    Config::DB_PASS(),
    Config::DB_NAME(),
    Config::DB_PORT()
);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Update user with email 'testadmin@test.com' to have admin role
$email = 'testadmin@test.com';
$role = 'admin';

$sql = "UPDATE users SET role = ? WHERE email = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("ss", $role, $email);

if ($stmt->execute()) {
    echo "✓ User '$email' updated to role '$role'\n";
    echo "You can now test with the admin token!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$db->close();
?>
