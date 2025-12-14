<?php

/**
 * @OA\Get(
 *      path="/reservations",
 *      tags={"reservations"},
 *      summary="Get all reservations",
 *      @OA\Response(
 *           response=200,
 *           description="Array of all reservations in the database"
 *      )
 * )
 */
Flight::route('GET /reservations', function(){
    $user = getAuthenticatedUser();
    if (!$user) {
        Flight::halt(401, json_encode(['error' => 'Unauthorized']));
    }
    Flight::json(Flight::reservationService()->getAll());
});

/**
 * @OA\Get(
 *     path="/reservations/user/{user_id}",
 *     tags={"reservations"},
 *     summary="Get reservations by user",
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns reservations for the specified user"
 *     )
 * )
 */
Flight::route('GET /reservations/user/@user_id', function($user_id){
    Flight::json(Flight::reservationService()->getReservationsByUser($user_id));
});

/**
 * @OA\Get(
 *     path="/reservations/{id}",
 *     tags={"reservations"},
 *     summary="Get reservation by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the reservation",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Returns the reservation with the given ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Reservation not found"
 *     )
 * )
 */
Flight::route('GET /reservations/@id', function($id){
    $reservation = Flight::reservationService()->getById($id);
    if ($reservation) {
        Flight::json($reservation);
    } else {
        Flight::json(['error' => 'Reservation not found'], 404);
    }
});

/**
 * @OA\Post(
 *     path="/reservations",
 *     tags={"reservations"},
 *     summary="Create a new reservation (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "reservation_date"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="reservation_date", type="string", format="date-time", example="2025-12-20 19:00:00"),
 *             @OA\Property(property="num_guests", type="integer", example=4),
 *             @OA\Property(property="status", type="string", example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="New reservation created successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('POST /reservations', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reservationService()->add($data), 201);
});

/**
 * @OA\Put(
 *     path="/reservations/{id}/status",
 *     tags={"reservations"},
 *     summary="Update reservation status (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="confirmed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation status updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /reservations/@id/status', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::reservationService()->updateStatus($id, $data['status']);
    Flight::json(['success' => true, 'message' => 'Reservation status updated']);
});

/**
 * @OA\Put(
 *     path="/reservations/{id}",
 *     tags={"reservations"},
 *     summary="Update an existing reservation by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="reservation_date", type="string", format="date-time", example="2025-12-20 19:30:00"),
 *             @OA\Property(property="num_guests", type="integer", example=5),
 *             @OA\Property(property="status", type="string", example="confirmed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation updated successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('PUT /reservations/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reservationService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/reservations/{id}",
 *     tags={"reservations"},
 *     summary="Delete a reservation by ID (Admin only)",
 *     security={"ApiKey": {}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Reservation ID",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Reservation deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden - Admin access required"
 *     )
 * )
 */
Flight::route('DELETE /reservations/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::reservationService()->delete($id);
    Flight::json(['success' => true, 'message' => 'Reservation deleted']);
});

?>