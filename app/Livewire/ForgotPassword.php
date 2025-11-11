<?php

namespace App\Livewire;

use App\Services\PasswordResetService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ForgotPassword extends Component
{
    public $email = '';

    public $status = '';

    protected $rules = [
        'email' => 'required|email',
    ];

    protected $messages = [
        'email.required' => 'L\'adresse email est requise.',
        'email.email' => 'L\'adresse email doit être valide.',
    ];

    public function sendResetLink(PasswordResetService $passwordResetService)
    {
        $this->status = '[PROCESSING] Envoi du lien de réinitialisation...';
        $this->validate();

        try {
            $passwordResetService->sendResetLink($this->email);
            $this->status = '[SUCCESS] Si cet email existe dans notre système, un lien de réinitialisation vous a été envoyé. Vérifiez votre boîte de réception.';
            $this->email = ''; // Clear email for security
        } catch (\Exception $e) {
            $this->status = '[ERROR] Une erreur est survenue. Veuillez réessayer.';
            // Still show success message for security (don't reveal if email exists)
            $this->status = '[SUCCESS] Si cet email existe dans notre système, un lien de réinitialisation vous a été envoyé. Vérifiez votre boîte de réception.';
            $this->email = ''; // Clear email for security
        }
    }

    public function render()
    {
        return view('livewire.forgot-password');
    }
}
