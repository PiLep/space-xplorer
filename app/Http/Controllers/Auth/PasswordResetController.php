<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\PasswordResetConfirmation;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form.
     * Note: This is handled by the Livewire component route directly.
     */
    public function showForgotPasswordForm(): View
    {
        // This method is not used as the route uses Livewire component directly
        // But kept for consistency
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link.
     */
    public function sendResetLink(ForgotPasswordRequest $request, PasswordResetService $passwordResetService): RedirectResponse
    {
        // Send reset link using the service
        $status = $passwordResetService->sendResetLink($request->email);

        // Always return success message for security (don't reveal if email exists)
        return redirect()
            ->route('password.request')
            ->with('status', __('Si cet email existe dans notre système, un lien de réinitialisation vous a été envoyé. Vérifiez votre boîte de réception.'));
    }

    /**
     * Show the reset password form.
     */
    public function showResetForm(Request $request, string $token): View|RedirectResponse
    {
        // Get email from query string
        $email = $request->query('email');

        if (! $email) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => __('Ce lien de réinitialisation est invalide.')]);
        }

        // Check if user exists
        $user = \App\Models\User::where('email', $email)->first();

        if (! $user) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => __('Ce lien de réinitialisation est invalide.')]);
        }

        // Verify token exists in password_reset_tokens table
        // Laravel stores tokens hashed with Hash::make(), so we need to check all tokens for this email
        $resetRecord = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetRecord || ! \Illuminate\Support\Facades\Hash::check($token, $resetRecord->token)) {
            return redirect()
                ->route('password.request')
                ->withErrors(['email' => __('Ce lien de réinitialisation est invalide ou a expiré.')]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset the user's password.
     */
    public function reset(ResetPasswordRequest $request, PasswordResetService $passwordResetService): RedirectResponse
    {
        // Reset password using the service
        $status = $passwordResetService->reset($request->only('email', 'password', 'password_confirmation', 'token'));

        // Check if reset was successful
        if ($status === Password::PASSWORD_RESET) {
            // Get user to send confirmation email
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user) {
                // Send confirmation email
                Mail::to($user)->send(new PasswordResetConfirmation($user));
            }

            return redirect()
                ->route('login')
                ->with('status', __('Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'));
        }

        // Handle errors
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
