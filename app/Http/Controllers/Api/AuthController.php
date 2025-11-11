<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        $user = $authService->register($request);

        // Create Sanctum token for API clients
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
                'token' => $token,
            ],
            'message' => 'User registered successfully',
            'status' => 'success',
        ], 201);
    }

    /**
     * Login user and return token.
     *
     * The 'remember' parameter affects the session cookie lifetime for web clients.
     * For API clients using Sanctum tokens, tokens already have a long lifetime.
     * See ARCHITECTURE.md for more details on Remember Me behavior.
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $user = $authService->login($request);

        // Create Sanctum token for API clients
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
                'token' => $token,
            ],
            'message' => 'Login successful',
            'status' => 'success',
        ]);
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        // Logout user from session (only if session auth is being used)
        if (Auth::check()) {
            // Réinitialiser le flag d'animation terminal lors de la déconnexion
            session()->forget('terminal_boot_seen');
            Auth::guard('web')->logout();
        }

        return response()->json([
            'message' => 'Logged out successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Get authenticated user information.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'home_planet_id' => $user->home_planet_id,
                ],
            ],
            'status' => 'success',
        ]);
    }
}
