<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    /**
     * Login admin user and authenticate them with admin guard.
     * Verifies is_super_admin flag and email whitelist.
     */
    public function login(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user has super admin flag
        if (! $user->is_super_admin) {
            throw ValidationException::withMessages([
                'email' => ['You do not have admin privileges.'],
            ]);
        }

        // Check if user email is in whitelist
        $whitelist = env('ADMIN_EMAIL_WHITELIST', '');
        $allowedEmails = array_map('trim', explode(',', $whitelist));
        $allowedEmails = array_filter($allowedEmails); // Remove empty values

        if (! empty($allowedEmails) && ! in_array($user->email, $allowedEmails)) {
            throw ValidationException::withMessages([
                'email' => ['Your email is not authorized to access the admin panel.'],
            ]);
        }

        // Authenticate user with admin guard
        Auth::guard('admin')->login($user);

        return $user;
    }

    /**
     * Logout admin user.
     */
    public function logout(): void
    {
        Auth::guard('admin')->logout();
    }
}
