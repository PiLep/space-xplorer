<?php

namespace App\Services;

use App\Events\FailedLoginAttempt;
use App\Events\FirstLogin;
use App\Events\UserLoggedIn;
use App\Events\UserLoggedOut;
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

        // Send email verification code
        $emailVerificationService = app(EmailVerificationService::class);
        $emailVerificationService->generateCode($user);

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

        // Send email verification code
        $emailVerificationService = app(EmailVerificationService::class);
        $emailVerificationService->generateCode($user);

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
            // Dispatch event for failed login attempt
            event(new FailedLoginAttempt(
                $request->email,
                $request->ip(),
                $request->userAgent()
            ));

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Get remember value from request (defaults to false if not provided)
        $remember = $request->filled('remember') ? (bool) $request->remember : false;

        // Check if this is the first login BEFORE authenticating
        // First login is when user logs in for the first time after registration
        // We check if user has verified email and has no previous login sessions
        $isFirstLogin = $user->hasVerifiedEmail() && ! $this->hasPreviousLogin($user);

        // Authenticate user in session with remember me option
        Auth::login($user, $remember);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        // Dispatch first login event if applicable
        if ($isFirstLogin) {
            event(new FirstLogin($user));
        }

        return $user;
    }

    /**
     * Login user from credentials (for Livewire).
     *
     * @param  bool  $remember  Whether to create a "remember me" cookie
     */
    public function loginFromCredentials(string $email, string $password, bool $remember = false): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            // Dispatch event for failed login attempt
            event(new FailedLoginAttempt(
                $email,
                request()->ip(),
                request()->userAgent()
            ));

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if this is the first login BEFORE authenticating
        // First login is when user logs in for the first time after registration
        // We check if user has verified email and has no previous login sessions
        $isFirstLogin = $user->hasVerifiedEmail() && ! $this->hasPreviousLogin($user);

        // Authenticate user in session with remember me option
        Auth::login($user, $remember);

        // Dispatch event to track user login
        event(new UserLoggedIn($user));

        // Dispatch first login event if applicable
        if ($isFirstLogin) {
            event(new FirstLogin($user));
        }

        return $user;
    }

    /**
     * Check if user's email is verified.
     */
    public function isEmailVerified(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        $user = Auth::user();

        // Réinitialiser le flag d'animation terminal lors de la déconnexion
        session()->forget('terminal_boot_seen');
        Auth::logout();

        // Dispatch event to track logout if user was authenticated
        if ($user) {
            event(new UserLoggedOut($user));
        }
    }

    /**
     * Check if user has had a previous login session.
     * This is used to determine if this is the first login.
     *
     * @return bool True if user has previous login sessions, false otherwise
     */
    private function hasPreviousLogin(User $user): bool
    {
        // Check if there are any sessions for this user in the sessions table
        // We check if there's at least one session record for this user
        // This is checked BEFORE login, so we don't need to exclude current session
        return \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $user->id)
            ->exists();
    }
}
