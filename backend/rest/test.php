<?php
/**
 * Test file for DAO classes
 * Run this to check if database connection and DAO operations work
 */

// Include DAO classes
require_once 'dao/UserDao.php';
require_once 'dao/CategoryDao.php';
require_once 'dao/MenuItemDao.php';
require_once 'dao/OrderDao.php';
require_once 'dao/ReservationDao.php';

echo "<h1>DAO Test Results</h1>";
echo "<hr>";

// Test 1: UserDao
echo "<h2>Test 1: UserDao</h2>";
try {
    $userDao = new UserDao();
    $users = $userDao->getAll();
    echo "✅ UserDao works! Found " . count($users) . " users.<br>";
    
    if (count($users) > 0) {
        echo "<pre>";
        print_r($users[0]); // Show first user
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ UserDao Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 2: CategoryDao
echo "<h2>Test 2: CategoryDao</h2>";
try {
    $categoryDao = new CategoryDao();
    $categories = $categoryDao->getAll();
    echo "✅ CategoryDao works! Found " . count($categories) . " categories.<br>";
    
    if (count($categories) > 0) {
        echo "<pre>";
        print_r($categories[0]); // Show first category
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ CategoryDao Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 3: MenuItemDao
echo "<h2>Test 3: MenuItemDao</h2>";
try {
    $menuItemDao = new MenuItemDao();
    $items = $menuItemDao->getAll();
    echo "✅ MenuItemDao works! Found " . count($items) . " menu items.<br>";
    
    // Test custom method
    $availableItems = $menuItemDao->getAvailableItems();
    echo "Available items: " . count($availableItems) . "<br>";
    
    if (count($items) > 0) {
        echo "<pre>";
        print_r($items[0]); // Show first item
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ MenuItemDao Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 4: OrderDao
echo "<h2>Test 4: OrderDao</h2>";
try {
    $orderDao = new OrderDao();
    $orders = $orderDao->getAll();
    echo "✅ OrderDao works! Found " . count($orders) . " orders.<br>";
    
    if (count($orders) > 0) {
        echo "<pre>";
        print_r($orders[0]); // Show first order
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ OrderDao Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

// Test 5: ReservationDao
echo "<h2>Test 5: ReservationDao</h2>";
try {
    $reservationDao = new ReservationDao();
    $reservations = $reservationDao->getAll();
    echo "✅ ReservationDao works! Found " . count($reservations) . " reservations.<br>";
    
    if (count($reservations) > 0) {
        echo "<pre>";
        print_r($reservations[0]); // Show first reservation
        echo "</pre>";
    }
} catch (Exception $e) {
    echo "❌ ReservationDao Error: " . $e->getMessage() . "<br>";
}
echo "<hr>";

echo "<h2>All Tests Complete!</h2>";
echo "<p>If you see ✅ for all tests, your DAO classes are working correctly!</p>";
?>