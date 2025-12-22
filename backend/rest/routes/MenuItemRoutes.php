<?php

/**
 * @OA\Get(
 *      path="/menu-items",
 *      tags={"menu-items"},
 *      summary="Get all menu items",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all menu items in the database"
 *      )
 * )
 */
Flight::route('GET /menu-items', function(){
    Flight::json(Flight::menuItemService()->getAll());
});

/**
 * @OA\Get(
 *      path="/menu-items/available",
 *      tags={"menu-items"},
 *      summary="Get available menu items",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all available menu items"
 *      )
 * )
 */
Flight::route('GET /menu-items/available', function(){
    Flight::json(Flight::menuItemService()->getAvailableItems());
});

/**
 * @OA\Get(
 *     path="/menu-items/category/{category_id}",
 *     tags={"menu-items"},
 *     summary="Get menu items by category",
 *     @OA\Parameter(
 *         name="category_id",
 *         in="path",
 *         required=true,
 *         description="ID of the category",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns menu items in the specified category"
 *     )
 * )
 */
Flight::route('GET /menu-items/category/@category_id', function($category_id){
    Flight::json(Flight::menuItemService()->getByCategory($category_id));
});

/**
 * @OA\Get(
 *     path="/menu-items/{id}",
 *     tags={"menu-items"},
 *     summary="Get menu item by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the menu item",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the menu item with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Menu item not found"
 *     )
 * )
 */
Flight::route('GET /menu-items/@id', function($id){
    $item = Flight::menuItemService()->getById($id);
    if ($item) {
        Flight::json($item);
    } else {
        Flight::json(['error' => 'Menu item not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/menu-items",
 *     tags={"menu-items"},
 *     summary="Create a new menu item (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "category_id", "price"},
 *             @OA\Property(property="name", type="string", example="Caesar Salad"),
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="description", type="string", example="Fresh romaine with parmesan"),
 *             @OA\Property(property="price", type="number", format="float", example=8.99),
 *             @OA\Property(property="is_available", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="New menu item created successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('POST /menu-items', function(){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    $data = Flight::request()->data->getData();
    Flight::json(Flight::menuItemService()->add($data), 201);
});

/**
 * @OA\Put(
 *     path="/menu-items/{id}",
 *     tags={"menu-items"},
 *     summary="Update an existing menu item by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Menu Item ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "category_id", "price"},
 *             @OA\Property(property="name", type="string", example="Updated Salad"),
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(property="description", type="string", example="Updated description"),
 *             @OA\Property(property="price", type="number", format="float", example=9.99),
 *             @OA\Property(property="is_available", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu item updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /menu-items/@id', function($id){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    $data = Flight::request()->data->getData();
    Flight::json(Flight::menuItemService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/menu-items/{id}",
 *     tags={"menu-items"},
 *     summary="Delete a menu item by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Menu Item ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Menu item deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('DELETE /menu-items/@id', function($id){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    Flight::menuItemService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Menu item deleted']);
});

?>