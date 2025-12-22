<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::group('/auth', function() {
    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register a new user",
     *     description="Create a new user account with email and password",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         description="User registration data",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password", "name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe",
     *                     description="User full name"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="john@example.com",
     *                     description="User email address"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="securePassword123",
     *                     description="User password"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     example="+1234567890",
     *                     description="User phone number (optional)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully"),
     *             @OA\Property(property="data", type="object", description="User data")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Registration failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    Flight::route('POST /register', function() {
        $data = Flight::request()->data->getData();

        $response = Flight::auth_service()->register($data);

        if ($response['success']) {
            Flight::json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => $response['data']
            ], 201);
        } else {
            Flight::halt(500, json_encode(['error' => $response['error']]));
        }
    });

    /**
     * @OA\Post(
     *     path="/auth/login",
     *     tags={"auth"},
     *     summary="Login to system using email and password",
     *     description="Authenticate user and receive JWT token",
     *     @OA\RequestBody(
     *         description="Login credentials",
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="john@example.com",
     *                 description="User email address"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="securePassword123",
     *                 description="User password"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User logged in successfully with JWT token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged in successfully"),
     *             @OA\Property(property="data", type="object", description="User data with JWT token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Login failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();

        $response = Flight::auth_service()->login($data);

        if ($response['success']) {
            Flight::json([
                'success' => true,
                'message' => 'User logged in successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, json_encode(['error' => $response['error']]));
        }
    });
});
?>
