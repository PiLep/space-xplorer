<?php

namespace App\Http\Controllers\Api;

use App\Events\UserRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch event to generate home planet
        event(new UserRegistered($user));

        // Create Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Store token in session for Livewire components
        // Livewire components will call API endpoints and need the token for subsequent requests
        Session::put('sanctum_token', $token);

        // Authenticate user in session for web routes (Livewire pages)
        Auth::login($user);

        // Refresh user to get updated home_planet_id if planet was generated
        $user->refresh();

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
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all existing tokens (optional: for single device login)
        // $user->tokens()->delete();

        // Create new Sanctum token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Store token in session for Livewire components
        // Livewire components will call API endpoints and need the token for subsequent requests
        Session::put('sanctum_token', $token);

        // Authenticate user in session for web routes (Livewire pages)
        Auth::login($user);

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
