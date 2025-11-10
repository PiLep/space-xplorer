<?php

namespace App\Services;

use App\Events\UserLoggedIn;
use App\Events\UserRegistered;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user and authenticate them.
     */
    public function register(RegisterRequest $request): User
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Dispatch event to generate home planet
        event(new UserRegistered($user));

        // Refresh user to get updated home_planet_id if planet was generated
        $user->refresh();

        // Authenticate user in session
        Auth::login($user);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        return $user;
    }

    /**
     * Register a new user from array data (for Livewire).
     */
    public function registerFromArray(array $data): User
    {
        // Validate data manually
        $validated = validator($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ])->validate();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Dispatch event to generate home planet
        event(new UserRegistered($user));

        // Refresh user to get updated home_planet_id if planet was generated
        $user->refresh();

        // Authenticate user in session
        Auth::login($user);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        return $user;
    }

    /**
     * Login user and authenticate them.
     */
    public function login(LoginRequest $request): User
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Authenticate user in session
        Auth::login($user);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        return $user;
    }

    /**
     * Login user from credentials (for Livewire).
     */
    public function loginFromCredentials(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Authenticate user in session
        Auth::login($user);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        return $user;
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
