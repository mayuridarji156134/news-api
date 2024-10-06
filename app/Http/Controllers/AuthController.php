<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Info(
 *     title="News Aggregator API",
 *     version="1.0.0",
 *     description="This is the API documentation for the News Aggregator built using Laravel.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(response=200, description="User successfully registered"),
     *     @OA\Response(response=400, description="Bad Request"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function register(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Return the token upon successful registration
            return response()->json([
                'message' => 'User registered successfully',
                'token' => $user->createToken('api-token')->plainTextToken
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors with 422 status code
            return response()->json([
                'message' => 'Bad Request : Validation failed',
                'errors' => $e->errors()
            ], 422);
        
        } catch (\Exception $e) {
            // Catch any other exceptions (e.g., database errors, server errors) and return a 500 response
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function login(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Retrieve the user by email
            $user = User::where('email', $request->email)->first();

            // Check if the user exists and the password is correct
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401); // Return 401 for incorrect credentials
            }

            // Return the token upon successful login
            return response()->json([
                'message' => 'Login successful',
                'token' => $user->createToken('api-token')->plainTextToken
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors with 422 status code
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Catch any other exceptions (e.g., database errors, server errors) and return a 500 response
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Ensure the user is authenticated before attempting to log out
        $request->user()->tokens()->delete();

        // Return response with 200 status code
        return response()->json(['message' => 'Logged out successfully'], 200);
    }

}
