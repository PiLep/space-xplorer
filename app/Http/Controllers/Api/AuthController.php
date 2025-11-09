<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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

        // Store token in session (for backward compatibility, though Livewire now uses direct service calls)
        Session::put('sanctum_token', $token);

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
     */
    public function login(LoginRequest $request, AuthService $authService): JsonResponse
    {
        $user = $authService->login($request);

        // Create Sanctum token for API clients
        $token = $user->createToken('auth-token')->plainTextToken;

        // Store token in session (for backward compatibility, though Livewire now uses direct service calls)
        Session::put('sanctum_token', $token);

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

        // Clear token from session for Livewire components
        Session::forget('sanctum_token');

        // Logout user from session (only if session auth is being used)
        if (Auth::check()) {
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
