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
        'code.required' => 'Verification code required.',
        'code.size' => 'Verification code must be 6 digits.',
        'code.regex' => 'Verification code must contain only numbers.',
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
        $this->status = '[PROCESSING] Verifying code...';
        $this->validate();

        $user = Auth::user();

        try {
            // Check if email is already verified
            if ($user->hasVerifiedEmail()) {
                $this->isVerifying = false;
                session()->flash('success', 'Email already verified. Welcome back!');

                return $this->redirect(route('dashboard'), navigate: true);
            }

            // Check if user has exceeded attempts
            if ($user->hasExceededVerificationAttempts()) {
                $this->isVerifying = false;
                $this->status = '[ERROR] Maximum verification attempts exceeded. Please request a new code.';
                $this->addError('code', 'Maximum attempts exceeded. Please request a new code.');

                return;
            }

            // Check if code exists and is not expired
            if (! $user->hasPendingVerificationCode()) {
                $this->isVerifying = false;
                $this->status = '[ERROR] Verification code has expired. Please request a new code.';
                $this->addError('code', 'Code has expired. Please request a new code.');

                return;
            }

            // Verify the code
            $isValid = $emailVerificationService->verifyCode($user, $this->code);

            if (! $isValid) {
                $this->isVerifying = false;
                $attemptsRemaining = $this->getAttemptsRemainingProperty();
                $this->status = '[ERROR] Invalid verification code. '.$attemptsRemaining.' attempts remaining.';
                $this->addError('code', 'Invalid code. Please check and try again.');
                $this->code = '';

                return;
            }

            // Success - email verified
            session()->flash('success', 'Email verified successfully. Welcome aboard, '.$user->name.'!');
            $this->code = '';

            // Redirect to dashboard
            $this->isVerifying = false;

            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            $this->status = '[ERROR] Verification failed. Please try again.';
            $this->addError('code', $e->getMessage() ?: 'An error occurred during verification.');
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
                $this->status = '[INFO] Resend available in '.$cooldown.' seconds.';

                return;
            }

            // Generate and send new code
            $emailVerificationService->resendCode($user);
            $this->status = '[SUCCESS] New verification code sent. Check your email.';
            $this->code = '';
        } catch (EmailVerificationException $e) {
            $this->status = '[ERROR] '.$e->getMessage();
            $this->addError('code', $e->getMessage());
        } catch (\Exception $e) {
            $this->status = '[ERROR] Failed to send verification code. Please try again later.';
            $this->addError('code', $e->getMessage() ?: 'An error occurred while sending the code.');
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

    public function render()
    {
        return view('livewire.verify-email');
    }
}
