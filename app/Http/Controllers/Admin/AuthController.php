<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\AdminAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private AdminAuthService $adminAuthService
    ) {}

    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View
    {
        return view('admin.login');
    }

    /**
     * Handle admin login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        try {
            $this->adminAuthService->login(
                $request->email,
                $request->password
            );

            $request->session()->regenerate();

            return redirect()->intended(route('admin.users.index'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        }
    }

    /**
     * Handle admin logout.
     */
    public function logout(): RedirectResponse
    {
        $this->adminAuthService->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
