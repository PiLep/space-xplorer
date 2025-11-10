<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated with admin guard
        if (! Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        // Check if user has super admin flag
        if (! $user->is_super_admin) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'You do not have admin privileges.']);
        }

        // Check if user email is in whitelist
        $whitelist = config('admin.email_whitelist', '');
        $allowedEmails = array_map('trim', explode(',', $whitelist));
        $allowedEmails = array_filter($allowedEmails); // Remove empty values

        if (! empty($allowedEmails) && ! in_array($user->email, $allowedEmails)) {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Your email is not authorized to access the admin panel.']);
        }

        return $next($request);
    }
}
