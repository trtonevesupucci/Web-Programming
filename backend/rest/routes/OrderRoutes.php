<?php

/**
 * @OA\Get(
 *      path="/orders",
 *      tags={"orders"},
 *      summary="Get all orders",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all orders in the database"
 *      )
 * )
 */
Flight::route('GET /orders', function(){
    $user = getAuthenticatedUser();
    if (!$user) {
        Flight::halt(401, json_encode(['error' => 'Unauthorized']));
    }
    Flight::json(Flight::orderService()->getAll());
});

/**
 * @OA\Get(
 *     path="/orders/user/{user_id}",
 *     tags={"orders"},
 *     summary="Get orders by user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns orders for the specified user"
 *     )
 * )
 */
Flight::route('GET /orders/user/@user_id', function($user_id){
    Flight::json(Flight::orderService()->getOrdersByUser($user_id));
});

/**
 * @OA\Get(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Get order by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the order",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the order with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     )
 * )
 */
Flight::route('GET /orders/@id', function($id){
    $order = Flight::orderService()->getById($id);
    if ($order) {
        Flight::json($order);
    } else {
        Flight::json(['error' => 'Order not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/orders",
 *     tags={"orders"},
 *     summary="Create a new order (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="total_price", type="number", format="float", example=45.99),
 *             @OA\Property(property="status", type="string", example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="New order created successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('POST /orders', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::orderService()->add($data), 201);
});

/**
 * @OA\Put(
 *     path="/orders/{id}/status",
 *     tags={"orders"},
 *     summary="Update order status (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="completed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order status updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /orders/@id/status', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::orderService()->updateStatus($id, $data['status']);
    Flight::json(['success' => true, 'message' => 'Order status updated']);
});

/**
 * @OA\Put(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Update an existing order by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="total_price", type="number", format="float", example=50.99),
 *             @OA\Property(property="status", type="string", example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /orders/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::orderService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/orders/{id}",
 *     tags={"orders"},
 *     summary="Delete an order by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('DELETE /orders/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::orderService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Order deleted']);
});

?>