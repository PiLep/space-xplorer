<?php

namespace App\Livewire;

use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ResetPassword extends Component
{
    public $token = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $status = '';

    public $passwordStrength = '';

    protected $rules = [
        'token' => 'required|string',
        'email' => 'required|email',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'token.required' => 'Le token de réinitialisation est requis.',
        'email.required' => 'L\'adresse email est requise.',
        'email.email' => 'L\'adresse email doit être valide.',
        'password.required' => 'Le mot de passe est requis.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    ];

    public function mount($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function updatedPassword($value)
    {
        $this->calculatePasswordStrength($value);
    }

    protected function calculatePasswordStrength($password)
    {
        $strength = 0;
        $feedback = [];

        if (strlen($password) >= 8) {
            $strength++;
        } else {
            $feedback[] = 'Au moins 8 caractères';
        }

        if (preg_match('/[a-z]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Une lettre minuscule';
        }

        if (preg_match('/[A-Z]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Une lettre majuscule';
        }

        if (preg_match('/[0-9]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Un chiffre';
        }

        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $strength++;
        } else {
            $feedback[] = 'Un caractère spécial';
        }

        if ($strength <= 2) {
            $this->passwordStrength = '[WEAK] Mot de passe faible. Recommandé : '.implode(', ', $feedback);
        } elseif ($strength <= 3) {
            $this->passwordStrength = '[MEDIUM] Mot de passe moyen. Recommandé : '.implode(', ', $feedback);
        } elseif ($strength <= 4) {
            $this->passwordStrength = '[GOOD] Mot de passe correct. Recommandé : '.implode(', ', $feedback);
        } else {
            $this->passwordStrength = '[STRONG] Mot de passe fort.';
        }
    }

    public function resetPassword(PasswordResetService $passwordResetService)
    {
        $this->status = '[PROCESSING] Réinitialisation du mot de passe...';
        $this->validate();

        try {
            $status = $passwordResetService->reset([
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ]);

            if ($status === Password::PASSWORD_RESET) {
                session()->flash('status', __('Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.'));

                return $this->redirect(route('login'), navigate: true);
            } else {
                $this->status = '[ERROR] '.__($status);
                $this->addError('password', __($status));
            }
        } catch (\Exception $e) {
            $this->status = '[ERROR] Une erreur est survenue lors de la réinitialisation. Veuillez réessayer.';
            $this->addError('password', $e->getMessage() ?: 'Une erreur est survenue.');
        }
    }

    public function render()
    {
        return view('livewire.reset-password');
    }
}
