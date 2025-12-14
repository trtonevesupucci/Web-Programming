<?php
require_once __DIR__ . '/rest/services/MenuItemService.php';

$menu_item_service = new MenuItemService();
try {
	// Get menu items with category details
	$menus = $menu_item_service->getMenuItemsWithCategory();
	print_r($menus);

	// Example: get items for category ID 1
	$categories = $menu_item_service->getByCategory(1);
	print_r($categories);
} catch (Exception $e) {
	echo 'Error: ' . $e->getMessage();
}
?>
