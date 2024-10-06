<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(name="Password Reset", description="Password Reset operations")
 */
class PasswordResetController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/password/email",
     *     summary="Send Password Reset Link",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Password reset link sent", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     *     @OA\Response(response=400, description="User not found", @OA\JsonContent()),
     * )
     */
    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $response = Password::sendResetLink($request->only('email'));

        return $response === Password::RESET_LINK_SENT
            ? response()->json(['message' => __('passwords.sent')], 200)
            : response()->json(['message' => __('passwords.user')], 400);
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Reset User Password",
     *     tags={"Password Reset"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="your-token"),
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="newpassword"),
     *             @OA\Property(property="password_confirmation", type="string", example="newpassword"),
     *         ),
     *     ),
     *     @OA\Response(response=200, description="Password has been reset", @OA\JsonContent()),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent()),
     *     @OA\Response(response=400, description="Invalid token or user not found", @OA\JsonContent()),
     * )
     */
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        // Reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => bcrypt($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __('passwords.reset')], 200)
            : response()->json(['message' => __('passwords.token')], 400);
    }
}
