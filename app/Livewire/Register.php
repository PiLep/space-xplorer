<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $isSubmitting = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'Le nom est requis.',
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'password.required' => 'Le mot de passe est requis.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'Les mots de passe ne correspondent pas.',
    ];

    public function register(AuthService $authService)
    {
        // Prevent double submission
        if ($this->isSubmitting) {
            return;
        }

        $this->validate();

        $this->isSubmitting = true;

        try {
            $authService->registerFromArray([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            // Redirect to dashboard
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            // Handle other errors
            $this->addError('email', $e->getMessage() ?: 'An error occurred during registration. Please try again.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
