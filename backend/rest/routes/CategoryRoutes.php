<?php

/**
 * @OA\Get(
 *      path="/categories",
 *      tags={"categories"},
 *      summary="Get all categories",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all categories in the database"
 *      )
 * )
 */
Flight::route('GET /categories', function(){
    Flight::json(Flight::categoryService()->getAll());
});

/**
 * @OA\Get(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Get category by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the category",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the category with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 */
Flight::route('GET /categories/@id', function($id){
    $category = Flight::categoryService()->getById($id);
    if ($category) {
        Flight::json($category);
    } else {
        Flight::json(['error' => 'Category not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/categories",
 *     tags={"categories"},
 *     summary="Create a new category (Admin only)",
 *     security={{"ApiKey": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Appetizers"),
 *             @OA\Property(property="description", type="string", example="Start your meal with delicious appetizers")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="New category created successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('POST /categories', function(){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    $data = Flight::request()->data->getData();
    Flight::json(Flight::categoryService()->add($data), 201);
});

/**
 * @OA\Put(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Update an existing category by ID (Admin only)",
 *     security={{"ApiKey": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Updated Category"),
 *             @OA\Property(property="description", type="string", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /categories/@id', function($id){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    $data = Flight::request()->data->getData();
    Flight::json(Flight::categoryService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/categories/{id}",
 *     tags={"categories"},
 *     summary="Delete a category by ID (Admin only)",
 *     security={{"ApiKey": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Category ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('DELETE /categories/@id', function($id){
    $user = getAuthenticatedUser();
    if (!$user || (is_object($user) ? $user->role : $user['role']) !== 'admin') {
        Flight::halt(403, json_encode(['error' => 'Access denied: insufficient privileges']));
    }
    Flight::categoryService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Category deleted']);
});
?>