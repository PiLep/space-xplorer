<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public $email = '';

    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    protected $messages = [
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'password.required' => 'Le mot de passe est requis.',
    ];

    public function login(AuthService $authService)
    {
        $this->validate();

        try {
            $authService->loginFromCredentials($this->email, $this->password);

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
            $this->addError('email', $e->getMessage() ?: 'Invalid credentials. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
