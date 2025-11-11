<?php

namespace App\Livewire;

use App\Exceptions\EmailVerificationException;
use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class VerifyEmail extends Component
{
    public $code = '';

    public $status = '';

    protected $isVerifying = false;

    protected $autoVerifying = false;

    protected $rules = [
        'code' => 'required|string|size:6|regex:/^[0-9]+$/',
    ];

    protected $messages = [
        'code.required' => 'Security clearance code required.',
        'code.size' => 'Security clearance code must be 6 digits (STELLAR_CORP_MEGA_INC protocol).',
        'code.regex' => 'Security clearance code must contain only numeric characters.',
    ];

    public function mount()
    {
        // Check if user is authenticated
        if (! Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        $user = Auth::user();

        // Check if email is already verified
        if ($user->hasVerifiedEmail()) {
            return $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function updatedCode($value)
    {
        // Auto-format: remove non-numeric characters and limit to 6 digits
        $this->code = preg_replace('/[^0-9]/', '', $value);
        $this->code = substr($this->code, 0, 6);

        // Clear status when code changes
        $this->status = '';

        // Auto-verify when code reaches 6 digits (e.g., when pasted)
        // Only if not already verifying to avoid multiple calls
        if (strlen($this->code) === 6 && ! $this->isVerifying) {
            $this->autoVerifying = true;
            $this->verify(app(EmailVerificationService::class));
            $this->autoVerifying = false;
        }
    }

    public function verify(EmailVerificationService $emailVerificationService)
    {
        // Prevent multiple simultaneous verification attempts
        // But allow manual calls even if auto-verification is in progress
        if ($this->isVerifying && $this->autoVerifying) {
            return;
        }

        $this->isVerifying = true;
        $this->status = '[PROCESSING] Validating security clearance with STELLAR_CORP_MEGA_INC authentication servers...';
        $this->validate();

        $user = Auth::user();

        try {
            // Check if email is already verified
            if ($user->hasVerifiedEmail()) {
                $this->isVerifying = false;
                session()->flash('success', 'Security clearance already active. Welcome back, '.$user->name.'!');

                return $this->redirect(route('dashboard'), navigate: true);
            }

            // Check if user has exceeded attempts
            if ($user->hasExceededVerificationAttempts()) {
                $this->isVerifying = false;
                $this->status = '[SECURITY_LOCKOUT] Maximum authentication attempts exceeded. Account temporarily locked. Request new clearance code from STELLAR_CORP_MEGA_INC security division.';
                $this->addError('code', 'Security lockout activated. Request new clearance code.');

                return;
            }

            // Check if code exists and is not expired
            if (! $user->hasPendingVerificationCode()) {
                $this->isVerifying = false;
                $this->status = '[EXPIRED] Security clearance token has expired (STELLAR_CORP_MEGA_INC protocol: 15-minute validity window). Request new authentication token.';
                $this->addError('code', 'Clearance token expired. Request new token.');

                return;
            }

            // Verify the code
            $isValid = $emailVerificationService->verifyCode($user, $this->code);

            if (! $isValid) {
                $this->isVerifying = false;
                $attemptsRemaining = $this->getAttemptsRemainingProperty();
                $this->status = '[AUTH_FAILURE] Invalid security clearance code. Authentication rejected by STELLAR_CORP_MEGA_INC security systems. '.$attemptsRemaining.' attempts remaining before lockout.';
                $this->addError('code', 'Clearance code invalid. Verify and retry.');
                $this->code = '';

                return;
            }

            // Success - email verified
            session()->flash('success', 'Security clearance granted. Account activated. Welcome to STELLAR_CORP_MEGA_INC, '.$user->name.'!');
            $this->code = '';

            // Redirect to dashboard
            $this->isVerifying = false;

            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            $this->status = '[SYSTEM_ERROR] Authentication protocol failure. STELLAR_CORP_MEGA_INC security systems unavailable. Retry or contact support.';
            $this->addError('code', $e->getMessage() ?: 'System error during clearance validation.');
            $this->isVerifying = false;
        }
    }

    public function resend(EmailVerificationService $emailVerificationService)
    {
        $user = Auth::user();

        try {
            // Check if user can resend
            if (! $user->canResendVerificationCode()) {
                $cooldown = $this->getResendCooldownProperty();
                $this->status = '[RATE_LIMIT] Anti-fraud protocol active. New token request available in '.$cooldown.' seconds.';

                return;
            }

            // Generate and send new code
            $emailVerificationService->resendCode($user);
            $this->status = '[SUCCESS] New security clearance token dispatched to corporate email address. Check your inbox.';
            $this->code = '';
        } catch (EmailVerificationException $e) {
            $this->status = '[ERROR] '.$e->getMessage();
            $this->addError('code', $e->getMessage());
        } catch (\Exception $e) {
            $this->status = '[SYSTEM_ERROR] Failed to dispatch security token. STELLAR_CORP_MEGA_INC communication systems unavailable. Retry later.';
            $this->addError('code', $e->getMessage() ?: 'System error during token dispatch.');
        }
    }

    public function getAttemptsRemainingProperty(): int
    {
        $user = Auth::user();

        return max(0, User::MAX_VERIFICATION_ATTEMPTS - $user->email_verification_attempts);
    }

    public function getCanResendProperty(): bool
    {
        $user = Auth::user();

        return $user->canResendVerificationCode();
    }

    public function getResendCooldownProperty(): int
    {
        $user = Auth::user();

        if ($user->email_verification_code_sent_at === null) {
            return 0;
        }

        $cooldownEnd = $user->email_verification_code_sent_at->copy()->addMinutes(User::RESEND_COOLDOWN_MINUTES);
        $secondsRemaining = now()->diffInSeconds($cooldownEnd, false);

        return max(0, $secondsRemaining);
    }

    public function getMaskedEmailProperty(): string
    {
        $user = Auth::user();
        $email = $user->email;
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return $email;
        }

        $local = $parts[0];
        $domain = $parts[1];

        // Mask local part: show first character, mask the rest
        $maskedLocal = substr($local, 0, 1).str_repeat('*', max(1, strlen($local) - 1));

        return $maskedLocal.'@'.$domain;
    }

    public function updateCooldown()
    {
        // Force Livewire to refresh computed properties
        // This method is called by wire:poll to update the cooldown display
    }

    public function render()
    {
        return view('livewire.verify-email');
    }
}
