<?php
echo "Step 1: Started<br>";

echo "Step 2: Loading BaseService...<br>";
require_once 'rest/services/BaseService.php';
echo "✓ BaseService loaded<br>";

echo "Step 3: Loading OrderItemDao...<br>";
require_once 'rest/dao/OrderItemDao.php';
echo "✓ OrderItemDao loaded<br>";

echo "Step 4: Loading OrderItemService...<br>";
require_once 'rest/services/OrderItemService.php';
echo "✓ OrderItemService loaded<br>";

echo "Step 5: Instantiating OrderItemService...<br>";
$service = new OrderItemService();
echo "✓ OrderItemService instantiated!<br>";

echo "<br><strong>Success!</strong>";
?>